<?php

namespace App\Console\Commands;

use App\Models\Achievement;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

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

        $achievements = Achievement::get();

        $lesson_achievements = Achievement::where('group', Achievement::LESSON)->pluck('category')->toArray();
        $comments_achievements = Achievement::where('group', Achievement::COMMENT)->pluck('category')->toArray();

        Cache::put('achievements', json_encode($achievements));
        Cache::put('lesson_achievements', json_encode($lesson_achievements));
        Cache::put('comment_achievements', json_encode($comments_achievements));
        Cache::put('achievements_count', $achievements->count());

        $this->info('Setup Complete!');
    }
}
