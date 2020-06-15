<?php

namespace App\Console\Commands;

use App\Exceptions\PXEException;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;

class ValidateMount extends Command
{

    public const MOUNT_BOOT = '/nfs/boot';
    public const MOUNT_ROOT = '/nfs/root';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'validate:mount';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Validate if the base image is mounted, if not mount it.';

    /**
     * Execute the console command.
     *
     * @return mixed
     * @throws PXEException
     */
    public function handle()
    {
        if (!$this->hasBaseImage()) {
            $this->error('No base image present. Skipping.');
            return;
        }

        if (!$this->hasLoopDevice()) {
            $this->warn("Loop device not available. Creating...");
            $this->makeLoopDevice();
        }

        $device = $this->getLoopDevice();
        if (!$device) {
            throw new PXEException('Cant create a new loop device. Try to reboot the machine. If that does not work, increase the max_loop value in the system.');
        }

        $partitionMap = [
            ['index' => 1, 'dest' => static::MOUNT_BOOT],
            ['index' => 2, 'dest' => static::MOUNT_ROOT]
        ];

        foreach ($partitionMap as $mapped) {
            $partition = $this->partitionPath($device, $mapped['index']);
            if (!$this->partitionIsMounted($partition)) {
                $this->warn("Partition $partition not mounted. Mounting...");
                $this->mountPartition($partition, $mapped['dest']);
            }
        }

        $this->info('Done validating mount.');
    }

    /**
     * @param  string  $partition
     * @param  string  $destination
     */
    private function mountPartition(string $partition, string $destination): void
    {
        (new Process(['sudo', 'mount', $partition, $destination]))->run();
    }

    /**
     * @param  string  $partition
     * @return bool
     */
    private function partitionIsMounted(string $partition): bool
    {
        $process = new Process(['df']);
        $process->run();

        return strpos($process->getOutput(), $partition) !== false;
    }

    /**
     * @return void
     */
    private function makeLoopDevice(): void
    {
        $path = '/nfs';
        $bootPath = $path.'/boot';
        $rootPath = $path.'/root';

        (new Process(['sudo', 'mkdir', '-p', $bootPath, $rootPath]))->run();
        (new Process(['sudo', 'kpartx', '-v', '-a', InitImg::DISTRO_STORAGE_FILENAME]))->run();
    }

    /**
     * @return bool
     */
    private function hasLoopDevice(): bool
    {
        $process = new Process(['sudo', 'losetup']);
        $process->run();

        return strpos($process->getOutput(), InitImg::DISTRO_STORAGE_FILENAME) !== false;
    }

    /**
     * @return string|null
     */
    private function getLoopDevice(): ?string
    {
        $process = new Process(['sudo', 'losetup']);
        $process->run();

        $device = null;
        foreach (explode("\n", $process->getOutput()) as $line) {
            if (strpos($line, InitImg::DISTRO_STORAGE_FILENAME) !== false) {
                $parts = Str::of($line)->replaceMatches('/\s+/', ' ')->explode(' ');
                $device = str_replace('/dev/', '', $parts[0]);
                break;
            }
        }

        return $device;
    }

    /**
     * @param  string  $device
     * @param  int  $index
     * @return string
     */
    private function partitionPath(string $device, int $index): string
    {
        return "/dev/mapper/{$device}p{$index}";
    }

    /**
     * @return bool
     */
    private function hasBaseImage(): bool
    {
        return file_exists(InitImg::DISTRO_STORAGE_FILENAME);
    }

}
