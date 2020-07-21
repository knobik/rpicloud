<?php

namespace App\Console\Commands;

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
    protected $signature = 'cron:node-status';

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
            GetNodeStatusJob::dispatch($node->id);
        }
    }
}
