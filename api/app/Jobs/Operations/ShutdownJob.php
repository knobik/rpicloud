<?php

namespace App\Jobs\Operations;

class ShutdownJob extends BaseOperationJob
{
    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->track('Shutdown the node.');
        $this->shutdown();
    }
}
