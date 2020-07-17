<?php


namespace App\Operations;


use App\Jobs\Operations\AddSystemUserJob;
use App\Jobs\Operations\MakeBackupJob;
use App\Jobs\Operations\NetbootAndWaitJob;
use App\Jobs\Operations\RestoreBackupJob;
use App\Jobs\Operations\SetHostnameJob;
use App\Jobs\Operations\ShrinkImageJob;
use App\Jobs\Operations\StorageBootAndWaitJob;
use App\Models\Node;

class RestoreOperation extends BaseOperation
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
     * @var string|null
     */
    private ?string $hostname;

    /**
     * RestoreOperation constructor.
     * @param Node $node
     * @param string $device
     * @param string $filename
     * @param string|null $hostname
     */
    public function __construct(Node $node, string $device, string $filename, ?string $hostname = null)
    {
        parent::__construct($node);
        $this->device = $device;
        $this->filename = $filename;
        $this->hostname = $hostname;
    }

    /**
     * @return string
     */
    protected function name(): string
    {
        return 'Restore backup.';
    }

    /**
     * @return void
     * @throws \ReflectionException
     */
    protected function build(): void
    {
        $this->addJob(new NetbootAndWaitJob($this->node->id));
        $this->addJob(new RestoreBackupJob($this->node->id, $this->device, $this->filename));
        $this->addJob(new AddSystemUserJob($this->node->id, $this->device));

        if ($this->hostname) {
            $this->addJob(new SetHostnameJob($this->node->id, $this->device, $this->hostname));
        }

        $this->addJob(new StorageBootAndWaitJob($this->node->id));
    }
}
