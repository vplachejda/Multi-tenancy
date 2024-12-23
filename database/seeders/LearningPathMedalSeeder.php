<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\LearningPathMedal;

class LearningPathMedalSeeder extends Seeder
{
    public function run()
    {
        foreach ($this->medalData() as $medal) {
            LearningPathMedal::create($medal);
        }
    }

    private function medalData()
    {
        for ($i = 1; $i <= 200; $i++) {
            yield [
                'medal_name' => "Medal $i",
                'description' => "Description for Medal $i.",
                'user_id' => rand(1, 200),
            ];
        }
    }
}
