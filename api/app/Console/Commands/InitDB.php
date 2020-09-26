<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class InitDB extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'init:db';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates and seeds the database file.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $dbPath = database_path('database.sqlite');

        if (file_exists($dbPath)) {
            $this->info('Database exists. Skipping.');
            return;
        }

        touch($dbPath);
        $this->call('migrate', ['--force' => true]);
        $this->call('db:seed', ['--class' => 'DatabaseSeeder', '--force' => true]);

        $this->info('Done initizing database.');
    }
}
