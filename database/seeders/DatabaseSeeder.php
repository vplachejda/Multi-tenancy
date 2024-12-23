<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            RoleSeeder::class,
            CompanySeeder::class,
            TenantSeeder::class,
            UserSeeder::class,
            ChapterSeeder::class,
            ProductionSeeder::class,
            MyStoryTrainingSeeder::class,
            LearningPathMedalSeeder::class,
            LearningPathUserSeeder::class,
            RentedProductionSeeder::class,
            AsyncSessionSeeder::class,
        ]);
    }
}
