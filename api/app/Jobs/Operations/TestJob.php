<?php

namespace App\Jobs\Operations;

use App\Exceptions\PXEException;
use App\Services\PXEService;

class TestJob extends BaseOperationJob
{
    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $process = $this->execute('lsblk --json');
        echo "\n\n" . $process->getOutput() . "\n\n";
    }
}
