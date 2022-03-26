<?php

namespace App\Console\Commands;

use App\Models\Achievement;
use App\Traits\Caches;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

class RunSetup extends Command
{
    use Caches;

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

        if(! env('CACHE_DRIVER') || ! in_array(env('CACHE_DRIVER'), ['database', 'redis']))
        {
            $this->info('Cache driver key must be set to database or redis');

            return;
        }

        $this->info('Running Migration...');
        Artisan::call('migrate');
        $this->info('Migration Completed!');

        $this->info('Seeding DB...');
        Artisan::call('db:seed');
        $this->info('DB Seeded!');

        self::putInitialCaches();
        
        $this->info('Setup Complete!');
    }
}
