<?php

namespace Tests\Unit;

use App\Models\Company;
use App\Models\Tenant;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class CompanyMigrationTest extends TestCase
{
    /** @test */
    public function company_table_has_expected_columns()
    {
        $this->assertTrue(Schema::hasTable('companies'));
        $this->assertTrue(Schema::hasColumn('companies', 'id'));
        $this->assertTrue(Schema::hasColumn('companies', 'name'));
        $this->assertTrue(Schema::hasColumn('companies', 'created_at'));
        $this->assertTrue(Schema::hasColumn('companies', 'updated_at'));
    }

    /** @test */
    public function can_insert_and_retrieve_company_data()
    {
        $companyName = 'Test Company';

        $companyId = \DB::table('companies')->insertGetId([
            'name' => $companyName,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->assertDatabaseHas('companies', [
            'id' => $companyId,
            'name' => $companyName,
        ]);

        $retrievedCompany = \DB::table('companies')->find($companyId);
        $this->assertEquals($companyName, $retrievedCompany->name);
    }
  
    /** @test */
    public function deleting_company_cascades_to_related_tenants()
    {
        $company = Company::factory()->create();

        $tenant = Tenant::factory()->create(['company_id' => $company->id]);

        // Assert the tenant exists
        $this->assertDatabaseHas('tenants', [
            'id' => $tenant->id,
            'company_id' => $company->id,
        ]);

        // Delete the company
        $company->delete();

        // Assert the tenant is deleted due to cascade
        $this->assertDatabaseMissing('tenants', ['id' => $tenant->id]);
    }
  
    /** @test */
    public function company_name_is_unique()
    {
        $companyName = 'Unique Company';

        \DB::table('companies')->insert([
            'name' => $companyName,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->expectException(\Illuminate\Database\QueryException::class);

        // Try inserting a company with the same name
        \DB::table('companies')->insert([
            'name' => $companyName,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
