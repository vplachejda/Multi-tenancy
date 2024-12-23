<?php

namespace Tests\Unit;

use App\Models\Tenant;
use App\Models\User;
use App\Models\Company;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class TenantMigrationTest extends TestCase
{
    /** @test */
    public function tenant_table_has_expected_columns()
    {
        $this->assertTrue(Schema::hasTable('tenants'));
        $this->assertTrue(Schema::hasColumn('tenants', 'id'));
        $this->assertTrue(Schema::hasColumn('tenants', 'name'));
        $this->assertTrue(Schema::hasColumn('tenants', 'company_id'));
        $this->assertTrue(Schema::hasColumn('tenants', 'created_at'));
        $this->assertTrue(Schema::hasColumn('tenants', 'updated_at'));
    }

    /** @test */
    public function company_id_is_a_foreign_key()
    {
        $company = Company::factory()->create();
        $tenant = Tenant::factory()->create(['company_id' => $company->id]);

        $this->assertEquals($tenant->company_id, $company->id);

        $company->delete();
        $this->assertDatabaseMissing('tenants', ['id' => $tenant->id]);
    }

    /** @test */
    public function tenants_are_tenant_isolated()
    {
        // Create two companies
        $company1 = Company::factory()->create();
        $company2 = Company::factory()->create();

        // Create tenants for each company
        $tenant1 = Tenant::factory()->for($company1)->create();
        $tenant2 = Tenant::factory()->for($company2)->create();

        // Create users for each tenant
        $user1 = User::factory()->for($tenant1)->create();
        $user2 = User::factory()->for($tenant2)->create();

        // Check user1 can access their own tenant
        $this->assertEquals($tenant1->id, $user1->tenant_id);

        // Check user2 can access their own tenant
        $this->assertEquals($tenant2->id, $user2->tenant_id);

        // Check user1 cannot access tenant2
        $this->assertNotEquals($tenant1->id, $tenant2->id);
        $this->assertNotEquals($user1->tenant_id, $tenant2->id);

        // Check that user1's tenant data is isolated
        $tenant1Users = User::where('tenant_id', $tenant1->id)->get();
        $this->assertTrue($tenant1Users->contains($user1));
        $this->assertFalse($tenant1Users->contains($user2));
    }

    /** @test */
    public function tenant_based_scoping_restricts_data()
    {
        $tenant1 = Tenant::factory()->create();
        $tenant2 = Tenant::factory()->create();

        $user1 = User::factory()->for($tenant1)->create();
        $user2 = User::factory()->for($tenant2)->create();

        // Ensure tenant1 can only retrieve its users
        $tenant1Users = User::where('tenant_id', $tenant1->id)->get();
        $this->assertTrue($tenant1Users->contains($user1));
        $this->assertFalse($tenant1Users->contains($user2));
    }

    /** @test */
    public function deleting_company_deletes_related_tenants_and_users()
    {
        $company = Company::factory()->create();
        $tenant = Tenant::factory()->for($company)->create();
        $user = User::factory()->for($tenant)->create();

        $company->delete();

        $this->assertDatabaseMissing('tenants', ['id' => $tenant->id]);
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }
  
    /** @test */
    public function tenant_names_can_be_duplicate_across_different_companies()
    {
        $company1 = Company::factory()->create();
        $company2 = Company::factory()->create();

        Tenant::factory()->for($company1)->create(['name' => 'Tenant 1']);
        $tenant2 = Tenant::factory()->for($company2)->create(['name' => 'Tenant 1']);

        $this->assertDatabaseHas('tenants', ['id' => $tenant2->id, 'name' => 'Tenant 1']);
    }
  
    /** @test */
    public function tenant_can_have_multiple_users()
    {
        $tenant = Tenant::factory()->create();

        $user1 = User::factory()->for($tenant)->create();
        $user2 = User::factory()->for($tenant)->create();

        $this->assertTrue($tenant->users->contains($user1));
        $this->assertTrue($tenant->users->contains($user2));
    }
  
    /** @test */
    public function deleting_tenant_deletes_related_users()
    {
        $tenant = Tenant::factory()->create();
        $user = User::factory()->for($tenant)->create();

        $tenant->delete();

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }
  
    /** @test */
    public function tenant_data_isolated_across_companies()
    {
        $company1 = Company::factory()->create();
        $company2 = Company::factory()->create();

        $tenant1 = Tenant::factory()->for($company1)->create();
        $tenant2 = Tenant::factory()->for($company2)->create();

        $user1 = User::factory()->for($tenant1)->create();
        $user2 = User::factory()->for($tenant2)->create();

        // Ensure tenant1 data does not overlap with tenant2 data
        $this->assertNotEquals($user1->tenant_id, $user2->tenant_id);
        $this->assertNotEquals($tenant1->id, $tenant2->id);
    }
}
