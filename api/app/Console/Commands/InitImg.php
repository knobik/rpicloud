<?php

namespace App\Console\Commands;

use App\Exceptions\InitException;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;

class InitImg extends Command
{
    public const DISTRO_STORAGE_FILENAME = '/baseImages/netbootBase.img';
    public const DISTRO_IMG_URL = 'https://downloads.raspberrypi.org/raspios_lite_armhf_latest';

    private const VFAT_INDEX = 1;
    private const EXT4_INDEX = 2;

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
     */
    private function prepareForUsage(): void
    {
        // mount the image
        $this->call('validate:mount');

        $isArm = Str::of($this->getLocalArch())
            ->lower()
            ->startsWith('arm');
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
