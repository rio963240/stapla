<?php

namespace Tests\Feature;

use App\Models\Qualification;
use App\Models\User;
use App\Models\UserQualificationTarget;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlanRescheduleTest extends TestCase
{
    use RefreshDatabase;

    public function test_returns_404_when_accessing_other_users_target(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $qualification = Qualification::create([
            'name' => 'テスト資格',
            'is_active' => true,
        ]);

        $targetB = UserQualificationTarget::create([
            'user_id' => $userB->id,
            'qualification_id' => $qualification->qualification_id,
            'start_date' => now()->addDays(7),
            'exam_date' => now()->addDays(60),
            'daily_study_time' => 60,
            'buffer_rate' => 10,
        ]);

        $response = $this->actingAs($userA)
            ->getJson(route('plan-reschedule.data', ['target_id' => $targetB->user_qualification_targets_id]));

        $response->assertStatus(404);
        $response->assertJson(['message' => 'not found']);
    }
}
