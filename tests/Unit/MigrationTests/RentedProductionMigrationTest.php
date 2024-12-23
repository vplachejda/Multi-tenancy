<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\RentedProduction;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class RentedProductionMigrationTest extends TestCase
{
    /** @test */
    public function rented_production_table_has_expected_columns()
    {
        $this->assertTrue(Schema::hasTable('rented_productions'));
        $this->assertTrue(Schema::hasColumn('rented_productions', 'id'));
        $this->assertTrue(Schema::hasColumn('rented_productions', 'user_id'));
        $this->assertTrue(Schema::hasColumn('rented_productions', 'rental_date'));
        $this->assertTrue(Schema::hasColumn('rented_productions', 'created_at'));
        $this->assertTrue(Schema::hasColumn('rented_productions', 'updated_at'));
    }

    /** @test */
    public function user_id_is_a_foreign_key()
    {
        $user = User::factory()->create();
        $rentedProduction = RentedProduction::factory()->create(['user_id' => $user->id]);

        $this->assertEquals($rentedProduction->user_id, $user->id);

        // Check cascading delete
        $user->delete();
        $this->assertDatabaseMissing('rented_productions', ['id' => $rentedProduction->id]);
    }
 
    /** @test */
    public function user_can_have_multiple_rented_productions()
    {
        $user = User::factory()->create();
        $rentedProductions = RentedProduction::factory()->count(3)->create(['user_id' => $user->id]);

        $this->assertCount(3, $user->rentedProductions);
    }
 
    /** @test */
    public function rented_production_data_is_properly_cascaded()
    {
        $user = User::factory()->create();
        $rentedProduction = RentedProduction::factory()->create(['user_id' => $user->id]);

        // Assert the rented production belongs to the user
        $this->assertEquals($user->id, $rentedProduction->user_id);

        // Deleting the user should remove the rented production
        $user->delete();
        $this->assertDatabaseMissing('rented_productions', ['id' => $rentedProduction->id]);
    }
}
