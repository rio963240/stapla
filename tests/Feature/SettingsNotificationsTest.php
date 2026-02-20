<?php

namespace Tests\Feature;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettingsNotificationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_notification_settings_can_be_updated(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->putJson(route('settings.notifications.update'), [
                'line_notify_enabled' => true,
                'line_morning_at' => '07:00',
                'line_evening_at' => '21:00',
            ]);

        $response->assertOk();
        $response->assertJson(['status' => 'notifications-updated']);

        $user->refresh();
        $this->assertTrue($user->line_notify_enabled);
        $this->assertEquals('07:00', Carbon::parse($user->line_morning_time)->format('H:i'));
        $this->assertEquals('21:00', Carbon::parse($user->line_evening_time)->format('H:i'));
    }

    public function test_invalid_morning_time_format_returns_422(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->putJson(route('settings.notifications.update'), [
                'line_notify_enabled' => false,
                'line_morning_at' => '25:00',
                'line_evening_at' => '20:00',
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['line_morning_at']);
    }

    public function test_invalid_evening_time_format_returns_422(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->putJson(route('settings.notifications.update'), [
                'line_notify_enabled' => false,
                'line_morning_at' => '08:00',
                'line_evening_at' => '99:99',
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['line_evening_at']);
    }

    public function test_invalid_time_format_with_minutes_returns_422(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->putJson(route('settings.notifications.update'), [
                'line_notify_enabled' => false,
                'line_morning_at' => '08:60',
                'line_evening_at' => '20:00',
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['line_morning_at']);
    }
}
