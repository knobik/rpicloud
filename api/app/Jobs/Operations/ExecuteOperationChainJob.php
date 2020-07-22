<?php

namespace App\Jobs\Operations;

use Illuminate\Contracts\Bus\Dispatcher;

class ExecuteOperationChainJob extends BaseOperationJob
{
    /**
     * Dont retry base jobs.
     *
     * @var int
     */
    public int $tries = 1;

    /**
     * @var int
     */
    public int $timeout = 0;

    /**
     * @var array
     */
    private array $operations;

    /**
     * MakeBackupJob constructor.
     * @param int $nodeId
     * @param array $operations
     */
    public function __construct(int $nodeId, array $operations)
    {
        parent::__construct($nodeId);

        $this->operations = $operations;
    }

    /**
     * Execute the job.
     * @throws \Exception
     */
    public function handle(): void
    {
        try {
            foreach ($this->operations as $operation) {
                /** @var BaseOperationJob $job */
                $job = new $operation['class'](...$operation['parameters']);
                app(Dispatcher::class)->dispatchNow($job);
            }
        } catch (\Exception $exception) {
            $this->trackError($exception->getMessage());
        }
    }
}
