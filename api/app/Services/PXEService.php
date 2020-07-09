<?php

namespace App\Services;


use App\Exceptions\PXEException;
use App\Models\Node;
use Symfony\Component\Process\Process;

class PXEService
{
    /**
     * @param  Node  $node
     */
    public function disableNetboot(Node $node): void
    {
        $this->processNetboot(
            $node,
            Process::fromShellCommandline('sudo rm /etc/dnsmasq.d/'.$this->macSlug($node).'.conf'),
            true
        );
    }

    /**
     * @param  Node  $node
     */
    public function enableNetboot(Node $node): void
    {
        $this->processNetboot(
            $node,
            Process::fromShellCommandline('echo "dhcp-mac=set:whitelisted,'.$node->mac.'" | sudo tee /etc/dnsmasq.d/'.$this->macSlug($node).'.conf'),
            false
        );
    }

    /**
     * @return void
     */
    public function restartPxeService(): void
    {
        Process::fromShellCommandline('sudo killall dnsmasq')->run();
    }

    /**
     * @param  Node  $node
     * @return string
     */
    public function macSlug(Node $node): string
    {
        return str_replace(':', '-', $node->mac);
    }

    /**
     * @param  Node  $node
     * @param  Process  $process
     * @param  bool  $value
     */
    private function processNetboot(Node $node, Process $process, bool $value): void
    {
        $process->run();
        $this->restartPxeService();

        $node->netboot = $value;
        $node->save();
    }
}
