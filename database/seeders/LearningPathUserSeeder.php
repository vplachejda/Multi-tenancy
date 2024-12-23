<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\LearningPathUser;

class LearningPathUserSeeder extends Seeder
{
    public function run()
    {
        foreach ($this->pathUserData() as $pathUser) {
            LearningPathUser::create($pathUser);
        }
    }

    private function pathUserData()
    {
        for ($i = 1; $i <= 200; $i++) {
            yield [
                'user_id' => rand(1, 200),
                'path_id' => "path_ $i",
            ];
        }
    }
}
