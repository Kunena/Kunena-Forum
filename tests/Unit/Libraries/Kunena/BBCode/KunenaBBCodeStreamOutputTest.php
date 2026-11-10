<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Tests
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Tests\Unit\Libraries\Kunena\BBCode;

use Kunena\Forum\Libraries\BBCode\KunenaBBCodeLibrary;
use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;
use Nbbc\BBCode;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * The short output of [spoiler] and [file] in an activity stream (forceMinimal, used by the JomSocial plugin). The parser encodes
 * the content of a tag, but it hands the parameters over as the member wrote them, and these two printed a parameter as it was.
 */
class KunenaBBCodeStreamOutputTest extends UnitTestCase
{
    private const BBCODE = 'src/libraries/kunena/src/BBCode/KunenaBBCode.php';

    protected function setUp(): void
    {
        // The parser is in the folder External of the library, and the library of the tags is in the file of KunenaBBCode.
        spl_autoload_register(static function ($class) {
            if (strpos($class, 'Nbbc\\') === 0) {
                $file = Files::path('src/libraries/kunena/External/Nbbc/src/' . substr($class, 5) . '.php');

                if (is_file($file)) {
                    require_once $file;
                }
            }
        });

        // The file of KunenaBBCode loads the parser from the folder of the library.
        if (!\defined('KPATH_FRAMEWORK')) {
            \define('KPATH_FRAMEWORK', Files::path('src/libraries/kunena'));
        }

        if (!class_exists(KunenaBBCodeLibrary::class, false)) {
            require_once Files::path(self::BBCODE);
        }
    }

    private function links(string $html): \DOMNodeList
    {
        $previous = libxml_use_internal_errors(true);
        $dom      = new \DOMDocument();
        $dom->loadHTML('<?xml encoding="UTF-8"><html><body>' . $html . '</body></html>');
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        return $dom->getElementsByTagName('a');
    }

    /**
     * A layout of the template with its variables (the contract of KunenaLayout: properties, and escape()).
     */
    private function layout(string $file, array $properties): string
    {
        $view = new #[\AllowDynamicProperties] class () {
            public function escape($text)
            {
                return htmlspecialchars((string) $text, ENT_COMPAT, 'UTF-8');
            }
        };

        foreach ($properties as $name => $value) {
            $view->$name = $value;
        }

        $path = Files::path($file);

        ob_start();
        (function () use ($path) {
            include $path;
        })->call($view);

        return (string) ob_get_clean();
    }

    private function minimal(): object
    {
        return (object) [
            'parent'          => (object) ['forceMinimal' => true, 'forceSecure' => true],
            'lost_start_tags' => [],
            'was_limited'     => false,
            'context'         => '',
        ];
    }

    public static function payloads(): array
    {
        return [
            'image with a handler' => ['<img src=x onerror=alert(1)>'],
            'svg'                  => ['<svg onload=alert(1)>'],
            'quotes'               => ['a" onmouseover="alert(1)'],
        ];
    }

    #[DataProvider('payloads')]
    public function testSpoilerTitleIsTextInAStream(string $title): void
    {
        $html = (string) self::bare(KunenaBBCodeLibrary::class)->DoSpoiler($this->minimal(), 2, 'spoiler', $title, [], 'hidden words');

        $this->assertSame('[' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . ']', $html);
    }

    #[DataProvider('payloads')]
    public function testFileNameIsTextInAStream(string $name): void
    {
        $html = (string) self::bare(KunenaBBCodeLibrary::class)->DoFile($this->minimal(), 2, 'file', false, ['name' => $name], 'x');

        $this->assertStringNotContainsString('<', $html);
        $this->assertStringNotContainsString('"', $html);
        $this->assertStringStartsWith('[ ', $html);
    }

    public function testPlainTitleAndNameAreKept(): void
    {
        $library = self::bare(KunenaBBCodeLibrary::class);

        $this->assertSame('[The ending]', (string) $library->DoSpoiler($this->minimal(), 2, 'spoiler', 'The ending', [], 'hidden words'));
        $this->assertSame('[ report.pdf ]', (string) $library->DoFile($this->minimal(), 2, 'file', false, ['name' => 'some/dir/report.pdf'], 'x'));
        $this->assertSame('[ a &amp; b.pdf ]', (string) $library->DoFile($this->minimal(), 2, 'file', false, [], 'a &amp; b.pdf'), 'The content is encoded once.');
    }
}
