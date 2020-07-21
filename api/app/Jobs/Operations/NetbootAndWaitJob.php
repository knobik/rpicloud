<?php

namespace App\Jobs\Operations;

use App\Exceptions\PXEException;
use App\Services\PXEService;

class NetbootAndWaitJob extends BaseOperationJob
{
    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public int $timeout = 500;

    /**
     * Execute the job.
     * @param PXEService $PXEService
     * @throws PXEException
     */
    public function handle(PXEService $PXEService): void
    {
        $node = $this->getNode();

        // enable pxe boot
        $PXEService->enableNetboot($node);
        sleep(5); // wait for the dnsmasq to restart

        // reboot the node
        $this->track('Rebooting the node.');
        $this->reboot(10);

        // wait till node netboots
        $this->track('Waiting for the node to netboot.');
        $this->waitForBoot(config('pxe.hostname'));
    }
}
