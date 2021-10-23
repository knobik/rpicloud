<?php

namespace App\Jobs\Operations;

use App\Http\Controllers\Api\BackupController;
use App\Models\Backup;

class MakeBackupJob extends BaseOperationJob
{
    public const BACKUP_DIRECTORY = '/backups';

    public int $timeout = 0;

    /**
     * @var string
     */
    public string $device;

    /**
     * @var string
     */
    public string $filename;

    /**
     * MakeBackupJob constructor.
     * @param int $nodeId
     * @param string $device
     * @param string $filename
     */
    public function __construct(int $nodeId, string $device, string $filename)
    {
        parent::__construct($nodeId);

        $this->device = $device;
        $this->filename = $filename;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->track("Making backup of {$this->device}");

        $outputFilename = static::BACKUP_DIRECTORY . "/{$this->filename}";
        $process = $this->executeAsync("sudo dd if={$this->device} of={$outputFilename} bs=1M status=progress");

        $operation = $this->getOperation();
        foreach ($process as $type => $data) {
            $this->log(trim($data), $operation);
        }

        $filesize = filesize(BackupController::BACKUPS_DIRECTORY . "/{$this->filename}");

        $this->make($filesize);
    }

    /**
     * @param int $filesize
     * @return Backup
     */
    public function make(int $filesize): Backup
    {
        return Backup::create(
            [
                'node_id' => $this->nodeId,
                'filename' => $this->filename,
                'filesize' => $filesize
            ]
        );
    }
}
