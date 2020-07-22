<?php


namespace App\Operations;


use App\Jobs\BaseSSHJob;
use App\Jobs\Operations\ExecuteOperationChainJob;
use App\Jobs\Operations\FinishOperationJob;
use App\Jobs\Operations\StartOperationJob;
use App\Models\Node;
use App\Models\Operation;

abstract class BaseOperation
{
    public const QUEUE = 'operations';

    /**
     * @var array
     */
    protected array $chain = [];

    /**
     * @var Node
     */
    protected Node $node;

    /**
     * BaseOperation constructor.
     * @param Node $node
     *
     * @return void
     */
    public function __construct(Node $node)
    {
        $this->node = $node;
    }

    /**
     * @param string $className
     * @param array $parameters
     * @return void
     */
    public function addJobByClassname(string $className, array $parameters)
    {
        $this->chain[] = [
            'class' => $className,
            'parameters' => $parameters
        ];
    }

    /**
     * @param BaseSSHJob $job
     * @throws \ReflectionException
     */
    public function addJob(BaseSSHJob $job)
    {
        $reflection = new \ReflectionClass($job);

        $parameters = [];
        foreach ($reflection->getConstructor()->getParameters() as $parameter) {
            $property = $reflection->getProperty($parameter->name);
            $property->setAccessible(true);
            $parameters[] = $property->getValue($job);
        }

        $this->addJobByClassname($reflection->name, $parameters);
    }

    /**
     * @return void
     */
    public function dispatch(): void
    {
        $this->compile();
        $this->createOperation($this->name());

        ExecuteOperationChainJob::dispatch($this->node->id, $this->chain)
            ->onQueue(static::QUEUE);
    }

    /**
     * @return void
     */
    public function dispatchNow(): void
    {
        $this->compile();
        $this->createOperation($this->name());

        ExecuteOperationChainJob::dispatchNow($this->node->id, $this->chain);
    }

    /**
     * @return void
     */
    private function compile(): void
    {
        $this->addJobByClassname(StartOperationJob::class, [$this->node->id]);
        $this->build();
        $this->addJobByClassname(FinishOperationJob::class, [$this->node->id]);
    }

    /**
     * @param $name
     * @return Operation
     */
    private function createOperation($name): Operation
    {
        return Operation::create(
            [
                'name' => $name,
                'description' => 'Waiting in queue.',
                'node_id' => $this->node->id
            ]
        );
    }

    /**
     * @return string
     */
    abstract protected function name(): string;

    /**
     * @return void
     */
    abstract protected function build(): void;
}
