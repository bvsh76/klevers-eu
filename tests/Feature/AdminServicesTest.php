<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Segment;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminServicesTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $user = User::factory()->create();
        $user->role = Role::Admin;
        $user->save();

        return $user;
    }

    public function test_segment_crud_with_intro(): void
    {
        $this->actingAs($this->admin())->post('/admin/segments', [
            'title' => ['ru' => 'Начинающим'],
            'intro' => ['ru' => 'Коротко о сегменте'],
        ])->assertSessionHasNoErrors();

        $segment = Segment::firstOrFail();
        $this->assertSame('nachinayuschim', $segment->getTranslation('slug', 'ru', false));
        $this->assertSame('Коротко о сегменте', $segment->getTranslation('intro', 'ru', false));

        $this->get('/admin/segments')->assertOk()->assertSee('Начинающим');
        $this->get("/admin/segments/{$segment->id}/edit")->assertOk()->assertSee('Вступление');
    }

    public function test_service_syncs_segments(): void
    {
        $a = Segment::create(['slug' => ['ru' => 'a'], 'title' => ['ru' => 'Сегмент A']]);
        $b = Segment::create(['slug' => ['ru' => 'b'], 'title' => ['ru' => 'Сегмент B']]);
        $admin = $this->admin();

        $this->actingAs($admin)->post('/admin/services', [
            'title' => ['ru' => 'Бухгалтерия'],
            'segments' => [$a->id, $b->id],
        ])->assertSessionHasNoErrors();

        $service = Service::firstOrFail();
        $this->assertEqualsCanonicalizing([$a->id, $b->id], $service->segments()->pluck('segments.id')->all());

        $this->actingAs($admin)->get("/admin/services/{$service->id}/edit")->assertOk()->assertSee('Сегмент A');

        $this->actingAs($admin)->put("/admin/services/{$service->id}", [
            'title' => ['ru' => 'Бухгалтерия'],
            'slug' => ['ru' => 'buhgalteriya'],
        ])->assertSessionHasNoErrors();

        $this->assertSame(0, $service->segments()->count());
    }

    public function test_unknown_segment_is_rejected(): void
    {
        $this->actingAs($this->admin())->post('/admin/services', [
            'title' => ['ru' => 'Услуга'],
            'segments' => [999],
        ])->assertSessionHasErrors('segments.0');
    }
}
