<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Chapter;

class ChapterSeeder extends Seeder
{
    public function run()
    {
        foreach ($this->chapterData() as $chapter) {
            Chapter::create($chapter);
        }
    }

    private function chapterData()
    {
        for ($i = 1; $i <= 200; $i++) {
            yield [
                'title' => "Chapter $i",
                'description' => "Description for Chapter $i.",
                'user_id' => rand(1, 200),
            ];
        }
    }
}
