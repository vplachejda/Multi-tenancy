<?php

namespace Tests\Unit;

use App\Models\LearningPathMedal;
use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class LearningPathMedalMigrationTest extends TestCase
{
    /** @test */
    public function learning_path_medal_table_has_expected_columns()
    {
        $this->assertTrue(Schema::hasTable('learning_path_medals'));
        $this->assertTrue(Schema::hasColumn('learning_path_medals', 'id'));
        $this->assertTrue(Schema::hasColumn('learning_path_medals', 'medal_name'));
        $this->assertTrue(Schema::hasColumn('learning_path_medals', 'description'));
        $this->assertTrue(Schema::hasColumn('learning_path_medals', 'user_id'));
        $this->assertTrue(Schema::hasColumn('learning_path_medals', 'created_at'));
        $this->assertTrue(Schema::hasColumn('learning_path_medals', 'updated_at'));
    }

    /** @test */
    public function user_id_is_a_foreign_key()
    {
        $user = User::factory()->create();
        $learningPathMedal = LearningPathMedal::factory()->create(['user_id' => $user->id]);

        $this->assertEquals($learningPathMedal->user_id, $user->id);

        $user->delete();
        $this->assertDatabaseMissing('learning_path_medals', ['id' => $learningPathMedal->id]);
    }

    /** @test */
    public function learning_path_medal_table_data_is_accessible()
    {
        $user = User::factory()->create();
        $medalName = 'Gold Medal';
        $description = 'Awarded for outstanding performance.';

        $record = \DB::table('learning_path_medals')->insertGetId([
            'user_id' => $user->id,
            'medal_name' => $medalName,
            'description' => $description,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->assertDatabaseHas('learning_path_medals', [
            'id' => $record,
            'user_id' => $user->id,
            'medal_name' => $medalName,
            'description' => $description,
        ]);
    }

    /** @test */
    public function deleting_user_cascades_to_learning_path_medals()
    {
        $user = User::factory()->create();
        $medalName = 'Gold Medal';

        \DB::table('learning_path_medals')->insert([
            'user_id' => $user->id,
            'medal_name' => $medalName,
            'description' => 'Awarded for outstanding performance.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Delete the user
        $user->delete();

        // Assert the learning_path_medals record is removed
        $this->assertDatabaseMissing('learning_path_medals', [
            'user_id' => $user->id,
            'medal_name' => $medalName,
        ]);
    }
}
