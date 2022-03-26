<?php

namespace Database\Seeders;

use App\Models\Lesson;
use App\Models\User;
use App\Traits\Caches;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use Caches;

    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            AchievementSeeder::class
        ]);

        $lessons = Lesson::factory()
            ->count(20)
            ->create();

        $users = User::factory(5)->create()->each(function ($user){
            // $user->lessons()->attach(Lesson::select('id')->orderByRaw("RAND()")->first()->id);

            $this->cacheAchievements($user, [], true);
        });
    }
}
