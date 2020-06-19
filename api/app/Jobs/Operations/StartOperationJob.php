<?php

namespace App\Jobs\Operations;

class StartOperationJob extends BaseOperationJob
{
    private string $name;

    public function __construct(int $nodeId, string $name)
    {
        parent::__construct($nodeId);

        $this->name = $name;
    }

    /**
     * Execute the job.
     * @throws \Exception
     */
    public function handle(): void
    {
        // wait till node netboots
        $this->startTracking($this->name);
    }
}
