<?php


namespace App\Operations;


use App\Jobs\Operations\RebootJob;

class RebootOperation extends BaseOperation
{

    /**
     * @return string
     */
    protected function name(): string
    {
        return 'Reboot node.';
    }

    /**
     * @return void
     */
    protected function build(): void
    {
        $this->addJob(new RebootJob($this->node->id));
    }
}
