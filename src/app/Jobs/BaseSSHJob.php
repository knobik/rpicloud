<?php

namespace App\Jobs;

use App\Models\Node;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\Ssh\Ssh;

abstract class BaseSSHJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public const SSH_USER = 'rpi';

    /**
     * @var int
     */
    protected $nodeId;

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
     * @param  int  $nodeId
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
            $this->ssh = Ssh::create(static::SSH_USER, $this->getNode()->ip)
                ->disableStrictHostKeyChecking()
                ->usePrivateKey(storage_path('app/ssh/id_rsa'));
        }

        return $this->ssh;
    }
}
