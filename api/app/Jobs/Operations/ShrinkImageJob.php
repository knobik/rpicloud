<?php

namespace App\Jobs\Operations;

use App\Http\Controllers\Api\BackupController;
use App\Models\Backup;

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

        $operation = $this->getOperation();
        foreach ($process as $type => $data) {
            $this->log(trim($data), $operation);
        }

        $filesize = filesize(BackupController::BACKUPS_DIRECTORY . "/{$this->filename}");
        Backup::where('filename', '=', $this->filename)
            ->update([
                'filesize' => $filesize
            ]);

    }
}
