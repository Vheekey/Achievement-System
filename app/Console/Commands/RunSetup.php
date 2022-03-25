<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class RunSetup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'run:setup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup Initial database records';

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
     * @return int
     */
    public function handle()
    {
        if(! env('DB_DATABASE')){
            $this->info('Database key absent!');

            return;
        }

        $this->info('Running Migration...');
        Artisan::call('migrate');
        $this->info('Migration Completed!');

        $this->info('Creating Users...');
        Artisan::call('db:seed');
        $this->info('Users Seeded!');

        $this->info('Setup Complete!');
    }
}
