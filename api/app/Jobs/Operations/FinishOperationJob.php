<?php

namespace App\Jobs\Operations;

use App\Exceptions\PXEException;
use App\Services\PXEService;

class FinishOperationJob extends BaseOperationJob
{
    /**
     * Execute the job.
     * @throws \Exception
     */
    public function handle(): void
    {
        // wait till node netboots
        $this->track('Operation finished.');
        $this->endTracking();
    }
}
