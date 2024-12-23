<?php

namespace Tests\Unit;

use App\Models\Tenant;
use App\Models\User;
use App\Models\Company;
use App\Models\Role;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class UserMigrationTest extends TestCase
{
    /** @test */
    public function user_table_has_expected_columns()
    {
        $this->assertTrue(Schema::hasTable('users'));
        $this->assertTrue(Schema::hasColumn('users', 'id'));
        $this->assertTrue(Schema::hasColumn('users', 'name'));
        $this->assertTrue(Schema::hasColumn('users', 'email'));
        $this->assertTrue(Schema::hasColumn('users', 'password'));
        $this->assertTrue(Schema::hasColumn('users', 'tenant_id'));
        $this->assertTrue(Schema::hasColumn('users', 'company_id'));
        $this->assertTrue(Schema::hasColumn('users', 'role_id'));
        $this->assertTrue(Schema::hasColumn('users', 'created_at'));
        $this->assertTrue(Schema::hasColumn('users', 'updated_at'));
    }

    /** @test */
    public function user_table_foreign_keys_are_valid()
    {
        $this->assertTrue(Schema::hasColumn('users', 'company_id'));
        $this->assertTrue(Schema::hasColumn('users', 'tenant_id'));
        $this->assertTrue(Schema::hasColumn('users', 'role_id'));

        $company = Company::factory()->create();
        $tenant = Tenant::factory()->create();
        $role = Role::factory()->create();

        // Create a user instance with valid foreign key references
        $user = User::factory()->create([
            'company_id' => $company->id,
            'tenant_id' => $tenant->id,
            'role_id' => $role->id
        ]);

        // Ensure the foreign keys are correctly set
        $this->assertEquals($user->company_id, $company->id);
        $this->assertEquals($user->tenant_id, $tenant->id);
        $this->assertEquals($user->role_id, $role->id);

        // Check cascading delete behavior by deleting the company, tenant, or role
        $company->delete();
        $this->assertDatabaseMissing('users', ['company_id' => $company->id]);

        // Delete tenant and verify if related user is deleted (assuming cascade on delete)
        $tenant->delete();
        $this->assertDatabaseMissing('users', ['tenant_id' => $tenant->id]);

        // Delete role and verify if related user is deleted (assuming cascade on delete)
        $role->delete();
        $this->assertDatabaseMissing('users', ['role_id' => $role->id]);
    }

    /** @test */
    public function user_company_matches_tenant_company()
    {
        $company = Company::factory()->create();
        $tenant = Tenant::factory()->for($company)->create();
        $user = User::factory()->for($tenant)->for($company)->create();

        $this->assertEquals($user->company_id, $tenant->company_id);
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
    public function deleting_company_deletes_related_tenants()
    {
        $company = Company::factory()->create();
        $tenant = Tenant::factory()->for($company)->create();

        $company->delete();
        $this->assertDatabaseMissing('tenants', ['id' => $tenant->id]);
    }

}
