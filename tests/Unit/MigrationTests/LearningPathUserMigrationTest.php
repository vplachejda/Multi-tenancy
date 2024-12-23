<?php

namespace Tests\Unit;

use App\Models\LearningPathUser;
use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class LearningPathUserMigrationTest extends TestCase
{
    /** @test */
    public function learning_path_user_table_has_expected_columns()
    {
        $this->assertTrue(Schema::hasTable('learning_path_users'));
        $this->assertTrue(Schema::hasColumn('learning_path_users', 'id'));
        $this->assertTrue(Schema::hasColumn('learning_path_users', 'user_id'));
        $this->assertTrue(Schema::hasColumn('learning_path_users', 'path_id'));
        $this->assertTrue(Schema::hasColumn('learning_path_users', 'created_at'));
        $this->assertTrue(Schema::hasColumn('learning_path_users', 'updated_at'));
    }
    
    /** @test */
    public function user_id_is_a_foreign_key()
    {
        $user = User::factory()->create();
        $learningPathUser = LearningPathUser::factory()->create(['user_id' => $user->id]);

        $this->assertEquals($learningPathUser->user_id, $user->id);

        $user->delete();
        $this->assertDatabaseMissing('learning_path_users', ['id' => $learningPathUser->id]);
    }

    /** @test */
    public function learning_path_user_table_data_is_accessible()
    {
        $user = User::factory()->create();
        $pathId = 'test-path-id'; 
        $record = \DB::table('learning_path_users')->insertGetId([
            'user_id' => $user->id,
            'path_id' => $pathId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->assertDatabaseHas('learning_path_users', [
            'id' => $record,
            'user_id' => $user->id,
            'path_id' => $pathId,
        ]);
    }

    /** @test */
    public function deleting_user_cascades_to_learning_path_users()
    {
        $user = User::factory()->create();
        $pathId = 'test-path-id';
        \DB::table('learning_path_users')->insert([
            'user_id' => $user->id,
            'path_id' => $pathId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Delete the user
        $user->delete();

        // Assert the learning_path_users record is removed
        $this->assertDatabaseMissing('learning_path_users', [
            'user_id' => $user->id,
            'path_id' => $pathId,
        ]);
    }
}
