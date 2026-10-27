<?php

namespace Tests\Feature;

use App\Enums\LeadStatus;
use App\Enums\Role;
use App\Models\Lead;
use App\Models\Subscriber;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminLeadsTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $user = User::factory()->create();
        $user->role = Role::Admin;
        $user->save();

        return $user;
    }

    private function lead(string $name, string $email, LeadStatus $status = LeadStatus::New): Lead
    {
        return Lead::create(['name' => $name, 'email' => $email, 'locale' => 'ru', 'status' => $status]);
    }

    public function test_leads_list_filters_by_status_and_search(): void
    {
        $this->lead('Анна', 'anna@example.com');
        $this->lead('Пётр', 'petr@example.com', LeadStatus::Done);
        $admin = $this->admin();

        $this->actingAs($admin)->get('/admin/leads')->assertOk()->assertSee('Анна')->assertSee('Пётр');
        $this->actingAs($admin)->get('/admin/leads?status=done')->assertSee('Пётр')->assertDontSee('anna@example.com');
        $this->actingAs($admin)->get('/admin/leads?q=ANNA')->assertSee('anna@example.com')->assertDontSee('petr@example.com');
    }

    public function test_lead_status_can_be_changed(): void
    {
        $lead = $this->lead('Анна', 'anna@example.com');
        $admin = $this->admin();

        $this->actingAs($admin)->get("/admin/leads/{$lead->id}")->assertOk()->assertSee('anna@example.com');
        $this->actingAs($admin)->patch("/admin/leads/{$lead->id}", ['status' => 'in_progress'])->assertSessionHasNoErrors();
        $this->assertSame(LeadStatus::InProgress, $lead->fresh()->status);

        $this->actingAs($admin)->patch("/admin/leads/{$lead->id}", ['status' => 'bogus'])->assertSessionHasErrors('status');
    }

    public function test_leads_export_csv(): void
    {
        $this->lead('Анна', 'anna@example.com');
        $this->lead('Пётр', 'petr@example.com', LeadStatus::Spam);

        $response = $this->actingAs($this->admin())->get('/admin/leads/export?status=new');
        $response->assertOk()->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $csv = $response->streamedContent();

        $this->assertStringStartsWith("\xEF\xBB\xBFID;Дата;Статус", $csv);
        $this->assertStringContainsString('anna@example.com', $csv);
        $this->assertStringNotContainsString('petr@example.com', $csv);
    }

    public function test_subscribers_list_and_export(): void
    {
        Subscriber::create(['email' => 'active@example.com', 'locale' => 'et']);
        Subscriber::create(['email' => 'gone@example.com', 'locale' => 'en', 'unsubscribed_at' => now()]);
        $admin = $this->admin();

        $this->actingAs($admin)->get('/admin/subscribers?state=active')->assertOk()
            ->assertSee('active@example.com')->assertDontSee('gone@example.com');

        $csv = $this->actingAs($admin)->get('/admin/subscribers/export')->assertOk()->streamedContent();
        $this->assertStringContainsString('active@example.com', $csv);
        $this->assertStringContainsString('gone@example.com', $csv);
    }

    public function test_guest_cannot_export(): void
    {
        $this->get('/admin/leads/export')->assertRedirect(route('admin.login'));
        $this->get('/admin/subscribers/export')->assertRedirect(route('admin.login'));
    }
}
