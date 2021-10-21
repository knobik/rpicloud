<?php

namespace App\Jobs;

use App\Exceptions\OperationException;
use App\Exceptions\SSHException;
use App\Models\Node;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use romanzipp\QueueMonitor\Traits\IsMonitored;
use Spatie\Ssh\Ssh;
use Symfony\Component\Process\Process;

abstract class BaseSSHJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use IsMonitored;

    /**
     * Dont retry base jobs.
     *
     * @var int
     */
    public int $tries = 1;

    /**
     * @var int
     */
    protected int $nodeId;

    /**
     * @var Node|null
     */
    private ?Node $node = null;

    /**
     * @var Ssh|null
     */
    private ?Ssh $ssh = null;

    /**
     * Create a new job instance.
     *
     * @param int $nodeId
     */
    public function __construct(int $nodeId)
    {
        $this->nodeId = $nodeId;
    }

    /**
     * @return Node
     */
    protected function getNode(): Node
    {
        if (!$this->node) {
            $this->node = Node::find($this->nodeId);
        }

        return $this->node;
    }

    /**
     * @return Ssh
     */
    protected function getSSH(): Ssh
    {
        if (!$this->ssh) {
            $this->ssh = Ssh::create(config('pxe.user'), $this->getNode()->ip)
                ->disableStrictHostKeyChecking()
                ->usePrivateKey(storage_path('app/ssh/id_rsa'));
        }

        return $this->ssh;
    }

    /**
     * @param string|array $command
     * @return Process
     */
    protected function execute($command): Process
    {
        $process = $this->getSSH()->execute($command);

        $node = $this->getNode();
        if ($process->isSuccessful() && !$node->online) {
            $node->online = true;
            $node->save();
        }

        return $process;
    }

    /**
     * @param $command
     * @return Process
     */
    protected function executeAsync($command): Process
    {
        return $this->getSSH()->executeAsync($command);
    }

    /**
     * @param string $command
     * @return Process
     * @throws SSHException
     */
    protected function executeOrFail(string $command): Process
    {
        $process = $this->execute($command);
        if (!$process->isSuccessful()) {
            $this->failWithMessage($process->getErrorOutput());
        }

        return $process;
    }

    /**
     * @param string $message
     * @throws SSHException
     */
    protected function failWithMessage(string $message): void
    {
        throw new SSHException(
            "SSH ERROR ({$this->getNode()->ip}): {$message}"
        );
    }
}
