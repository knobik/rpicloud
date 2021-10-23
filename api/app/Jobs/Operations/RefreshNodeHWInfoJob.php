<?php

namespace App\Jobs\Operations;

use App\Jobs\GetNodeHWInfoJob;

class RefreshNodeHWInfoJob extends BaseOperationJob
{
    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->track("Refreshing hardware info.");

        GetNodeHWInfoJob::dispatchSync($this->nodeId, false);
    }
}
