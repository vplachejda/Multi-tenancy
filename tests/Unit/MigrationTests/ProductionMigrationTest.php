<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Production;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ProductionMigrationTest extends TestCase
{
    /** @test */
    public function production_table_has_expected_columns()
    {
        $this->assertTrue(Schema::hasTable('productions'));
        $this->assertTrue(Schema::hasColumn('productions', 'id'));
        $this->assertTrue(Schema::hasColumn('productions', 'title'));
        $this->assertTrue(Schema::hasColumn('productions', 'genre'));
        $this->assertTrue(Schema::hasColumn('productions', 'user_id'));
        $this->assertTrue(Schema::hasColumn('productions', 'created_at'));
        $this->assertTrue(Schema::hasColumn('productions', 'updated_at'));
    }

    /** @test */
    public function user_id_is_a_foreign_key()
    {
        $user = User::factory()->create();
        $production = Production::factory()->create(['user_id' => $user->id]);

        $this->assertEquals($production->user_id, $user->id);

        // Verify cascading delete behavior
        $user->delete();
        $this->assertDatabaseMissing('productions', ['id' => $production->id]);
    }
 
    /** @test */
    public function title_and_genre_are_required()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);

        Production::factory()->create(['title' => null]);

        Production::factory()->create(['genre' => null]);
    }
 
    /** @test */
    public function user_can_have_multiple_productions()
    {
        $user = User::factory()->create();
        $productions = Production::factory()->count(3)->create(['user_id' => $user->id]);

        $this->assertCount(3, $user->productions);
    }
 
    /** @test */
    public function production_data_is_properly_cascaded()
    {
        $user = User::factory()->create();
        $production = Production::factory()->create(['user_id' => $user->id]);

        $this->assertEquals($user->id, $production->user_id);

        $user->delete();
        $this->assertDatabaseMissing('productions', ['id' => $production->id]);
    }
}
