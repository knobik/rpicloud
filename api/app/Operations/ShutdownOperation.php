<?php


namespace App\Operations;


use App\Jobs\Operations\ShutdownJob;

class ShutdownOperation extends BaseOperation
{

    /**
     * @return string
     */
    protected function name(): string
    {
        return 'Shutdown node';
    }

    /**
     * @return void
     * @throws \ReflectionException
     */
    protected function build(): void
    {
        $this->addJob(new ShutdownJob($this->node->id));
    }
}
