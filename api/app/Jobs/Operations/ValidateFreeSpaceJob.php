<?php

namespace App\Jobs\Operations;

use App\Exceptions\SSHException;
use App\Http\Controllers\Api\BackupController;
use Symfony\Component\Process\Process;

class ValidateFreeSpaceJob extends BaseOperationJob
{
    /**
     * @var string
     */
    public string $device;

    /**
     * MakeBackupJob constructor.
     * @param int $nodeId
     * @param string $device
     */
    public function __construct(int $nodeId, string $device)
    {
        parent::__construct($nodeId);

        $this->device = $device;
    }

    /**
     * Execute the job.
     * @throws SSHException
     */
    public function handle(): void
    {
        $this->track("Validating free space...");

        $hostAvailableSize = $this->getHostAvailableSize();
        $nodeDeviceSize = $this->getNodeDeviceSize($this->device);

        if ($hostAvailableSize < $nodeDeviceSize) {
            throw new SSHException("Host does not have enough space for this node backup. Host: {$hostAvailableSize}G, Node: {$nodeDeviceSize}G");
        }
    }

    /**
     * @param string $device
     * @return float
     */
    private function getNodeDeviceSize(string $device): float
    {
        // normalize the device
        $device = str_replace('/dev/', '', $device);
        $process = $this->execute('lsblk --json');

        $storageDevice = collect(json_decode($process->getOutput(), true)['blockdevices'])
            ->first(fn($item) => $item['name'] === $device);

        return (float)$this->normalizeSize($storageDevice['size']);
    }

    /**
     * @return float
     */
    private function getHostAvailableSize(): float
    {
        $backupDir = BackupController::BACKUPS_DIRECTORY;
        $process = Process::fromShellCommandline("df -Ph {$backupDir} | tail -1 | awk '{print $4}'");
        $process->run();

        return (float)$this->normalizeSize($process->getOutput());
    }

    /**
     * @param string $value
     * @return string
     */
    private function normalizeSize(string $value)
    {
        return str_replace('G', '', trim($value));
    }
}
