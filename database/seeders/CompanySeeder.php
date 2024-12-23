<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Company;

class CompanySeeder extends Seeder
{
    public function run()
    {
        foreach ($this->companyData() as $company) {
            Company::create($company);
        }
    }

    private function companyData()
    {
        for ($i = 1; $i <= 200; $i++) {
            yield [
                'name' => "Company $i",
            ];
        }
    }
}
