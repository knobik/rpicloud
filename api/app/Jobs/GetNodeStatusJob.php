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
            ->execute('whoami');

        if (!$process->isSuccessful()) {
            $node->online = false;
        } else {
            $node->online = true;
        }

        $node->save();
    }
}
