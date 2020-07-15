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

            $disks = $this->getDisks();
            if ($disks !== null) {
                $node->storage_devices = $disks;
            }
        }

        $node->save();
    }

    /**
     * @return array|null
     */
    private function getDisks(): ?array
    {
        $process = $this->getSSH()
            ->execute('lsblk --json');

        $disks = null;
        if ($process->isSuccessful()) {
            $disks = collect(json_decode($process->getOutput(), true)['blockdevices'])
                ->filter(
                    function ($device) {
                        return strtolower($device['type']) === 'disk';
                    }
                )->map(
                    function ($device) {
                        return [
                            'path' => '/dev/' . $device['name'],
                            'size' => $device['size'],
                            'read_only' => (bool)$device['ro']
                        ];
                    }
                )->all();
        }

        return $disks;
    }
}
