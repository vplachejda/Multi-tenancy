<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Production;

class ProductionSeeder extends Seeder
{
    public function run()
    {
        foreach ($this->productionData() as $production) {
            Production::create($production);
        }
    }

    private function productionData()
    {
        for ($i = 1; $i <= 200; $i++) {
            yield [
                'title' => "Production $i",
                'genre' => ['Education', 'Entertainment', 'Documentary'][rand(0, 2)],
                'user_id' => rand(1, 100),
            ];
        }
    }
}
