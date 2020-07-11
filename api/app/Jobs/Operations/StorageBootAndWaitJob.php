<?php

namespace App\Jobs\Operations;

use App\Exceptions\PXEException;
use App\Services\PXEService;

class StorageBootAndWaitJob extends BaseOperationJob
{
    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 500;

    /**
     * Execute the job.
     * @param PXEService $PXEService
     */
    public function handle(PXEService $PXEService): void
    {
        $node = $this->getNode();

        // enable pxe boot
        $PXEService->disableNetboot($node);

        // reboot the node
        $this->track('Trying to reboot the node.');
        $this->reboot(10);

        // wait till node netboots
        $this->track('Waiting for the node to storage boot.');
        $this->waitForBoot();
    }
}
