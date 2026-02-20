<?php

namespace Tests\Feature;

use App\Models\Qualification;
use App\Models\StudyPlan;
use App\Models\User;
use App\Models\UserQualificationTarget;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudyProgressDataTest extends TestCase
{
    use RefreshDatabase;

    public function test_study_progress_data_returns_json(): void
    {
        $user = User::factory()->create();

        $qualification = Qualification::create([
            'name' => 'テスト資格',
            'is_active' => true,
        ]);

        $target = UserQualificationTarget::create([
            'user_id' => $user->id,
            'qualification_id' => $qualification->qualification_id,
            'start_date' => now()->subDays(14),
            'exam_date' => now()->addDays(30),
            'daily_study_time' => 60,
            'buffer_rate' => 10,
        ]);

        StudyPlan::create([
            'user_qualification_targets_id' => $target->user_qualification_targets_id,
            'version' => 1,
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)
            ->getJson(route('study-progress.data', [
                'target_id' => $target->user_qualification_targets_id,
            ]));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/json');
        $response->assertJsonStructure([
            'cumulative',
            'domain_rates',
            'period_rates',
            'summary' => [
                'planned_total',
                'actual_total',
                'achievement_rate',
            ],
            'selected_target_id',
            'period_start',
            'period_end',
            'qualification_name',
        ]);
    }
}
