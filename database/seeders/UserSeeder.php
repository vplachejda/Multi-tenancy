<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run()
    {
        foreach ($this->userData() as $user) {
            User::create($user);
        }
    }

    private function userData()
    {
        for ($i = 1; $i <= 10000; $i++) {
            yield [
                'name' => "User $i",
                'last_name' => "Lastname $i",
                'email' => "user$i@example.com",
                'password' => bcrypt('password'),
                'company_id' => rand(1, 100),
                'tenant_id' => rand(1, 100),
                'role_id' => rand(1, 3),
            ];
        }
    }
}
