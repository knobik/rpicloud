<?php

namespace App\Console\Commands;

use App\Exceptions\InitException;
use App\Jobs\Operations\AddSystemUserJob;
use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;

class InitImg extends Command
{
    public const DISTRO_STORAGE_FILENAME = '/baseImages/netbootBase.img';
    public const DISTRO_IMG_URL = 'https://downloads.raspberrypi.org/raspios_lite_armhf_latest';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'init:img {--host}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Downloads and prepares the base netboot img.';

    /**
     * Execute the console command.
     *
     * @return mixed
     * @throws FileNotFoundException
     * @throws InitException
     */
    public function handle()
    {
        if (file_exists(static::DISTRO_STORAGE_FILENAME)) {
            $this->info("Base image already exists, skipping.");
            return;
        }

        if (!$this->option('host')) {
            $this->downloadLatestDistro();
        }

        $this->prepareForUsage();

        $this->info('Done initizing netboot img.');
    }

    /**
     * @return void
     */
    public function downloadLatestDistro(): void
    {
        // download the zip file
        $this->info('Downloading ' . static::DISTRO_IMG_URL);
        $zipPath = '/tmp/raspbian.zip';
        (new Process(['curl', '-fSL', static::DISTRO_IMG_URL, '-o', $zipPath]))->setTimeout(null)->run();

        // extract the zip file
        $this->info('Extracting from ' . $zipPath);
        $unzipPath = '/tmp/raspbian';
        (new Process(['unzip', '-o', $zipPath, '-d', $unzipPath]))->setTimeout(null)->run();

        // move the file
        $imgPath = head(glob("{$unzipPath}/*.img"));
        $this->info('Moving ' . $imgPath);
        rename($imgPath, static::DISTRO_STORAGE_FILENAME);

        $this->info('Cleaning up...');
        unlink($zipPath);
    }

    /**
     * @return void
     * @throws FileNotFoundException
     * @throws InitException
     */
    private function prepareForUsage(): void
    {
        if ($this->option('host') && !ValidateMount::hasBaseImage()) {
            $this->info('No base image present. Skipping img preparation.');
            return;
        }

        // mount the image
        $this->call('validate:mount');

        $this->copyFiles();

        if (!$this->option('host')) {
            $this->addSystemUser();
        }
    }

    /**
     * @throws InitException
     * @throws FileNotFoundException
     */
    private function addSystemUser(): void
    {
        foreach (AddSystemUserJob::commandChain(ValidateMount::MOUNT_ROOT) as $command) {
            $process = Process::fromShellCommandline($command);
            $process->run();

            if (!$process->isSuccessful()) {
                throw new InitException(
                    'Error while running command: ' . $command . "\n\n" . $process->getErrorOutput()
                );
            }
        }
    }

    /**
     * @return void
     */
    private function copyFiles(): void
    {
        $boot = ValidateMount::MOUNT_BOOT;
        $root = ValidateMount::MOUNT_ROOT;

        // setup nfs mounts
        $this->putFile($boot, 'cmdline.txt', $this->getStub('pxe/cmdline.txt'), 'root:root');
        $this->putFile("{$root}/etc", 'fstab', $this->getStub('pxe/fstab'), 'root:root');

        // setup hostname
        $this->putFile("{$root}/etc", 'hostname', $this->getStub('hostname/hostname'), 'root:root');
        $this->putFile("{$root}/etc", 'hosts', $this->getStub('hostname/hosts'), 'root:root');

        // init script
        $this->putScript("{$root}/etc", 'rc.local', $this->getStub('pxe/rc.local'), 'root:root');
        $this->putScript("{$root}/scripts", 'init.sh', $this->getStub('pxe/init.sh'));

        // helper scripts
        $this->putScript("{$root}/scripts", 'pishrink.sh', $this->getStub('pxe/pishrink.sh'));
    }

    /**
     * @param string $destination
     * @param string $filename
     * @param string $contents
     * @param string|null $owner
     */
    private function putScript(string $destination, string $filename, string $contents, ?string $owner = null)
    {
        $this->putFile($destination, $filename, $contents, $owner);
        $this->makeExecutable(rtrim($destination, '/') . '/' . $filename);
    }

    /**
     * @param string $destination
     * @param string $filename
     * @param string $contents
     * @param string|null $owner
     * @return void
     */
    private function putFile(string $destination, string $filename, string $contents, ?string $owner = null): void
    {
        $tmpFilename = '/tmp/' . Str::random(16);
        $fullFilename = rtrim($destination, '/') . '/' . $filename;
        file_put_contents($tmpFilename, $contents);

        (new Process(['sudo', 'mkdir', '-p', $destination]))->run();
        (new Process(['sudo', 'cp', '-f', $tmpFilename, $fullFilename]))->run();

        if ($owner) {
            (new Process(['sudo', 'chown', $owner, $fullFilename]))->run();
        }

        unlink($tmpFilename);
    }

    /**
     * @param string $filename
     */
    private function makeExecutable(string $filename)
    {
        (new Process(['sudo', 'chmod', '+x', $filename]))->run();
    }

    private function getStub(string $name): string
    {
        return $this->fillParameters(file_get_contents(base_path("stubs/{$name}")));
    }

    /**
     * @param string $content
     * @return string
     */
    private function fillParameters(string $content): string
    {
        $config = Arr::dot(config('pxe'));
        $keys = collect(array_keys($config))->map(fn($value) => "__config.{$value}__")->all();
        $values = array_values($config);

        $keys[] = '__ip__';
        $values[] = hostIp();

        return str_replace($keys, $values, $content);
    }
}
