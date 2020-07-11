<?php

namespace App\Jobs;


use Symfony\Component\Process\Process;

class GetNodeStatusJob extends BaseSSHJob
{
    public const TIMEOUT = 3;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        $node = $this->getNode();

        $process = $this->getSSH()
            ->execute('hostname');

        $hostname = trim($process->getOutput());
        $node->online = $process->isSuccessful();

        if ($node->online) {
            $node->hostname = $hostname;
            $node->netbooted = $hostname === config('pxe.hostname');
        }

        $node->save();
    }
}
