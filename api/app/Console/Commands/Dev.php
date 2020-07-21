<?php

namespace App\Console\Commands;

use App\Jobs\GetNodeHWInfoJob;
use App\Jobs\GetNodeStatusJob;
use App\Jobs\Operations\FinishOperationJob;
use App\Jobs\Operations\MakeBackupJob;
use App\Jobs\Operations\NetbootAndWaitJob;
use App\Jobs\Operations\SetHostnameJob;
use App\Jobs\Operations\ShrinkImageJob;
use App\Jobs\Operations\StartOperationJob;
use App\Jobs\Operations\RebootJob;
use App\Jobs\Operations\TestJob;
use App\Jobs\PrepareBaseImage;
use App\Models\Node;
use App\Operations\BackupOperation;
use App\Operations\RebootOperation;
use App\Operations\TestOperation;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
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
//        SetHostnameJob::dispatchNow(9, '/dev/mmcblk0', 'testtt');
//        (new BackupOperation(Node::findOrFail(5), '/dev/mmcblk0', now()->format('y-m-d_h-i-s') . '.img'))->dispatch();
    }
}
