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

namespace Kunena\Tests\Unit\Component\Site\Template\Aurelia;

use Closure;
use DOMDocument;
use DOMElement;
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use ReflectionProperty;

/**
 * The image of [img alt=... float=...]: the words of alt and the float come from the message, they are attribute values only. The
 * alt text is taken from the tag as it was written (KunenaBBCode), quotes and angle brackets included.
 */
class BBCodeImageTest extends UnitTestCase
{
    private mixed $savedConfig;

    public static function layouts(): array
    {
        return [
            'aurelia' => ['src/site/template/aurelia/layouts/bbcode/image/default.php'],
            'system'  => ['src/site/template/system/layouts/bbcode/image/default.php'],
        ];
    }

    protected function setUp(): void
    {
        $this->requireJoomla();

        if (!\defined('_JEXEC')) {
            \define('_JEXEC', 1);
        }

        $this->savedConfig = (new ReflectionProperty(KunenaConfig::class, 'instance'))->getValue();
        (new ReflectionProperty(KunenaConfig::class, 'instance'))->setValue(null, $this->bare(KunenaConfig::class, ['config' => ['lightbox' => 1, 'imageHeight' => 800]]));
    }

    protected function tearDown(): void
    {
        (new ReflectionProperty(KunenaConfig::class, 'instance'))->setValue(null, $this->savedConfig);
    }

    private function render(string $layoutFile, mixed $alt, mixed $float = 0): string
    {
        $layout = new class ($alt, $float) {
            public string $title = 'Attachment';

            public string $url = 'https://example.org/a.png';

            public int $size = 0;

            public bool $canLink = true;

            public function __construct(public mixed $alt, public mixed $float)
            {
            }

            public function subLayout(string $name): string
            {
                return '';
            }

            public function escape(string $value): string
            {
                return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
            }
        };

        $file   = Files::path($layoutFile);
        $render = function () use ($file): void {
            include $file;
        };

        ob_start();

        try {
            Closure::bind($render, $layout)();
        } finally {
            $output = ob_get_clean();
        }

        return $output;
    }

    /**
     * The elements of the output, as a browser reads it.
     */
    private function elements(string $html): array
    {
        $document = new DOMDocument();
        @$document->loadHTML('<!doctype html><html><body>' . $html . '</body></html>');

        return array_map(static fn (DOMElement $element) => $element->tagName, iterator_to_array($document->getElementsByTagName('body')->item(0)->getElementsByTagName('*')));
    }

    #[DataProvider('layouts')]
    public function testTheAltTextIsAnAttributeValueOnly(string $layoutFile): void
    {
        $output = $this->render($layoutFile, '"><script>alert(1)</script>');

        $this->assertSame(['div', 'a', 'img'], $this->elements($output));
        $this->assertStringNotContainsString('<script', $output);
        $this->assertStringContainsString('title="&quot;&gt;&lt;script&gt;alert(1)&lt;/script&gt;"', $output);
        $this->assertSame(1, substr_count($output, ' alt='), 'one alt attribute');
    }

    #[DataProvider('layouts')]
    public function testWithoutAltTheLinkHasNoTitle(string $layoutFile): void
    {
        $output = $this->render($layoutFile, 0);

        $this->assertStringNotContainsString('title="0"', $output);
        $this->assertStringNotContainsString(' title=', $output);
        $this->assertStringContainsString('alt="Attachment"', $output);
    }

    #[DataProvider('layouts')]
    public function testTheFloatIsLeftOrRightOnly(string $layoutFile): void
    {
        $this->assertSame(['div', 'a', 'img'], $this->elements($this->render($layoutFile, 0, 'left"><script>alert(1)</script>')));
        $this->assertStringNotContainsString('script', $this->render($layoutFile, 0, 'left;background:url(x)"'));
        $this->assertStringNotContainsString('float', $this->render($layoutFile, 0, 'left;background:url(x)"'));
    }

    public function testAFloatImageFloatsInAurelia(): void
    {
        $output = $this->render('src/site/template/aurelia/layouts/bbcode/image/default.php', 0, 'right');

        $this->assertStringContainsString('float:right;', $output);
        $this->assertStringContainsString('class="kmsgimage float"', $output);
    }

    #[DataProvider('layouts')]
    public function testTheLinkOpensInTheLightbox(string $layoutFile): void
    {
        $this->assertStringContainsString(' data-kunena-lightbox="gallery"', $this->render($layoutFile, 'photo'));
    }
}
