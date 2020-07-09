<?php

namespace App\Console\Commands;

use App\Jobs\GetNodeHWInfoJob;
use App\Jobs\GetNodeStatusJob;
use App\Jobs\Operations\FinishOperationJob;
use App\Jobs\Operations\MakeBackupJob;
use App\Jobs\Operations\NetbootAndWaitJob;
use App\Jobs\Operations\ShrinkImageJob;
use App\Jobs\Operations\StartOperationJob;
use App\Jobs\Operations\StorageBootAndWaitJob;
use App\Jobs\Operations\TestJob;
use App\Jobs\PrepareBaseImage;
use App\Models\Node;
use App\Operations\BackupOperation;
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
        $node = Node::findOrFail(4);
        $device = '/dev/mmcblk0';
        $filename = now()->format('y-m-d_h-i-s') . '.img';

        (new BackupOperation($node, $device, $filename))->dispatch();
    }
}
