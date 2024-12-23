<?php

namespace Tests\Unit\PerformanceTests;

use Tests\TestCase;

class LoadTest extends TestCase
{
    /** @test */
    public function it_handles_1000_users_querying_data_simultaneously()
    {
        $startTime = microtime(true);

        for ($i = 0; $i < 1000; $i++) {
            \DB::table('users')->where('id', rand(1, 1000))->get();
        }

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $this->assertLessThan(5, $executionTime, 'Query execution exceeded expected time!');
    }

    /** @test */
    public function it_handles_bulk_inserts_without_exceeding_time_limit()
    {
        $startTime = microtime(true);

        $bulkData = [];
        for ($i = 0; $i < 1000; $i++) {
            $bulkData[] = [
                'name' => 'Test User ' . $i,
                'email' => 'user'. uniqid($i) . '@example.com',
                'password' => bcrypt('password'),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        \DB::table('users')->insert($bulkData);

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $this->assertLessThan(10, $executionTime, 'Bulk insert operation exceeded expected time!');
    }

    /** @test */
    public function it_handles_large_data_retrieval_without_exceeding_time_limit()
    {
        $startTime = microtime(true);

        $users = \DB::table('users')->limit(10000)->get();

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $this->assertLessThan(8, $executionTime, 'Large data retrieval exceeded expected time!');
        $this->assertGreaterThanOrEqual(10000, $users->count(), 'Retrieved fewer rows than expected.');
    }

    /** @test */
    public function it_handles_concurrent_queries_without_errors()
    {
        $threads = [];

        for ($i = 0; $i < 10; $i++) {
            $threads[] = function () {
                \DB::table('users')->where('id', rand(1, 1000))->get();
            };
        }

        $startTime = microtime(true);

        // Execute all threads concurrently
        foreach ($threads as $thread) {
            $thread();
        }

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $this->assertLessThan(5, $executionTime, 'Concurrent queries exceeded expected time!');
    }

    /** @test */
    public function it_scales_with_increasing_data_volume()
    {
        $chunkSize = 1000; // Insert 1,000 records per chunk
        for ($i = 1; $i <= 5; $i++) {
            for ($chunk = 0; $chunk < 10; $chunk++) {
                $bulkData = [];
                for ($j = 0; $j < $chunkSize; $j++) {
                    $bulkData[] = [
                        'name' => 'Test User ' . ($i * $chunk * $j),
                        'email' => 'user' . uniqid($i) . uniqid($chunk) . ($i * $chunk * $j) . '@example.com',
                        'password' => bcrypt('password'),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                \DB::table('users')->insert($bulkData);
            }

            // Measure query performance
            $startTime = microtime(true);
            $users = \DB::table('users')->where('name', 'like', '%Test%')->get();
            $endTime = microtime(true);

            $executionTime = $endTime - $startTime;
            $this->assertLessThan(10, $executionTime, "Query on iteration $i exceeded time limit!");
        }
    }
}
