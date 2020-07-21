<?php

namespace App\Jobs\Operations;

class RebootJob extends BaseOperationJob
{
    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // wait till node netboots
        $this->track('Rebooting the node.');
        $this->reboot(10);

        // wait till node netboots
        $this->track('Waiting for the node to boot.');
        $this->waitForBoot();
    }
}
