<?php

namespace Tests\Unit;

use App\Models\Chapter;
use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ChapterMigrationTest extends TestCase
{
    /** @test */
    public function chapter_table_has_expected_columns()
    {
        $this->assertTrue(Schema::hasTable('chapters'));
        $this->assertTrue(Schema::hasColumn('chapters', 'id'));
        $this->assertTrue(Schema::hasColumn('chapters', 'title'));
        $this->assertTrue(Schema::hasColumn('chapters', 'description'));
        $this->assertTrue(Schema::hasColumn('chapters', 'user_id'));
        $this->assertTrue(Schema::hasColumn('chapters', 'created_at'));
        $this->assertTrue(Schema::hasColumn('chapters', 'updated_at'));
    }

    /** @test */
    public function user_id_is_a_foreign_key()
    {
        $user = User::factory()->create();
        $chapter = Chapter::factory()->create(['user_id' => $user->id]);

        $this->assertEquals($chapter->user_id, $user->id);

        $user->delete();
        $this->assertDatabaseMissing('chapters', ['id' => $chapter->id]);
    }

    /** @test */
    public function can_insert_and_retrieve_chapter_data()
    {
        $chapterData = [
            'title' => 'Sample Chapter',
            'description' => 'This is a description of the sample chapter.',
            'user_id' => User::factory()->create()->id,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        $chapterId = \DB::table('chapters')->insertGetId($chapterData);

        $this->assertDatabaseHas('chapters', [
            'id' => $chapterId,
            'title' => $chapterData['title'],
            'description' => $chapterData['description'],
        ]);

        $retrievedChapter = \DB::table('chapters')->find($chapterId);
        $this->assertEquals($chapterData['title'], $retrievedChapter->title);
        $this->assertEquals($chapterData['description'], $retrievedChapter->description);
    }

    /** @test */
    public function deleting_user_cascades_to_related_chapters()
    {
        $user = User::factory()->create();
        $chapter = Chapter::factory()->create(['user_id' => $user->id]);

        // Assert the chapter exists
        $this->assertDatabaseHas('chapters', [
            'id' => $chapter->id,
            'user_id' => $user->id,
        ]);

        // Delete the user
        $user->delete();

        // Assert the chapter is deleted due to cascade
        $this->assertDatabaseMissing('chapters', ['id' => $chapter->id]);
    }
}
