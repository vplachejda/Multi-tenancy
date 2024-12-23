<?php

namespace Tests\Unit;

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class RoleMigrationTest extends TestCase
{
    /** @test */
    public function role_table_has_expected_columns()
    {
        $this->assertTrue(Schema::hasTable('roles'));
        $this->assertTrue(Schema::hasColumn('roles', 'id'));
        $this->assertTrue(Schema::hasColumn('roles', 'name'));
        $this->assertTrue(Schema::hasColumn('roles', 'created_at'));
        $this->assertTrue(Schema::hasColumn('roles', 'updated_at'));
    }

    /** @test */
    public function role_names_are_unique()
    {
        $role1 = Role::factory()->create(['name' => 'example']);
        
        $this->expectException(\Illuminate\Database\QueryException::class);

        // Attempt to create another role with the same name
        Role::factory()->create(['name' => 'example']);
    }
 
    /** @test */
    public function roles_can_have_associated_users()
    {
        $role = Role::factory()->create(['name' => 'example1']);
        $user = User::factory()->for($role)->create();

        $this->assertEquals($role->id, $user->role_id);
        $this->assertTrue($role->users->contains($user));
    }

    /** @test */
    public function role_names_cannot_be_null()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);

        // Attempt to create a role with a null name
        Role::factory()->create(['name' => null]);
    }
}
