<?php

/**
 * Kunena Component
 *
 *  - The eBay layout of the template system (the fallback of every template which has none of its own) printed the title and the
 *    addresses of the answer of eBay as they came, like the layout of aurelia did.
 *  - The task "delete attachments" of the profile asked for the removal of the ids [0, 1] (array_keys of a list gives the places, not
 *    the ids), marked the removal before it asked whether the user may do it, and for an attachment that was never added to a message
 *    it saved the empty message: that creates a topic and a message that do not exist.
 *
 * @package         Kunena.Tests
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 **/

namespace Kunena\Tests\Unit\BBCode;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class SystemEbayAndDelfileTest extends TestCase
{
    private const EBAY       = __DIR__ . '/../../../src/site/template/system/layouts/bbcode/ebay/default.php';
    private const CONTROLLER = __DIR__ . '/../../../src/site/src/Controllers/UserController.php';

    protected function setUp(): void
    {
        if (!\defined('_JEXEC')) {
            \define('_JEXEC', 1);
        }

        require_once __DIR__ . '/fixtures/quote_stubs.php';
    }

    private function ebay(array $props): string
    {
        $view = new #[\AllowDynamicProperties] class () {
            public $ack        = 'Success';
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

    public function testAddressesCannotBreakOut(): void
    {
        $html = $this->ebay(['naturalurl' => 'https://x/"onmouseover="alert(1)', 'pictureurl' => 'https://x/"><script>alert(1)</script>']);

        $this->assertStringNotContainsString('<script>', $html);
        $this->assertStringNotContainsString('"onmouseover=', $html);
    }

    public static function badSchemes(): array
    {
        return [['javascript:alert(1)'], ['JaVaScRiPt:alert(1)'], ['data:text/html,x'], ['//evil.example/x'], ['']];
    }

    #[DataProvider('badSchemes')]
    public function testOnlyWebAddressesAreLinked(string $url): void
    {
        $html = $this->ebay(['naturalurl' => $url, 'pictureurl' => $url]);

        $this->assertStringNotContainsStringIgnoringCase('javascript:', $html);
        $this->assertStringNotContainsStringIgnoringCase('data:', $html);
        $this->assertStringContainsString('href=""', $html);
    }

    // --- delfile

    private function delfile(): string
    {
        $source = file_get_contents(self::CONTROLLER);

        $this->assertSame(1, preg_match('/public function delfile\([^)]*\)[^{]*\{(.*?)\r?\n    \}\r?\n/s', $source, $m));

        return $m[1];
    }

    public function testDelfileNoLongerBuildsTheIdsFromThePlaces(): void
    {
        $body = $this->delfile();

        $this->assertStringNotContainsString('array_keys(array_diff_key', $body);
        $this->assertStringNotContainsString('removeAttachments(', $body, 'The attachment is deleted by itself; nothing is marked first.');
        $this->assertStringNotContainsString('[$attachment->id, 1]', $body);
    }

    public function testDelfileAsksForThePermissionBeforeItChangesAnything(): void
    {
        $body = $this->delfile();

        $check = strpos($body, "isAuthorised('delete')");
        $this->assertIsInt($check);
        $this->assertStringContainsString('!$attachment->exists()', substr($body, max(0, $check - 60), 120));

        foreach (['->delete()', '->save(', 'getMessage()'] as $use) {
            $at = strpos($body, $use);

            $this->assertIsInt($at, $use);
            $this->assertLessThan($at, $check, "$use is after the check");
        }
    }

    public function testDelfileDoesNotSaveAMessageThatDoesNotExist(): void
    {
        $body = $this->delfile();

        $exists = strpos($body, '$message->exists()');
        $this->assertIsInt($exists);
        $this->assertLessThan(strpos($body, '$message->save()'), $exists, 'The message is saved inside the check that it exists.');
        $this->assertLessThan(strpos($body, '$topic->save(false)'), $exists);
        $this->assertStringContainsString('$topic->exists()', $body);
    }
}
