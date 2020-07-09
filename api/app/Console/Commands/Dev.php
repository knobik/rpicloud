<?php

namespace App\Console\Commands;

use App\Jobs\GetNodeHWInfoJob;
use App\Jobs\GetNodeStatusJob;
use App\Jobs\Operations\FinishOperationJob;
use App\Jobs\Operations\NetbootAndWaitJob;
use App\Jobs\Operations\StartOperationJob;
use App\Jobs\Operations\StorageBootAndWaitJob;
use App\Jobs\Operations\TestJob;
use App\Jobs\PrepareBaseImage;
use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class Dev extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dev';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $nodeId = 3;
        StartOperationJob::withChain([
            new NetbootAndWaitJob($nodeId),
            new TestJob($nodeId),
            new StorageBootAndWaitJob($nodeId),
            new FinishOperationJob($nodeId)
        ])
            ->dispatch($nodeId, 'Test job chaining')
            ->allOnQueue('operations');

//        dd(GetNodeStatusJob::dispatchNow(2));

//        dd(TestJob::dispatchNow(2));

    }
}
