<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ValidateHost extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'validate:host';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Validate if the host ip is the same as last time it was ran.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if (strpos(config('app.url'), 'localhost') !== false) {
            $this->warn('Not initized, skipping.');
            return;
        }

        if (strpos(config('app.url'), hostIp()) !== false) {
            $this->info('Host IP not changed. Skipping.');
            return;
        }

        $this->call('init:host');
        $this->call('init:img', ['--host' => true]);

        $this->info('Done validating host ip.');
    }
}
