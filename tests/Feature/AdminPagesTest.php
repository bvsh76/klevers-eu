<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPagesTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $user = User::factory()->create();
        $user->role = Role::Admin;
        $user->save();

        return $user;
    }

    private function page(array $slug, ?string $key = null): Page
    {
        return Page::create(['key' => $key, 'slug' => $slug, 'title' => ['ru' => 'Страница '.$slug['ru']], 'is_published' => true]);
    }

    public function test_guest_cannot_open_pages(): void
    {
        $this->get('/admin/pages')->assertRedirect(route('admin.login'));
    }

    public function test_index_lists_pages(): void
    {
        $this->page(['ru' => 'o-nas', 'en' => 'about']);

        $this->actingAs($this->admin())->get('/admin/pages')
            ->assertOk()->assertSee('Страница o-nas')->assertSee('EN: about');
    }

    public function test_store_generates_slug_from_default_title(): void
    {
        $this->actingAs($this->admin())->post('/admin/pages', [
            'title' => ['ru' => 'Наши услуги', 'et' => '', 'en' => 'Our services'],
            'slug' => ['ru' => '', 'et' => '', 'en' => ''],
            'is_published' => '1',
            'sort' => '3',
        ])->assertSessionHasNoErrors()->assertRedirect();

        $page = Page::firstOrFail();
        $this->assertSame('nashi-uslugi', $page->getTranslation('slug', 'ru', false));
        $this->assertSame('our-services', $page->getTranslation('slug', 'en', false));
        $this->assertSame('', $page->getTranslation('slug', 'et', false));
        $this->assertTrue($page->is_published);
        $this->assertSame(3, $page->sort);
    }

    public function test_duplicate_and_invalid_slugs_are_rejected(): void
    {
        $this->page(['ru' => 'kontakty']);

        $this->actingAs($this->admin())->post('/admin/pages', [
            'title' => ['ru' => 'Другая', 'en' => 'Other'],
            'slug' => ['ru' => 'kontakty', 'en' => 'Bad Slug!'],
        ])->assertSessionHasErrors(['slug.ru', 'slug.en']);

        $this->assertSame(1, Page::count());
    }

    public function test_title_in_default_locale_is_required(): void
    {
        $this->actingAs($this->admin())->post('/admin/pages', [
            'title' => ['ru' => '', 'en' => 'Only English'],
        ])->assertSessionHasErrors(['title.ru', 'slug.ru']);
    }

    public function test_update_replaces_translations(): void
    {
        $page = $this->page(['ru' => 'staroe', 'et' => 'vana']);
        $page->setTranslation('title', 'et', 'Vana')->save();

        $this->actingAs($this->admin())->put("/admin/pages/{$page->id}", [
            'title' => ['ru' => 'Новое', 'et' => ''],
            'slug' => ['ru' => 'novoe', 'et' => ''],
        ])->assertSessionHasNoErrors();

        $page->refresh();
        $this->assertSame('novoe', $page->getTranslation('slug', 'ru', false));
        $this->assertSame(['ru'], array_keys($page->getTranslations('title')));
        $this->assertSame(['ru'], array_keys($page->getTranslations('slug')));
    }

    public function test_system_page_cannot_be_deleted_but_regular_can(): void
    {
        $system = $this->page(['ru' => 'glavnaya'], 'home');
        $regular = $this->page(['ru' => 'vremennaya']);
        $admin = $this->admin();

        $this->actingAs($admin)->delete("/admin/pages/{$system->id}")->assertSessionHasErrors('delete');
        $this->actingAs($admin)->delete("/admin/pages/{$regular->id}")->assertRedirect(route('admin.pages.index'));

        $this->assertModelExists($system);
        $this->assertModelMissing($regular);
    }
}
