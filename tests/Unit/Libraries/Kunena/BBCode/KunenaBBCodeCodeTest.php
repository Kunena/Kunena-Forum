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

use DOMDocument;
use DOMElement;
use DOMXPath;
use Kunena\Forum\Libraries\BBCode\KunenaBBCodeLibrary;
use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;
use Nbbc\BBCode;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * [code]: a standard <pre><code> block with the language as a class "language-..." for highlight.js. The content is encoded once
 * by the parser, and only a plain language name reaches the attribute.
 */
class KunenaBBCodeCodeTest extends UnitTestCase
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

    /**
     * The plain parser of NBBC (no config, no context) with the tags of Kunena.
     */
    private function parse(string $text, ?BBCode $parser = null): string
    {
        $parser ??= new BBCode(self::bare(KunenaBBCodeLibrary::class));

        return $parser->parse($text);
    }

    private function dom(string $html): DOMXPath
    {
        $previous = libxml_use_internal_errors(true);
        $dom      = new DOMDocument();
        $dom->loadHTML('<?xml encoding="UTF-8"><html><body>' . $html . '</body></html>');
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        return new DOMXPath($dom);
    }

    /**
     * The one code element of the output, in its pre and its box with the head "Code:".
     */
    private function code(string $html): DOMElement
    {
        $xpath = $this->dom($html);
        $codes = $xpath->query('//div[@class="bbcode_code"]/pre[@class="bbcode_code_body"]/code');

        $this->assertSame(1, $codes->length, $html);
        $this->assertSame('Code:', $xpath->query('//div[@class="bbcode_code"]/div[@class="bbcode_code_head"]')->item(0)->textContent);

        return $codes->item(0);
    }

    public function testIndentationIsKept(): void
    {
        $this->assertSame("    indented\n        more", $this->code($this->parse("[code]\n    indented\n        more\n[/code]"))->textContent);
    }

    public function testCodeWithoutLanguageHasNoClass(): void
    {
        $html = $this->parse('[code]x = 1;[/code]');
        $code = $this->code($html);

        $this->assertFalse($code->hasAttribute('class'));
        $this->assertSame('x = 1;', $code->textContent);
        $this->assertStringNotContainsString('style=', $html);
    }

    public static function languages(): array
    {
        return [
            'default'           => ['[code=php]echo 1;[/code]', 'language-php'],
            'type'              => ['[code type=php]echo 1;[/code]', 'language-php'],
            'uppercase'         => ['[code=PHP]echo 1;[/code]', 'language-php'],
            'uppercase type'    => ['[code type=PHP]echo 1;[/code]', 'language-php'],
            'type wins over ='  => ['[code=js type=php]echo 1;[/code]', 'language-php'],
            'digits and dashes' => ['[code=x86-asm_1]echo 1;[/code]', 'language-x86-asm_1'],
        ];
    }

    #[DataProvider('languages')]
    public function testLanguageIsTheClassOfCode(string $text, string $class): void
    {
        $html = $this->parse($text);
        $code = $this->code($html);

        $this->assertSame($class, $code->getAttribute('class'));
        $this->assertSame('echo 1;', $code->textContent);
        $this->assertStringContainsString('<pre class="bbcode_code_body"><code class="' . $class . '">echo 1;</code></pre>', $html);
    }

    public function testHtmlInCodeIsTextEncodedOnce(): void
    {
        $content = '<script>alert(1)</script><b onclick="x">b</b> & \'q\'';
        $html    = $this->parse('[code=html]' . $content . '[/code]');

        $this->assertStringContainsString(
            '&lt;script&gt;alert(1)&lt;/script&gt;&lt;b onclick=&quot;x&quot;&gt;b&lt;/b&gt; &amp; &#039;q&#039;',
            $html
        );
        $this->assertStringNotContainsString('&amp;lt;', $html);

        $xpath = $this->dom($html);
        $this->assertSame(0, $xpath->query('//script')->length);
        $this->assertSame(0, $xpath->query('//b')->length);
        $this->assertSame(0, $xpath->query('//@onclick')->length);
        $this->assertSame($content, $this->code($html)->textContent);
    }

    public function testBbcodeSmileysAndUrlsInCodeStayLiteral(): void
    {
        $content = '[b]x[/b] [url]https://example.com[/url] :)';
        $html    = $this->parse('[code]' . $content . '[/code]');

        $this->assertStringNotContainsString('<b>', $html);
        $this->assertStringNotContainsString('<a', $html);
        $this->assertStringNotContainsString('<img', $html);
        $this->assertSame($content, $this->code($html)->textContent);
    }

    public static function hostileLanguages(): array
    {
        $values = [
            'quote and handler' => 'x" onmouseover="alert(1)',
            'space and handler' => 'php onmouseover=alert(1)',
            'script'            => '<script>',
            'closing the tag'   => 'php"><img src=x onerror=alert(1)>',
            'c++'               => 'c++',
            'space'             => 'java script',
            'too long'          => str_repeat('a', 40),
            'empty'             => '',
        ];
        $cases = [];

        foreach ($values as $name => $value) {
            $cases[$name . ' as =']    = ['[code=' . $value . ']text[/code]'];
            $cases[$name . ' as type'] = ['[code type=' . $value . ']text[/code]'];
        }

        return $cases;
    }

    #[DataProvider('hostileLanguages')]
    public function testHostileLanguageNeverReachesAnAttribute(string $text): void
    {
        $html  = $this->parse($text);
        $xpath = $this->dom($html);

        $this->assertSame(1, $xpath->query('//code')->length, $html);
        $this->assertSame(0, $xpath->query('//script')->length, $html);
        $this->assertSame(0, $xpath->query('//img')->length, $html);
        $this->assertSame(0, $xpath->query('//@*[starts-with(name(), "on")]')->length, $html);

        // The parser accepts each of these tags: the block is shown with its text.
        $code = $this->code($html);
        $this->assertSame('text', $code->textContent);

        foreach ([$code, $code->parentNode] as $element) {
            foreach ($element->attributes as $attribute) {
                $this->assertSame('class', $attribute->name, $html);
            }
        }

        if ($code->hasAttribute('class')) {
            $this->assertMatchesRegularExpression('/^language-[a-z0-9_-]{1,32}$/D', $code->getAttribute('class'));
        }
    }

    public function testHostileLanguagesOfTheSpecGiveNoClass(): void
    {
        foreach (['[code type=x" onmouseover="alert(1)]text[/code]', '[code=c++]text[/code]', '[code=' . str_repeat('a', 40) . ']text[/code]'] as $text) {
            $html = $this->parse($text);
            $this->assertFalse($this->code($html)->hasAttribute('class'), $html);
        }
    }

    public static function codeLanguages(): array
    {
        return [
            'valid'            => ['php', 'php'],
            'dash, underscore' => ['objective-c_2', 'objective-c_2'],
            'trimmed'          => ["  js \n", 'js'],
            'uppercase'        => ['PHP', 'php'],
            'quote'            => ['php"', ''],
            'space inside'     => ['java script', ''],
            'plus'             => ['c++', ''],
            'dot'              => ['x.y', ''],
            'tag'              => ['<b>', ''],
            'line after'       => ["php\nx", ''],
            'empty'            => ['', ''],
            'null'             => [null, ''],
            'array'            => [['php'], ''],
            '32 characters'    => [str_repeat('a', 32), str_repeat('a', 32)],
            'too long'         => [str_repeat('a', 33), ''],
        ];
    }

    #[DataProvider('codeLanguages')]
    public function testCodeLanguage($value, string $expected): void
    {
        $this->assertSame($expected, KunenaBBCodeLibrary::codeLanguage($value));
    }

    public function testNoAssetsAndNoJoomlaForAContextEvenWithTheSettingOn(): void
    {
        // A parser of an e-mail with highlighting on: a call to Factory would fail here, with or without Joomla.
        $parser          = new class (self::bare(KunenaBBCodeLibrary::class)) extends BBCode {
            public $config;
            public $context;
        };
        $parser->config  = (object) ['highlightCode' => 1];
        $parser->context = 'subscription';

        $this->assertSame('language-php', $this->code($this->parse('[code=php]x[/code]', $parser))->getAttribute('class'));
    }

    public function testRuleOfCodeIsTheMethodWithItsPlainText(): void
    {
        $library = self::bare(KunenaBBCodeLibrary::class);
        $rule    = $library->default_tag_rules['code'];

        $this->assertSame(BBCode::BBCODE_MODE_LIBRARY, $rule['mode']);
        $this->assertSame('DoCode', $rule['method']);
        $this->assertSame(BBCode::BBCODE_VERBATIM, $rule['content']);
        $this->assertSame("\n<b>Code:</b>\n", $rule['plain_start']);
        $this->assertSame("\n", $rule['plain_end']);
        $this->assertArrayNotHasKey('template', $rule);
    }
}
