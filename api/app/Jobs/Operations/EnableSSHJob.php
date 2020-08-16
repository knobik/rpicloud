<?php

namespace App\Jobs\Operations;

class EnableSSHJob extends BaseOperationJob
{
    /**
     * @var string
     */
    private string $device;

    public function __construct(int $nodeId, string $device)
    {
        parent::__construct($nodeId);

        $this->device = $device;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->track("Enabling SSH.");

        // mount the root partition so we can put files inside
        $partition = $this->getPartitionDevice($this->device, 1);
        $this->execute("sudo mount {$partition} " . static::MOUNT_POINT);

        $this->execute('sudo touch ' . static::MOUNT_POINT . '/ssh');

        $this->execute('sudo umount ' . static::MOUNT_POINT);
    }
}
