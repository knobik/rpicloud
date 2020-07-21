<?php

namespace App\Jobs\Operations;

class StartOperationJob extends BaseOperationJob
{
    /**
     * Execute the job.
     * @throws \Exception
     */
    public function handle(): void
    {
        $this->startTracking();
    }
}
