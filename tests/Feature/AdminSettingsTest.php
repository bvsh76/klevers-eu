<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminSettingsTest extends TestCase
{
    use RefreshDatabase;

    private function user(Role $role): User
    {
        $user = User::factory()->create();
        $user->role = $role;
        $user->save();

        return $user;
    }

    public function test_admin_sees_settings_page(): void
    {
        $this->actingAs($this->user(Role::Admin))->get('/admin/settings')
            ->assertOk()
            ->assertSee('name="ga4_id"', false);
    }

    public function test_admin_saves_and_normalizes_settings(): void
    {
        $this->actingAs($this->user(Role::Admin))->put('/admin/settings', [
            'whatsapp' => '+372 5555 5555',
            'telegram' => '@klevers_test',
            'lead_email' => 'test@example.com',
            'booking_embed' => '<div id="booking"></div>',
            'ga4_id' => 'g-abc1234',
        ])->assertRedirect(route('admin.settings.edit'));

        $this->assertSame('37255555555', Setting::get('whatsapp'));
        $this->assertSame('klevers_test', Setting::get('telegram'));
        $this->assertSame('test@example.com', Setting::get('lead_email'));
        $this->assertSame('<div id="booking"></div>', Setting::get('booking_embed'));
        $this->assertSame('G-ABC1234', Setting::get('ga4_id'));
    }

    public function test_empty_value_clears_setting(): void
    {
        Setting::put('telegram', 'klevers_test');

        $this->actingAs($this->user(Role::Admin))->put('/admin/settings', ['telegram' => ''])
            ->assertRedirect();

        $this->assertNull(Setting::get('telegram'));
    }

    public function test_invalid_values_are_rejected(): void
    {
        $this->actingAs($this->user(Role::Admin))->put('/admin/settings', [
            'whatsapp' => 'abc',
            'telegram' => 'a b',
            'lead_email' => 'not-email',
            'ga4_id' => 'abc',
        ])->assertSessionHasErrors(['whatsapp', 'telegram', 'lead_email', 'ga4_id']);
    }

    public function test_editor_has_no_access(): void
    {
        $editor = $this->user(Role::Editor);

        $this->actingAs($editor)->get('/admin/settings')->assertForbidden();
        $this->actingAs($editor)->put('/admin/settings', ['ga4_id' => 'G-ABC1234'])->assertForbidden();
        $this->actingAs($editor)->get('/admin')->assertOk()->assertDontSee('/admin/settings', false);
    }
}
