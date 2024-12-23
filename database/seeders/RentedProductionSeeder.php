<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\RentedProduction;

class RentedProductionSeeder extends Seeder
{
    public function run()
    {
        foreach ($this->rentedData() as $rented) {
            RentedProduction::create($rented);
        }
    }

    private function rentedData()
    {
        for ($i = 1; $i <= 200; $i++) {
            yield [
                'user_id' => rand(1, 100),
                'rental_date' => now()->subDays(rand(1, 30)),
            ];
        }
    }
}
