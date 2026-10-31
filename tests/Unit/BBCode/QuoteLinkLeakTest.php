<?php

/**
 * Kunena Component
 *
 * Regression test for the link of [quote post=<id>]. The address is built by the router from the subject of the topic and the
 * alias of the category without any check of access, so anybody who wrote [quote post=<id>] with the id of a message of a
 * private category got the title of its topic back in the link. The link is only built for a message which exists and which the
 * reader may read.
 *
 * @package         Kunena.Tests
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 **/

namespace Kunena\Tests\Unit\BBCode;

use Kunena\Forum\Libraries\BBCode\KunenaBBCodeLibrary;
use Kunena\Forum\Libraries\Forum\Message\KunenaMessageHelper;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class QuoteLinkLeakTest extends TestCase
{
    private const BBCODE = __DIR__ . '/../../../src/libraries/kunena/src/BBCode/KunenaBBCode.php';
    private const LAYOUT = __DIR__ . '/../../../src/site/template/aurelia/layouts/bbcode/quote/default.php';

    private static function load(): void
    {
        if (!\defined('_JEXEC')) {
            \define('_JEXEC', 1);
        }

        if (!\defined('KPATH_FRAMEWORK')) {
            \define('KPATH_FRAMEWORK', realpath(__DIR__ . '/../../../src/libraries/kunena'));
        }

        if (!class_exists(KunenaMessageHelper::class, false)) {
            require_once __DIR__ . '/fixtures/quote_stubs.php';
        }

        spl_autoload_register(static function ($class) {
            if (strpos($class, 'Nbbc\\') === 0) {
                $file = KPATH_FRAMEWORK . '/External/Nbbc/src/' . substr($class, 5) . '.php';

                if (is_file($file)) {
                    require_once $file;
                }
            }
        });

        require_once self::BBCODE;
    }

    protected function setUp(): void
    {
        self::load();
        KunenaMessageHelper::$messages = [];
        KunenaMessageHelper::$urlCalls = [];
    }

    private function quote(string $default, string $content = 'text'): string
    {
        $library = (new \ReflectionClass(KunenaBBCodeLibrary::class))->newInstanceWithoutConstructor();

        return (string) $library->DoQuote(null, 2, 'quote', $default, [], $content);
    }

    public function testMessageTheReaderMayReadGetsItsLink(): void
    {
        KunenaMessageHelper::$messages[5] = KunenaMessageHelper::message(true, true, '/forum/1-general/7-hello?mesid=5');

        $html = $this->quote('"Some Member userid=3 post=5"');

        $this->assertStringContainsString('href="https://forum.example/forum/1-general/7-hello?mesid=5"', $html);
    }

    public function testMessageOfAPrivateCategoryGetsNoLinkAndNoAddress(): void
    {
        KunenaMessageHelper::$messages[6] = KunenaMessageHelper::message(true, false, '/forum/9-staff/12-secret-salary-plans');

        $html = $this->quote('"Some Member userid=3 post=6"');

        $this->assertStringNotContainsString('secret-salary-plans', $html);
        $this->assertStringNotContainsString('<a ', $html);
        $this->assertSame([], KunenaMessageHelper::$urlCalls, 'The router must not even be asked for the address.');
    }

    public function testMissingMessageGetsNoLink(): void
    {
        $html = $this->quote('"Some Member userid=3 post=999"');

        $this->assertStringNotContainsString('<a ', $html);
        $this->assertSame([], KunenaMessageHelper::$urlCalls);
    }

    public function testQuoteStillShowsTheTextWithoutALink(): void
    {
        KunenaMessageHelper::$messages[6] = KunenaMessageHelper::message(true, false, '/x');

        $html = $this->quote('"Some Member userid=3 post=6"', 'quoted words');

        $this->assertStringContainsString('quoted words', $html);
        $this->assertStringContainsString('Some Member', $html);
    }

    public function testQuoteWithoutAPostNeverGetsALink(): void
    {
        $html = $this->quote('"Some Member userid=3"');

        $this->assertStringNotContainsString('<a ', $html);
        $this->assertSame([], KunenaMessageHelper::$urlCalls);
    }

    public static function addresses(): array
    {
        return [
            'quote'  => ['/a"onmouseover="alert(1)'],
            'angle'  => ['/a"><script>alert(1)</script>'],
        ];
    }

    #[DataProvider('addresses')]
    public function testLayoutEscapesTheAddress(string $address): void
    {
        KunenaMessageHelper::$messages[5] = KunenaMessageHelper::message(true, true, $address);

        $html = $this->quote('"Some Member userid=3 post=5"');

        $this->assertStringNotContainsString('<script>', $html);
        $this->assertStringNotContainsString('" onmouseover', $html);
        $this->assertDoesNotMatchRegularExpression('/<a [^>]*"onmouseover=/', $html);
    }

    public function testSourceOnlyBuildsTheAddressForAReadableMessage(): void
    {
        $source = file_get_contents(self::BBCODE);

        $this->assertSame(1, preg_match('/public function DoQuote\([^)]*\)\s*\{(.*?)\n    \}\n/s', $source, $m));
        $body = $m[1];

        $this->assertMatchesRegularExpression('/\$msglink\s*=\s*\'\';\s*\n\s*if \(\$postid > 0\)/', $body, 'The link starts empty.');
        $this->assertMatchesRegularExpression('/exists\(\)\s*&&\s*\$message->isAuthorised\(\'read\'\).{0,200}getUrl\(/s', $body, 'getUrl() only after the read check.');
        $this->assertSame(1, substr_count($body, 'getUrl('), 'One place builds the address.');
        $this->assertStringContainsString('$this->escape($this->msglink)', file_get_contents(self::LAYOUT));
    }
}
