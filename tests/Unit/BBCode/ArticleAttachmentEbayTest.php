<?php

/**
 * Kunena Component
 *
 * Regression tests for three BBCode tags which show something that does not belong to the message:
 *
 *  - [article]ID[/article] loaded the article by its id and checked its view level and the state of its category, but not the state of
 *    the article (draft, trashed), its dates (not yet published, expired) or the view level of its category.
 *  - [attachment=ID] showed the link, the name and the size of any attachment when it was not shown inside a message, without asking
 *    whether the reader may see it (and ended in a fatal error when there was no attachment).
 *  - the eBay layout printed the title and the addresses of the answer of eBay, which the seller wrote, as they came.
 *
 * @package         Kunena.Tests
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 **/

namespace Kunena\Tests\Unit\BBCode;

use Kunena\Forum\Libraries\BBCode\KunenaArticleVisibility as V;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

// The data providers run before setUpBeforeClass().
if (!\defined('_JEXEC')) {
    \define('_JEXEC', 1);
}

require_once __DIR__ . '/../../../src/libraries/kunena/src/BBCode/KunenaArticleVisibility.php';

class ArticleAttachmentEbayTest extends TestCase
{
    private const BBCODE = __DIR__ . '/../../../src/libraries/kunena/src/BBCode/KunenaBBCode.php';
    private const EBAY   = __DIR__ . '/../../../src/site/template/aurelia/layouts/bbcode/ebay/default.php';
    private const NOW    = '2026-10-26 12:00:00';

    public static function setUpBeforeClass(): void
    {
        if (!\defined('_JEXEC')) {
            \define('_JEXEC', 1);
        }

        require_once __DIR__ . '/../../../src/libraries/kunena/src/BBCode/KunenaArticleVisibility.php';
    }

    // --- [article]

    public static function articles(): array
    {
        $levels = [1, 5];

        //                              state  up                      down                    catAccess levels  edit   expected
        return [
            'published'              => [1, null, null, 1, $levels, false, null],
            'published, zero dates'  => [1, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1, $levels, false, null],
            'published, in window'   => [1, '2026-01-01 00:00:00', '2027-01-01 00:00:00', 1, $levels, false, null],
            'unpublished draft'      => [0, null, null, 1, $levels, false, V::UNPUBLISHED],
            'trashed'                => [-2, null, null, 1, $levels, false, V::UNPUBLISHED],
            'archived'               => [2, null, null, 1, $levels, false, V::UNPUBLISHED],
            'not yet published'      => [1, '2026-12-01 00:00:00', null, 1, $levels, false, V::UNPUBLISHED],
            'expired'                => [1, null, '2026-10-01 00:00:00', 1, $levels, false, V::UNPUBLISHED],
            'expires right now'      => [1, null, self::NOW, 1, $levels, false, V::UNPUBLISHED],
            'starts right now'       => [1, self::NOW, null, 1, $levels, false, null],
            'category for registered' => [1, null, null, 2, $levels, false, V::CATEGORY],
            'draft in hidden category' => [0, null, null, 2, $levels, false, V::CATEGORY],
            'editor sees the draft'  => [0, null, null, 1, $levels, true, null],
            'editor sees trashed'    => [-2, null, null, 1, $levels, true, null],
            'editor sees expired'    => [1, null, '2026-10-01 00:00:00', 1, $levels, true, null],
            'editor, hidden category' => [0, null, null, 2, $levels, true, V::CATEGORY],
            'levels as strings'      => [1, null, null, 2, ['2'], false, null],
        ];
    }

    #[DataProvider('articles')]
    public function testArticleVisibility(int $state, ?string $up, ?string $down, int $catAccess, array $levels, bool $mayEdit, ?string $expected): void
    {
        $this->assertSame($expected, V::check($state, $up, $down, $catAccess, $levels, $mayEdit, self::NOW));
    }

    private function method(string $name): string
    {
        $source = file_get_contents(self::BBCODE);

        $this->assertSame(1, preg_match('/public function ' . preg_quote($name, '/') . '\([^)]*\)\s*\{(.*?)\r?\n    \}\r?\n/s', $source, $m), $name);

        return $m[1];
    }

    public function testDoArticleAsksThePolicy(): void
    {
        $body = $this->method('DoArticle');

        $this->assertStringContainsString('KunenaArticleVisibility::check(', $body);
        $this->assertStringContainsString("(int) \$article->state", $body);
        $this->assertStringContainsString('$article->publish_up', $body);
        $this->assertStringContainsString('$article->publish_down', $body);
        $this->assertStringContainsString('(int) $article->cat_access', $body);
        $this->assertStringContainsString("authorise('core.edit', \$assetId)", $body);
        $this->assertStringContainsString("authorise('core.edit.own', \$assetId)", $body);

        // The refusal comes before the text of the article is used.
        $this->assertLessThan(strpos($body, '$article->slug'), strpos($body, 'KunenaArticleVisibility::UNPUBLISHED'));
        $this->assertLessThan(strpos($body, '$article->slug'), strpos($body, 'KunenaArticleVisibility::CATEGORY'));
    }

    // --- [attachment]

    public function testAttachmentIsAuthorisedBeforeAnythingOfItIsShownInTheMinimalOutput(): void
    {
        $body = $this->method('DoAttachment');

        $start = strpos($body, '!isset($attachments) || !empty($bbcode->parent->forceMinimal)');
        $this->assertIsInt($start);

        $check = strpos($body, "isAuthorised('read')", $start);
        $this->assertIsInt($check, 'The branch asks whether the reader may see the attachment.');
        $this->assertStringContainsString('!$attachment || !$attachment->exists()', substr($body, $start, $check - $start + 40));

        foreach (['->isImage()', '->getImageLink()', '->getUrl()', '->filename', '->size'] as $use) {
            $at = strpos($body, $use, $start);

            $this->assertIsInt($at, $use);
            $this->assertLessThan($at, $check, "$use is used after the check");
        }
    }

    // --- eBay

    private function ebay(array $props): string
    {
        $view = new #[\AllowDynamicProperties] class () {
            public $naturalurl = 'https://www.ebay.com/itm/1';
            public $pictureurl = 'https://i.ebayimg.com/1.jpg';
            public $title      = 'A thing';
            public $status     = 'Active';

            public function escape($text)
            {
                return htmlspecialchars((string) $text, ENT_COMPAT, 'UTF-8');
            }
        };

        foreach ($props as $name => $value) {
            $view->$name = $value;
        }

        // The stand-in of Text is the one of the other BBCode tests (a class can only be declared once).
        require_once __DIR__ . '/fixtures/quote_stubs.php';

        $layout = self::EBAY;

        ob_start();
        (function () use ($layout) {
            include $layout;
        })->call($view);

        return ob_get_clean();
    }

    public function testNormalItemIsShown(): void
    {
        $html = $this->ebay([]);

        $this->assertStringContainsString('href="https://www.ebay.com/itm/1"', $html);
        $this->assertStringContainsString('src="https://i.ebayimg.com/1.jpg"', $html);
        $this->assertStringContainsString('>A thing</a>', $html);
    }

    public function testTitleIsText(): void
    {
        $html = $this->ebay(['title' => '<script>alert(1)</script><img src=x onerror=alert(2)>']);

        $this->assertStringNotContainsString('<script>', $html);
        $this->assertStringNotContainsString('<img src=x', $html);
        $this->assertStringContainsString('&lt;script&gt;', $html);
    }

    public function testAddressesCannotBreakOutOrRunScript(): void
    {
        $html = $this->ebay(['naturalurl' => 'https://x/"onmouseover="alert(1)', 'pictureurl' => 'https://x/"><script>alert(1)</script>']);

        $this->assertStringNotContainsString('<script>', $html);
        $this->assertStringNotContainsString('"onmouseover=', $html);
        $this->assertStringContainsString('&quot;', $html);
    }

    #[DataProvider('badSchemes')]
    public function testOnlyWebAddressesAreLinked(string $url): void
    {
        $html = $this->ebay(['naturalurl' => $url, 'pictureurl' => $url]);

        $this->assertStringNotContainsStringIgnoringCase('javascript:', $html);
        $this->assertStringNotContainsStringIgnoringCase('data:', $html);
        $this->assertStringContainsString('href=""', $html);
    }

    public static function badSchemes(): array
    {
        return [
            'javascript'  => ['javascript:alert(1)'],
            'upper case'  => ['JaVaScRiPt:alert(1)'],
            'data'        => ['data:text/html,<script>alert(1)</script>'],
            'relative'    => ['//evil.example/x'],
            'empty'       => [''],
        ];
    }
}
