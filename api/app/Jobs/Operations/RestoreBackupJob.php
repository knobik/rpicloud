<?php

namespace App\Jobs\Operations;

use App\Models\Backup;

class RestoreBackupJob extends BaseOperationJob
{
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
        $this->track("Restoring backup to {$this->device}");

        $outputFilename = "/backups/{$this->filename}";
        $process = $this->executeAsync("sudo dd if={$outputFilename} of={$this->device} bs=1M status=progress");

        $operation = $this->getOperation();
        foreach ($process as $type => $data) {
            $this->log(trim($data), $operation);
        }
    }
}
