<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Tenant;

class TenantSeeder extends Seeder
{
    public function run()
    {
        foreach ($this->tenantData() as $tenant) {
            Tenant::create($tenant);
        }
    }

    private function tenantData()
    {
        for ($i = 1; $i <= 100; $i++) { 
            yield [
                'name' => "Tenant $i",
                'company_id' => rand(1, 100),
            ];
        }
    }
}
