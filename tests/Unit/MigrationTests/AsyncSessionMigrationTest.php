<?php

namespace Tests\Unit;

use App\Models\AsyncSession;
use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AsyncSessionMigrationTest extends TestCase
{
    /** @test */
    public function async_session_table_has_expected_columns()
    {
        $this->assertTrue(Schema::hasTable('async_sessions'));
        $this->assertTrue(Schema::hasColumn('async_sessions', 'id'));
        $this->assertTrue(Schema::hasColumn('async_sessions', 'session_name'));
        $this->assertTrue(Schema::hasColumn('async_sessions', 'session_date'));
        $this->assertTrue(Schema::hasColumn('async_sessions', 'user_id'));
        $this->assertTrue(Schema::hasColumn('async_sessions', 'created_at'));
        $this->assertTrue(Schema::hasColumn('async_sessions', 'updated_at'));
    }

    /** @test */
    public function user_id_is_a_foreign_key()
    {
        $user = User::factory()->create();
        $asyncSession = AsyncSession::factory()->create(['user_id' => $user->id]);

        $this->assertEquals($asyncSession->user_id, $user->id);

        $user->delete();
        $this->assertDatabaseMissing('async_sessions', ['id' => $asyncSession->id]);
    }

    /** @test */
    public function can_insert_and_retrieve_async_session_data()
    {
        $sessionData = [
            'session_name' => 'Async Training Session',
            'session_date' => now()->format('Y-m-d'), // '2024-12-20'
            'user_id' => User::factory()->create()->id,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        $sessionId = \DB::table('async_sessions')->insertGetId($sessionData);

        $this->assertDatabaseHas('async_sessions', [
            'id' => $sessionId,
            'session_name' => $sessionData['session_name'],
        ]);

        $retrievedSession = \DB::table('async_sessions')->find($sessionId);

        // Compare only the date part of session_date
        $this->assertEquals(
            \Carbon\Carbon::parse($sessionData['session_date'])->toDateString(),
            \Carbon\Carbon::parse($retrievedSession->session_date)->toDateString()
        );
    }
    
    /** @test */
    public function deleting_user_cascades_to_related_async_sessions()
    {
        $user = User::factory()->create();
        $asyncSession = AsyncSession::factory()->create(['user_id' => $user->id]);

        // Assert the session exists
        $this->assertDatabaseHas('async_sessions', [
            'id' => $asyncSession->id,
            'user_id' => $user->id,
        ]);

        // Delete the user
        $user->delete();

        // Assert the session is deleted due to cascade
        $this->assertDatabaseMissing('async_sessions', ['id' => $asyncSession->id]);
    }

    /** @test */
    public function session_name_is_required()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);

        AsyncSession::factory()->create(['session_name' => null]);
    }

    /** @test */
    public function session_date_is_valid_format()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);

        AsyncSession::factory()->create(['session_date' => 'invalid-date']);
    }
}
