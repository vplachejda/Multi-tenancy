<?php

namespace Tests\Unit\PerformanceTests;

use Tests\TestCase;

class QueryOptimizationTest extends TestCase
{
    /** @test */
    public function optimized_query_executes_within_expected_time()
    {
        $startTime = microtime(true);

        // Execute the optimized query
        $users = \DB::table('users')
            ->join('tenants', 'users.tenant_id', '=', 'tenants.id')
            ->select('users.name', 'tenants.name as tenant_name')
            ->get();

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        // Assertions
        $this->assertNotEmpty($users, 'The query returned no results. Ensure there is sufficient data available.');
        $this->assertLessThan(
            2,
            $executionTime,
            sprintf('The query execution time was %.2f seconds, which exceeds the expected 2 seconds.', $executionTime)
        );
    }

    /** @test */
    public function indexed_query_performs_faster()
    {
        // Assuming an index exists on the 'email' column
        $startTime = microtime(true);

        $user = \DB::table('users')->where('email', 'user1@example.com')->first();

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $this->assertNotNull($user, 'No user found with the given email.');
        $this->assertLessThan(1, $executionTime, 'Indexed query took too long to execute!');
    }

    /** @test */
    public function pagination_query_is_efficient()
    {
        $startTime = microtime(true);

        $users = \DB::table('users')->paginate(100);

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $this->assertCount(100, $users->items(), 'Pagination did not return the expected number of items.');
        $this->assertLessThan(1, $executionTime, 'Pagination query took too long to execute!');
    }

    /** @test */
    public function batch_processing_query_is_efficient()
    {
        $startTime = microtime(true);

        \DB::table('users')->orderBy('id')->chunk(100, function ($users) {
            foreach ($users as $user) {
            }
        });

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $this->assertLessThan(10, $executionTime, 'Batch processing query took too long!');
    }

    /** @test */
    public function query_with_where_in_condition_is_optimized()
    {
        $ids = range(1, 100);

        $startTime = microtime(true);

        $users = \DB::table('users')->whereIn('id', $ids)->get();

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $this->assertCount(100, $users, 'Query did not return the expected number of rows.');
        $this->assertLessThan(2, $executionTime, 'WhereIn query took too long to execute!');
    }

    /** @test */
    public function caching_optimized_query_reduces_execution_time()
    {
        $startTimeFirst = microtime(true);

        $users = \DB::table('users')->get();

        $endTimeFirst = microtime(true);
        $executionTimeFirst = $endTimeFirst - $startTimeFirst;

        $this->assertNotEmpty($users, 'Query returned no results without cache.');

        // Store results in cache
        \Cache::put('all_users', $users, now()->addMinutes(10));

        $startTimeSecond = microtime(true);

        // Cached query
        $cachedUsers = \Cache::get('all_users');

        $endTimeSecond = microtime(true);
        $executionTimeSecond = $endTimeSecond - $startTimeSecond;

        $this->assertNotEmpty($cachedUsers, 'Query returned no results from cache.');
        $this->assertLessThan(
            $executionTimeFirst,
            $executionTimeSecond,
            sprintf('Cached query took longer (%.4f seconds) than the original query (%.4f seconds)!', $executionTimeSecond, $executionTimeFirst)
        );
    }

    /** @test */
    public function query_with_selecting_only_needed_columns_performs_better()
    {
        $startTime = microtime(true);

        $users = \DB::table('users')->select('id', 'name')->get();

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $this->assertNotEmpty($users, 'Query returned no results.');
        $this->assertLessThan(1, $executionTime, 'Query with selected columns took too long!');
    }
}
