<?php

namespace App\Jobs\Operations;

class ShrinkImageJob extends BaseOperationJob
{
    public int $timeout = 0;

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
    public function __construct(int $nodeId, string $filename)
    {
        parent::__construct($nodeId);

        $this->filename = $filename;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->track("Shrinking backup {$this->filename}");

        $outputFilename = "/backups/{$this->filename}";
        $process = $this->executeAsync("sudo /scripts/pishrink.sh {$outputFilename}");

        foreach ($process as $type => $data) {
            echo "{$data}";
        }
    }
}
