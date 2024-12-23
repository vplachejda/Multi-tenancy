<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\AsyncSession;

class AsyncSessionSeeder extends Seeder
{
    public function run()
    {
        foreach ($this->sessionData() as $session) {
            AsyncSession::create($session);
        }
    }

    private function sessionData()
    {
        for ($i = 1; $i <= 200; $i++) {
            yield [
                'session_name' => "Session $i",
                'session_date' => now()->addDays($i),
                'user_id' => rand(1, 200),
            ];
        }
    }
}
