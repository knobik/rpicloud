<?php

namespace App\Services;


use App\Exceptions\PXEException;
use App\Models\Node;
use Symfony\Component\Process\Process;

class PXEService
{
    /**
     * @param  Node  $node
     * @throws PXEException
     */
    public function disableNetboot(Node $node): void
    {
        $process = Process::fromShellCommandline(
            'echo "dhcp-mac=set:tobeignored,'.$node->mac.'" | sudo tee /etc/dnsmasq.d/'.$this->macSlug($node)
        );

        $process->run();

        if (!$process->isSuccessful()) {
            throw new PXEException("PXE Error ({$node->mac}): {$process->getErrorOutput()}");
        }

        $this->restartPxeService();

        $node->netboot = false;
        $node->save();
    }

    /**
     * @param  Node  $node
     * @throws PXEException
     */
    public function enableNetboot(Node $node): void
    {
        $process = Process::fromShellCommandline('sudo rm /etc/dnsmasq.d/'.$this->macSlug($node));
        $process->run();

        if (!$process->isSuccessful()) {
            throw new PXEException("PXE Error ({$node->mac}): {$process->getErrorOutput()}");
        }

        $this->restartPxeService();

        $node->netboot = true;
        $node->save();
    }

    /**
     * @throws PXEException
     */
    public function restartPxeService(): void
    {
        $process = Process::fromShellCommandline('sudo killall dnsmasq');
        $process->run();

        if (!$process->isSuccessful()) {
            throw new PXEException("PXE Error: {$process->getErrorOutput()}");
        }
    }

    /**
     * @param  Node  $node
     * @return string
     */
    public function macSlug(Node $node): string
    {
        return str_replace(':', '-', $node->mac);
    }
}
