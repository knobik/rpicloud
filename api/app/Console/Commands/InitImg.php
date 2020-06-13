<?php

namespace App\Console\Commands;

use App\Exceptions\InitException;
use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
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
    protected $signature = 'init:img';

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
        $this->downloadLatestDistro();
        $this->prepareForUsage();

        $this->info('Done.');
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
        // mount the image
        $this->call('validate:mount');

        $this->copyFiles();
        $this->addSystemUser();
    }

    /**
     * @throws InitException
     */
    private function addSystemUser(): void
    {
        $path = ValidateMount::MOUNT_ROOT;
        $password = bcrypt(Str::random(32));
        $commands = [
            // add a user
            "echo 'rpi:x:1001:1001:,,,:/home/rpi:/bin/bash' | sudo tee -a {$path}/etc/passwd",
            "echo 'rpi:x:1001:' | sudo tee -a {$path}/etc/group",
            "sudo chown -R 1001:1001 {$path}/home/rpi",
            "echo 'rpi:{$password}:18409:0:99999:7:::' | sudo tee -a {$path}/etc/shadow",

            // add user to sudo
            "sudo sed -i 's/sudo:x:27:pi/sudo:x:27:pi,rpi/g' {$path}/etc/group",
            "echo 'rpi ALL=(ALL) NOPASSWD:ALL' | sudo tee -a {$path}/etc/sudoers"
        ];

        foreach ($commands as $command) {
            $process = Process::fromShellCommandline($command);
            $process->run();

            if (!$process->isSuccessful()) {
                throw new InitException('Error while running command: ' . $command . "\n\n" . $process->getErrorOutput());
            }
        }
    }

    /**
     * @return void
     * @throws FileNotFoundException
     */
    private function copyFiles(): void
    {
        $this->putFile(ValidateMount::MOUNT_BOOT . '/cmdline.txt', $this->getStub('cmdline.txt'), 'root:root');
        $this->putFile(ValidateMount::MOUNT_BOOT . '/ssh', '');

        $this->putFile(ValidateMount::MOUNT_ROOT . '/etc/fstab', $this->getStub('fstab'), 'root:root');
        $this->putFile(
            ValidateMount::MOUNT_ROOT . '/home/rpi/.ssh/authorized_keys',
            \Storage::disk('local')->get(InitSSH::RSA_PUBLIC)
        );
    }

    /**
     * @param string $destination
     * @param string $contents
     * @param string|null $owner
     * @return void
     */
    private function putFile(string $destination, string $contents, ?string $owner = null): void
    {
        $filename = '/tmp/' . Str::random(16);
        file_put_contents($filename, $contents);

        (new Process(['sudo', 'mkdir', '-p', $destination]))->run();
        (new Process(['sudo', 'cp', '-f', $filename, $destination]))->run();

        if ($owner) {
            (new Process(['sudo', 'chown', $owner, $destination]))->run();
        }
    }

    private function getStub(string $name): string
    {
        return $this->fillParameters(file_get_contents(base_path("stubs/pxe/{$name}")));
    }

    /**
     * @param string $content
     * @return string
     */
    private function fillParameters(string $content): string
    {
        return str_replace(['__IP__'], [hostIp()], $content);
    }
}
