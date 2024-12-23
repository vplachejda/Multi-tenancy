<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\MyStoryTraining;

class MyStoryTrainingSeeder extends Seeder
{
    public function run()
    {
        foreach ($this->trainingData() as $training) {
            MyStoryTraining::create($training);
        }
    }

    private function trainingData()
    {
        for ($i = 1; $i <= 200; $i++) {
            yield [
                'user_id' => rand(1, 100),
                'training_date' => now()->subDays(rand(1, 30)),
            ];
        }
    }
}
