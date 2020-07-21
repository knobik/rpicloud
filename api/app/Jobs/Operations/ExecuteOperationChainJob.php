<?php

namespace App\Jobs\Operations;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ExecuteOperationChainJob  implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

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
     * @param array $operations
     */
    public function __construct(array $operations)
    {
        $this->operations = $operations;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        foreach ($this->operations as $operation) {
            /** @var BaseOperationJob $job */
            $job = new $operation['class'](...$operation['parameters']);
            app(Dispatcher::class)->dispatchNow($job);
        }
    }
}
