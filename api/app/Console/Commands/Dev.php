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
use App\Jobs\Operations\ValidateFreeSpaceJob;
use App\Jobs\PrepareBaseImage;
use App\Operations\TestOperation;
use Illuminate\Console\Command;

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
        dd(
            collect(["12", '13', '11', '5', '15', '7', 3, 17])->max()
        );
//        ValidateFreeSpaceJob::dispatchNow(13, '/dev/mmcblk0');
    }
}
