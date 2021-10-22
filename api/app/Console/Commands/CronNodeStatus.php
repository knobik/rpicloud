<?php

namespace App\Console\Commands;

use App\Jobs\GetNodeHWInfoJob;
use App\Jobs\GetNodeStatusJob;
use App\Models\Node;
use Illuminate\Console\Command;

class CronNodeStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:node-status {--hwinfo}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Adds a job to check the node status for the workers to pickup.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        foreach (Node::all() as $node) {
            if ($this->option('hwinfo')) {
                \Bus::chain([
                    new GetNodeStatusJob($node->id),
                    new GetNodeHWInfoJob($node->id, false)
                ])->dispatch();
            } else {
                GetNodeStatusJob::dispatch($node->id);
            }
        }
    }
}
