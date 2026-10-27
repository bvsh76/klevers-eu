<?php

namespace App\Support;

use Symfony\Component\HtmlSanitizer\HtmlSanitizer;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;

/**
 * Очистка HTML из админки по белому списку (под панель редактора TipTap).
 */
final class Html
{
    private static ?HtmlSanitizer $sanitizer = null;

    public static function clean(?string $html): string
    {
        if (blank($html)) {
            return '';
        }

        return self::sanitizer()->sanitize($html);
    }

    private static function sanitizer(): HtmlSanitizer
    {
        return self::$sanitizer ??= new HtmlSanitizer(
            (new HtmlSanitizerConfig)
                ->allowElement('p')
                ->allowElement('br')
                ->allowElement('h2')
                ->allowElement('h3')
                ->allowElement('h4')
                ->allowElement('strong')
                ->allowElement('em')
                ->allowElement('s')
                ->allowElement('u')
                ->allowElement('ul')
                ->allowElement('ol')
                ->allowElement('li')
                ->allowElement('blockquote')
                ->allowElement('hr')
                ->allowElement('a', ['href', 'target'])
                ->allowLinkSchemes(['https', 'http', 'mailto', 'tel'])
                ->allowRelativeLinks()
                ->forceAttribute('a', 'rel', 'noopener noreferrer')
                ->withMaxInputLength(200_000)
        );
    }
}
