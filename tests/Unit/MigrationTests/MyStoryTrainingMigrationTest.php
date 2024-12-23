<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\MyStoryTraining;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class MyStoryTrainingMigrationTest extends TestCase
{
    /** @test */
    public function my_story_training_table_has_expected_columns()
    {
        $this->assertTrue(Schema::hasTable('my_story_trainings'));
        $this->assertTrue(Schema::hasColumn('my_story_trainings', 'id'));
        $this->assertTrue(Schema::hasColumn('my_story_trainings', 'user_id'));
        $this->assertTrue(Schema::hasColumn('my_story_trainings', 'training_date'));
        $this->assertTrue(Schema::hasColumn('my_story_trainings', 'created_at'));
        $this->assertTrue(Schema::hasColumn('my_story_trainings', 'updated_at'));
    }

    /** @test */
    public function user_id_is_a_foreign_key()
    {
        $user = User::factory()->create();
        $training = MyStoryTraining::factory()->create(['user_id' => $user->id]);

        $this->assertEquals($training->user_id, $user->id);

        $user->delete();
        $this->assertDatabaseMissing('my_story_trainings', ['id' => $training->id]);
    }

    /** @test */
    public function user_can_have_multiple_trainings()
    {
        $user = User::factory()->create();
        $trainings = MyStoryTraining::factory()->count(3)->create(['user_id' => $user->id]);

        $this->assertCount(3, $user->myStoryTrainings);
    }

    /** @test */
    public function my_story_training_data_is_properly_cascaded()
    {
        $user = User::factory()->create();
        $training = MyStoryTraining::factory()->create(['user_id' => $user->id]);

        $this->assertEquals($user->id, $training->user_id);

        $user->delete();
        $this->assertDatabaseMissing('my_story_trainings', ['id' => $training->id]);
    }
}
