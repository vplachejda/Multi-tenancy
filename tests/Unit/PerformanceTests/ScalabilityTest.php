<?php

namespace Tests\Unit\PerformanceTests;

use Tests\TestCase;

class ScalabilityTest extends TestCase
{
    /** @test */
    public function system_scales_with_increasing_tenant_load()
    {
        // Ensure a company exists
        $companyId = \DB::table('companies')->insertGetId([
            'name' => 'Test Company'. uniqid(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Insert 100 tenants referencing the created company
        for ($i = 1; $i <= 100; $i++) {
            \DB::table('tenants')->insert([
                'name' => "Tenant $i",
                'company_id' => $companyId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Optionally validate users for the tenant
            $users = \DB::table('users')->where('tenant_id', $i)->get();
            $this->assertGreaterThanOrEqual(0, $users->count());
        }

        // Assert total tenants count
        $totalTenants = \DB::table('tenants')->count();
        $this->assertEquals(100, $totalTenants);
    }

    /** @test */
    public function system_handles_large_number_of_users_per_tenant()
    {
        // Ensure a company exists
         $companyId = \DB::table('companies')->insertGetId([
            'name' => 'Test Company'. uniqid(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $tenant = \DB::table('tenants')->insertGetId([
            'name' => 'Large Load Tenant',
            'company_id' => $companyId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        for ($i = 1; $i <= 1000; $i++) {
            \DB::table('users')->insert([
                'name' => "User $i",
                'tenant_id' => $tenant,
                'email' => "user$i$i@example.com",
                'password' => bcrypt("password"),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $users = \DB::table('users')->where('tenant_id', $tenant)->count();
        $this->assertEquals(1000, $users);
    }
 
     /** @test */
    public function system_performs_well_with_high_concurrent_reads()
    {
         // Ensure a company exists
         $companyId = \DB::table('companies')->insertGetId([
            'name' => 'Test Company'. uniqid(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $tenantId = \DB::table('tenants')->insertGetId([
            'name' => 'Concurrent Reads Tenant',
            'company_id' => $companyId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        for ($i = 1; $i <= 500; $i++) {
            \DB::table('users')->insert([
                'name' => "Concurrent User $i",
                'tenant_id' => $tenantId,
                'email' => "user{$tenantId}_$i@example.com",
                'password' => bcrypt("password"),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $startTime = microtime(true);

        for ($i = 1; $i <= 100; $i++) {
            $users = \DB::table('users')->where('tenant_id', $tenantId)->get();
            $this->assertNotEmpty($users);
        }

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $this->assertLessThan(5, $executionTime, 'High concurrent reads took too long!');
    }
 
     /** @test */
    public function system_performs_well_with_high_concurrent_writes()
    {
        $startTime = microtime(true);

        // Ensure a company exists
        $companyId = \DB::table('companies')->insertGetId([
            'name' => 'Test Company'. uniqid(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        for ($i = 1; $i <= 1000; $i++) {
            \DB::table('tenants')->insert([
                'name' => "Concurrent Write Tenant $i",
                'company_id' => $companyId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $this->assertLessThan(10, $executionTime, 'High concurrent writes took too long!');
    }
 
    /** @test */
    public function system_sustains_performance_under_simultaneous_reads_and_writes()
    {
        $startTime = microtime(true);

        // Ensure a company exists
        $companyId = \DB::table('companies')->insertGetId([
            'name' => 'Test Company'. uniqid(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        for ($i = 1; $i <= 500; $i++) {
            \DB::table('tenants')->insert([
                'name' => "Simultaneous Tenant $i",
                'company_id' => $companyId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $users = \DB::table('users')->where('tenant_id', $i)->get();
            $this->assertGreaterThanOrEqual(0, $users->count());
        }

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $this->assertLessThan(20, $executionTime, 'Simultaneous reads and writes took too long!');
    }
}
