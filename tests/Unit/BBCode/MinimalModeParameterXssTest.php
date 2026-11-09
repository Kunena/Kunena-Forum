<?php

/**
 * Kunena Component
 *
 * Regression test for the short output of [spoiler] and [file] in an activity stream (forceMinimal, used by the JomSocial
 * plugin). The parser encodes the content of a tag, but it hands the parameters over as the member wrote them, and these two
 * printed a parameter as it was: [spoiler="<img src=x onerror=alert(1)>"] and [file name="<img ...>"] were stored XSS in the
 * stream.
 *
 * @package         Kunena.Tests
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 **/

namespace Kunena\Tests\Unit\BBCode;

use Kunena\Forum\Libraries\BBCode\KunenaBBCodeLibrary;
use Kunena\Forum\Libraries\Forum\Message\KunenaMessageHelper;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class MinimalModeParameterXssTest extends TestCase
{
    private const BBCODE = __DIR__ . '/../../../src/libraries/kunena/src/BBCode/KunenaBBCode.php';

    protected function setUp(): void
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

    /**
     * The parser as a tag sees it in an activity stream.
     */
    private function minimal(): object
    {
        return (object) [
            'parent'          => (object) ['forceMinimal' => true, 'forceSecure' => true],
            'lost_start_tags' => [],
            'was_limited'     => false,
            'context'         => '',
        ];
    }

    private function library(): KunenaBBCodeLibrary
    {
        return (new \ReflectionClass(KunenaBBCodeLibrary::class))->newInstanceWithoutConstructor();
    }

    public static function payloads(): array
    {
        return [
            'image with a handler' => ['<img src=x onerror=alert(1)>'],
            'script'               => ['<script>alert(1)</script>'],
            'svg'                  => ['<svg onload=alert(1)>'],
            'quotes'               => ['a" onmouseover="alert(1)'],
        ];
    }

    #[DataProvider('payloads')]
    public function testSpoilerTitleIsText(string $title): void
    {
        $html = (string) $this->library()->DoSpoiler($this->minimal(), 2, 'spoiler', $title, [], 'hidden words');

        $this->assertStringNotContainsString('<', $html);
        $this->assertStringNotContainsString('"', $html);
        $this->assertSame('[' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . ']', $html);
    }

    public function testSpoilerKeepsAPlainTitleAndNeverShowsTheContent(): void
    {
        $html = (string) $this->library()->DoSpoiler($this->minimal(), 2, 'spoiler', 'The ending', [], 'hidden words');

        $this->assertSame('[The ending]', $html);
    }

    #[DataProvider('payloads')]
    public function testFileNameParameterIsText(string $name): void
    {
        $html = (string) $this->library()->DoFile($this->minimal(), 2, 'file', false, ['name' => $name], 'x');

        $this->assertStringNotContainsString('<', $html);
        $this->assertStringNotContainsString('"', $html);
        $this->assertStringStartsWith('[ ', $html);
        $this->assertStringEndsWith(' ]', $html);
    }

    public function testFileKeepsAPlainNameWithoutItsPath(): void
    {
        $this->assertSame('[ report.pdf ]', (string) $this->library()->DoFile($this->minimal(), 2, 'file', false, ['name' => 'some/dir/report.pdf'], 'x'));
    }

    public function testFileWithoutANameUsesTheContentOfTheParserOnce(): void
    {
        // The content is encoded by the parser already: it is not encoded twice.
        $html = (string) $this->library()->DoFile($this->minimal(), 2, 'file', false, [], 'a &amp; b.pdf');

        $this->assertSame('[ a &amp; b.pdf ]', $html);
    }
}
