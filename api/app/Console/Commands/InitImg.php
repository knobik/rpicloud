<?php

namespace App\Console\Commands;

use App\Console\Commands\traits\SystemProcess;
use App\Exceptions\InitException;
use App\Exceptions\PXEException;
use App\Jobs\Operations\AddSystemUserJob;
use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;

class InitImg extends Command
{
    use SystemProcess;

    public const NFS_BASE_PATH = '/nfs';
    public const TMP_MOUNT_PATH = '/mnt';
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
     * @param string $basePath
     * @return string
     */
    public static function bootPath(string $basePath = self::NFS_BASE_PATH): string
    {
        return $basePath . '/boot';
    }

    /**
     * @param string $basePath
     * @return string
     */
    public static function rootPath(string $basePath = self::NFS_BASE_PATH): string
    {
        return $basePath . '/root';
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     * @throws FileNotFoundException
     * @throws InitException
     * @throws PXEException
     */
    public function handle(): void
    {
        if ($this->hasBaseImage()) {
            $this->info("Base image already exists, skipping...");
            return;
        }

        $imagePath = $this->downloadLatestDistro();

        $this->prepareForUsage($imagePath);

        $this->info('Done initizing netboot image.');
    }

    /**
     * @return string
     */
    public function downloadLatestDistro(): string
    {
        // download the zip file
        $this->info('Downloading ' . static::DISTRO_IMG_URL);
        $zipPath = '/tmp/raspbian.zip';
        $this->process(['curl', '-fSL', static::DISTRO_IMG_URL, '-o', $zipPath], null);

        // extract the zip file
        $this->info('Extracting from ' . $zipPath);
        $unzipPath = '/tmp/raspbian';
        $this->process(['unzip', '-o', $zipPath, '-d', $unzipPath], null);

        $this->info('Cleaning up.');
        unlink($zipPath);

        return head(glob("{$unzipPath}/*.img"));
    }

    /**
     * @param string $imagePath
     * @return void
     * @throws FileNotFoundException
     * @throws InitException
     * @throws PXEException
     */
    private function prepareForUsage(string $imagePath): void
    {
        $this->info('Creating loop device.');
        $device = $this->makeLoopDevice($imagePath);

        if (!$device) {
            throw new PXEException(
                'Cant create a new loop device. Try to reboot the machine. If that does not work, increase the max_loop value in the system.'
            );
        }

        // prepare temp mounting point
        $tmpMountBootPath = static::bootPath(static::TMP_MOUNT_PATH);
        $tmpMountRootPath = static::rootPath(static::TMP_MOUNT_PATH);
        $this->process(['sudo', 'mkdir', '-p', $tmpMountBootPath, $tmpMountRootPath]);

        $partitionMap = [
            ['index' => 1, 'dest' => $tmpMountBootPath],
            ['index' => 2, 'dest' => $tmpMountRootPath],
        ];

        $this->info('Mounting directories.');
        // mount the partitions
        foreach ($partitionMap as $mapped) {
            $partition = $this->partitionPath($device, $mapped['index']);
            if (!$this->partitionIsMounted($partition)) {
                $this->mountPartition($partition, $mapped['dest']);
            }
        }

        // copy the system files
        $this->info('Copying system files.');
        $this->process(
            ['sudo', 'mkdir', '-p', static::bootPath(static::NFS_BASE_PATH), static::rootPath(self::NFS_BASE_PATH)]
        );
        $this->process(['sudo', 'cp', '-a', static::bootPath(static::TMP_MOUNT_PATH) . '/', self::NFS_BASE_PATH]);
        $this->process(['sudo', 'cp', '-a', static::rootPath(static::TMP_MOUNT_PATH) . '/', self::NFS_BASE_PATH]);

        // we are done with the mount, we can unmount the directories and teardown the loop device
        $this->info('Unmounting directories.');
        foreach ($partitionMap as $mapped) {
            $partition = $this->partitionPath($device, $mapped['index']);
            if ($this->partitionIsMounted($partition)) {
                $this->umountPartition($mapped['dest']);
            }
        }
        $this->info('Tearing down loop device.');
        $this->destroyLoopDevice($imagePath, $device);

        // prepare system
        $this->info('Preparing the system.');
        $this->copyFiles();
        $this->addSystemUser();
    }

    /**
     * @throws InitException
     * @throws FileNotFoundException
     */
    private function addSystemUser(): void
    {
        foreach (AddSystemUserJob::commandChain(static::rootPath()) as $command) {
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
        $boot = static::bootPath();
        $root = static::rootPath();

        // setup nfs mounts
        $this->putFile($boot, 'ssh.txt', '', 'root:root');
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
    private function putScript(string $destination, string $filename, string $contents, ?string $owner = null): void
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

        $this->process(['sudo', 'mkdir', '-p', $destination]);
        $this->process(['sudo', 'cp', '-f', $tmpFilename, $fullFilename]);

        if ($owner) {
            $this->process(['sudo', 'chown', $owner, $fullFilename]);
        }

        unlink($tmpFilename);
    }

    /**
     * @param string $filename
     */
    private function makeExecutable(string $filename): void
    {
        $this->process(['sudo', 'chmod', '+x', $filename]);
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

    /**
     * @return bool
     */
    private function hasBaseImage(): bool
    {
        return file_exists(static::rootPath() . '/etc/');
    }

    /**
     * @param string $partition
     * @param string $destination
     */
    private function mountPartition(string $partition, string $destination): void
    {
        $this->process(['sudo', 'mount', $partition, $destination]);
    }

    /**
     * @param string $destination
     */
    private function umountPartition(string $destination): void
    {
        $this->process(['sudo', 'umount', $destination]);
    }

    /**
     * @param string $partition
     * @return bool
     */
    private function partitionIsMounted(string $partition): bool
    {
        $process = new Process(['df']);
        $process->run();

        return strpos($process->getOutput(), $partition) !== false;
    }

    /**
     * @param string $imagePath
     * @return string|null
     */
    private function makeLoopDevice(string $imagePath): ?string
    {
        $this->process(['sudo', 'kpartx', '-v', '-a', $imagePath]);

        return $this->getLoopDevice($imagePath);
    }

    /**
     * @param string $imagePath
     * @param string $device
     * @return void
     */
    private function destroyLoopDevice(string $imagePath, string $device): void
    {
        $this->process(['sudo', 'kpartx', '-d', $imagePath]);
        $this->process(['sudo', 'losetup', '-d', "/dev/$device"]);
    }

    /**
     * @param string $imagePath
     * @return string|null
     */
    private function getLoopDevice(string $imagePath): ?string
    {
        $process = new Process(['sudo', 'losetup']);
        $process->run();

        $device = null;
        // go from bottom to top, we need the latest loop device
        foreach (explode("\n", $process->getOutput()) as $line) {
            if (strpos($line, $imagePath) !== false) {
                $parts = Str::of($line)->replaceMatches('/\s+/', ' ')->explode(' ');
                $device = str_replace('/dev/', '', $parts[0]);
                break;
            }
        }

        return $device;
    }

    /**
     * @param string $device
     * @param int $index
     * @return string
     */
    private function partitionPath(string $device, int $index): string
    {
        return "/dev/mapper/{$device}p{$index}";
    }
}
