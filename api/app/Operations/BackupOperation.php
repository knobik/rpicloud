<?php


namespace App\Operations;


use App\Jobs\Operations\MakeBackupJob;
use App\Jobs\Operations\NetbootAndWaitJob;
use App\Jobs\Operations\ShrinkImageJob;
use App\Jobs\Operations\RebootJob;
use App\Jobs\Operations\StorageBootAndWaitJob;
use App\Jobs\Operations\ValidateFreeSpaceJob;
use App\Models\Node;

class BackupOperation extends BaseOperation
{
    /**
     * @var string
     */
    private string $device;

    /**
     * @var string
     */
    private string $filename;

    /**
     * @var bool
     */
    private bool $shrink;

    public function __construct(Node $node, string $device, string $filename, bool $shrink = true)
    {
        parent::__construct($node);
        $this->device = $device;
        $this->filename = $filename;
        $this->shrink = $shrink;
    }

    /**
     * @return string
     */
    protected function name(): string
    {
        return 'Make backup.';
    }

    /**
     * @return void
     * @throws \ReflectionException
     */
    protected function build(): void
    {
        $this->addJob(new ValidateFreeSpaceJob($this->node->id, $this->device));
        $this->addJob(new NetbootAndWaitJob($this->node->id));
        $this->addJob(new MakeBackupJob($this->node->id, $this->device, $this->filename));

        if ($this->shrink) {
            $this->addJob(new ShrinkImageJob($this->node->id, $this->filename));
        }

        $this->addJob(new StorageBootAndWaitJob($this->node->id));
    }
}
