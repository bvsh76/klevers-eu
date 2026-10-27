<?php

namespace Tests\Feature;

use App\Enums\PricePeriod;
use App\Enums\Role;
use App\Models\Package;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPackagesTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $user = User::factory()->create();
        $user->role = Role::Admin;
        $user->save();

        return $user;
    }

    public function test_store_package_with_features_and_comma_price(): void
    {
        $service = Service::create(['slug' => ['ru' => 'buh'], 'title' => ['ru' => 'Бухгалтерия']]);

        $this->actingAs($this->admin())->post('/admin/packages', [
            'title' => ['ru' => 'Старт', 'en' => 'Start'],
            'features' => ['ru' => "До 10 документов\r\n\r\n  Годовой отчёт  ", 'en' => ''],
            'service_id' => $service->id,
            'price' => '49,90',
            'price_period' => 'month',
            'is_active' => '1',
        ])->assertSessionHasNoErrors();

        $package = Package::firstOrFail();
        $this->assertSame(['До 10 документов', 'Годовой отчёт'], $package->getTranslation('features', 'ru', false));
        $this->assertSame(['ru'], array_keys($package->getTranslations('features')));
        $this->assertSame('49.90', $package->price);
        $this->assertSame(PricePeriod::Month, $package->price_period);
        $this->assertSame($service->id, $package->service_id);

        $this->get('/admin/packages')->assertOk()->assertSee('49.90 € в месяц');
        $this->get("/admin/packages/{$package->id}/edit")->assertOk()->assertSee("До 10 документов\nГодовой отчёт", false);
    }

    public function test_price_requires_period_and_empty_price_means_on_request(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->post('/admin/packages', [
            'title' => ['ru' => 'Пакет'],
            'price' => '100',
        ])->assertSessionHasErrors('price_period');

        $this->actingAs($admin)->post('/admin/packages', [
            'title' => ['ru' => 'Индивидуальный'],
            'price' => '',
            'price_period' => 'month',
        ])->assertSessionHasNoErrors();

        $package = Package::firstOrFail();
        $this->assertTrue($package->isOnRequest());
        $this->assertNull($package->price_period);
        $this->actingAs($admin)->get('/admin/packages')->assertSee('по запросу');
    }

    public function test_delete_package(): void
    {
        $package = Package::create(['title' => ['ru' => 'Временный']]);

        $this->actingAs($this->admin())->delete("/admin/packages/{$package->id}")
            ->assertRedirect(route('admin.packages.index'));

        $this->assertModelMissing($package);
    }
}
