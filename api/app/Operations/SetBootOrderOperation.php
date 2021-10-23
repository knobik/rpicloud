<?php


namespace App\Operations;


use App\Jobs\GetNodeHWInfoJob;
use App\Jobs\Operations\RebootJob;
use App\Jobs\Operations\RefreshNodeHWInfoJob;
use App\Jobs\Operations\SetBootOrderJob;
use App\Models\Node;

class SetBootOrderOperation extends BaseOperation
{
    /**
     * @var array
     */
    private array $bootOrder;

    /**
     * @var bool
     */
    private bool $includeDhcpOption;

    /**
     * RestoreOperation constructor.
     * @param Node $node
     * @param array $bootOrder
     * @param bool $includeDhcpOption
     */
    public function __construct(Node $node, array $bootOrder, bool $includeDhcpOption)
    {
        parent::__construct($node);

        $this->bootOrder = $bootOrder;
        $this->includeDhcpOption = $includeDhcpOption;
    }

    /**
     * @return string
     */
    protected function name(): string
    {
        return 'Set boot order';
    }

    /**
     * @return void
     * @throws \ReflectionException
     */
    protected function build(): void
    {
        $this->addJob(new SetBootOrderJob($this->node->id, $this->bootOrder, $this->includeDhcpOption));
        $this->addJob(new RebootJob($this->node->id));
        $this->addJob(new RefreshNodeHWInfoJob($this->node->id));
    }
}
