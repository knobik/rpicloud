<?php

namespace App\Jobs\Operations;

class MakeBackupJob extends BaseOperationJob
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
        $this->track("Making backup of {$this->device}");

        $outputFilename = "/backups/{$this->filename}";
        $process = $this->executeAsync("sudo dd if={$this->device} of={$outputFilename} bs=1M status=progress");

        foreach ($process as $type => $data) {
            echo "{$data}\n";
        }
    }
}
