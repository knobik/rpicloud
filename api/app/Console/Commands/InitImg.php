<?php

namespace App\Console\Commands;

use App\Exceptions\ChrootException;
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
     * @var string|null
     */
    protected ?string $hostArchitecture = null;

    /**
     * Execute the console command.
     *
     * @return mixed
     * @throws FileNotFoundException
     */
    public function handle()
    {
//        $this->downloadLatestDistro();
        $this->prepareForUsage();

        $this->info('Done.');
    }

    /**
     * @return void
     */
    public function downloadLatestDistro(): void
    {
        // download the zip file
        $this->info('Downloading '.static::DISTRO_IMG_URL);
        $zipPath = '/tmp/raspbian.zip';
        (new Process(['curl', '-fSL', static::DISTRO_IMG_URL, '-o', $zipPath]))->run();

        // extract the zip file
        $this->info('Extracting from '.$zipPath);
        $unzipPath = '/tmp/raspbian';
        (new Process(['unzip', '-o', $zipPath, '-d', $unzipPath]))->run();

        // move the file
        $imgPath = head(glob("{$unzipPath}/*.img"));
        $this->info('Moving '.$imgPath);
        rename($imgPath, static::DISTRO_STORAGE_FILENAME);

        $this->info('Cleaning up...');
        unlink($zipPath);
    }

    /**
     * @return void
     * @throws FileNotFoundException
     */
    private function prepareForUsage(): void
    {
        // mount the image
        $this->call('validate:mount');

        $this->copyFiles();
        $this->addSystemUser();
//        $this->enableSSH();
    }

    /**
     * @return bool
     */
    private function needsEmulation(): bool
    {
        if ($this->hostArchitecture === null) {
            $this->hostArchitecture = $this->getLocalArch();
        }

        return Str::of($this->hostArchitecture)
                ->lower()
                ->startsWith('arm') === false;
    }

    private function addSystemUser(): void
    {
        //useradd rpi
        //mkdir -p /home/rpi/.ssh
        //chown -R rpi:rpi /home/rpi
        //adduser rpi sudo
        //echo 'rpi ALL=(ALL) NOPASSWD:ALL' >> /etc/sudoers
        //
        //PASSWORD=$(openssl passwd -1 __PASSWORD__)
        //usermod --password ${PASSWORD} rpi
    }

    /**
     * @return void
     * @throws FileNotFoundException
     */
    private function copyFiles(): void
    {
        $this->putFile(ValidateMount::MOUNT_BOOT.'/cmdline.txt', $this->getStub('cmdline.txt'), 'root:root');
        $this->putFile(ValidateMount::MOUNT_ROOT.'/etc/fstab', $this->getStub('fstab'), 'root:root');
        $this->putFile(
            ValidateMount::MOUNT_ROOT.'/home/rpi/.ssh/authorized_keys',
            \Storage::disk('local')->get(InitSSH::RSA_PUBLIC)
        );

        if ($this->needsEmulation()) {
            Process::fromShellCommandline(
                'sudo cp $(which qemu-arm-static) '.ValidateMount::MOUNT_ROOT.'/$(which qemu-arm-static)'
            )->run();
        }
    }

    /**
     * @param  string  $destination
     * @param  string  $contents
     * @param  string|null  $owner
     * @return void
     */
    private function putFile(string $destination, string $contents, ?string $owner = null): void
    {
        $filename = '/tmp/'.Str::random(16);
        file_put_contents($filename, $contents);

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
     * @param  string  $content
     * @return string
     */
    private function fillParameters(string $content): string
    {
        return str_replace(['__IP__'], [hostIp()], $content);
    }

    /**
     * @return string
     */
    private function getLocalArch(): string
    {
        $process = new Process(['uname', '--m']);
        $process->run();

        return trim($process->getOutput());
    }

}
