<?php

namespace App\Jobs\Operations;

use App\Jobs\BaseSSHJob;
use App\Jobs\Traits\TrackStatus;

abstract class BaseOperationJob extends BaseSSHJob
{
    use TrackStatus;

    /**
     * Reboot the node
     *
     * @param int|null $sleepTime
     * @return void
     */
    protected function reboot(?int $sleepTime = null): void
    {
        // if the node is online, try to reboot it.
        $process = $this->execute('sudo reboot');
        if ($process->isSuccessful()) {
            $node = $this->getNode();
            $node->online = false;
            $node->save();

            if ($sleepTime) {
                sleep($sleepTime);
            }
        }
    }

    /**
     * @return void
     */
    protected function waitForBoot(): void
    {
        while (true) {
            sleep(5);

            $process = $this->execute('whoami');
            if ($process->isSuccessful()) {
                break;
            }
        }
    }

}
