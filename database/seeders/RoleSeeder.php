<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run()
    {
        foreach ($this->roleData() as $role) {
            Role::create($role);
        }
    }

    private function roleData()
    {
        yield ['name' => 'Admin'];
        yield ['name' => 'Editor'];
        yield ['name' => 'Viewer'];
    }
}
