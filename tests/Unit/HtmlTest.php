<?php

namespace Tests\Unit;

use App\Support\Html;
use Tests\TestCase;

class HtmlTest extends TestCase
{
    public function test_keeps_allowed_markup(): void
    {
        $html = Html::clean('<h2>Заголовок</h2><p><strong>жирный</strong> и <em>курсив</em></p><ul><li>пункт</li></ul>');

        $this->assertSame('<h2>Заголовок</h2><p><strong>жирный</strong> и <em>курсив</em></p><ul><li>пункт</li></ul>', $html);
    }

    public function test_removes_scripts_handlers_and_styles(): void
    {
        $html = Html::clean('<p onclick="alert(1)" style="color:red" class="x">текст</p><script>alert(1)</script><iframe src="https://evil.test"></iframe>');

        $this->assertSame('<p>текст</p>', $html);
    }

    public function test_links_are_safe(): void
    {
        $this->assertStringNotContainsString('javascript:', Html::clean('<a href="javascript:alert(1)">x</a>'));
        $this->assertStringContainsString('rel="noopener noreferrer"', Html::clean('<a href="https://klevers.eu">x</a>'));
        $this->assertStringContainsString('href="/ru/kontakty"', Html::clean('<a href="/ru/kontakty">x</a>'));
        $this->assertStringContainsString('href="mailto:info&#64;klevers.eu"', Html::clean('<a href="mailto:info@klevers.eu">x</a>'));
    }

    public function test_empty_input(): void
    {
        $this->assertSame('', Html::clean(null));
        $this->assertSame('', Html::clean(''));
    }
}
