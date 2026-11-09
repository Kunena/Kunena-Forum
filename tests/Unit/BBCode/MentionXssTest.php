<?php

/**
 * Kunena Component
 *
 * Regression test for [mention]. The parsed content of the tag was printed into the title attribute of the link as it was. Text
 * is encoded by the parser, but a tag inside the mention is real markup: the quote of the href of a nested [url] closed the title,
 * and the rest of the address became attributes of the link of the mention
 * ([mention userid=1][url=http://a.com/onmouseover=alert(1)//]x[/url][/mention]): stored XSS for every reader of the topic.
 * Only the text of the content is used now, encoded once.
 *
 * @package         Kunena.Tests
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 **/

namespace Kunena\Tests\Unit\BBCode;

use Kunena\Forum\Libraries\BBCode\KunenaBBCodeLibrary;
use Kunena\Forum\Libraries\Forum\Message\KunenaMessageHelper;
use Nbbc\BBCode;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class MentionXssTest extends TestCase
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
     * The real parser with its own [url], [b]... and a mention which is rendered by the method of Kunena.
     */
    private function parse(string $text): string
    {
        $bbcode = new BBCode();
        $bbcode->addRule('mention', [
            'mode'     => BBCode::BBCODE_MODE_CALLBACK,
            'method'   => static function ($bbcode, $action, $name, $default, $params, $content) {
                if ($action == BBCode::BBCODE_CHECK) {
                    return true;
                }

                return KunenaBBCodeLibrary::renderMention('/forum/user/1-admin', (string) $content);
            },
            'class'    => 'block',
            'allow_in' => ['listitem', 'block', 'columns'],
            'content'  => BBCode::BBCODE_REQUIRED,
        ]);

        return (string) $bbcode->parse($text);
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

    public static function payloads(): array
    {
        return [
            'nested url, handler in the path' => ['[mention userid=1][url=http://a.com/onmouseover=alert(1)//]x[/url][/mention]'],
            'nested url, autofocus'           => ['[mention userid=1][url=http://a.com/autofocus/onfocus=alert(1)//]x[/url][/mention]'],
            'nested url without a default'    => ['[mention userid=1][url]http://a.com/onmouseover=alert(1)//[/url][/mention]'],
            'nested bold'                     => ['[mention userid=1][b]name[/b][/mention]'],
            'double quote'                    => ['[mention userid=1]a" onmouseover="alert(1)[/mention]'],
            'single quote'                    => ["[mention userid=1]a' onmouseover='alert(1)[/mention]"],
            'markup as text'                  => ['[mention userid=1]<img src=x onerror=alert(1)>[/mention]'],
        ];
    }

    #[DataProvider('payloads')]
    public function testMentionIsOneLinkWithItsOwnAttributesOnly(string $text): void
    {
        $html  = $this->parse($text);
        $links = $this->links($html);

        $this->assertSame(1, $links->length, 'One link, nothing nested: ' . $html);

        $names = [];

        foreach ($links->item(0)->attributes as $attribute) {
            $names[] = $attribute->name;
        }

        sort($names);
        $this->assertSame(['data-bs-toggle', 'href', 'title'], $names, $html);
        $this->assertSame('/forum/user/1-admin', $links->item(0)->getAttribute('href'));
        $this->assertSame(0, $links->item(0)->getElementsByTagName('*')->length, 'The link holds text only: ' . $html);
        $this->assertDoesNotMatchRegularExpression('/<a [^>]*title="[^"]*</', $html, 'No markup inside the title.');
    }

    public function testNameIsKeptAsText(): void
    {
        $html = KunenaBBCodeLibrary::renderMention('/u', 'Tom &amp; Jerry &lt;3');

        $this->assertSame('<a href="/u" data-bs-toggle="tooltip" title="Tom &amp; Jerry &lt;3">@Tom &amp; Jerry &lt;3</a>', $html);
    }

    public function testAddressOfTheRouterIsNotEncodedTwiceAndCanNotBreakOut(): void
    {
        $this->assertStringContainsString('href="/index.php?a=1&amp;b=2"', KunenaBBCodeLibrary::renderMention('/index.php?a=1&amp;b=2', 'x'));

        $html = KunenaBBCodeLibrary::renderMention('/u" onclick="alert(1)', 'x');

        $this->assertStringNotContainsString('" onclick="', $html);
        $this->assertSame(1, $this->links($html)->length);
        $this->assertFalse($this->links($html)->item(0)->hasAttribute('onclick'));
    }

    public static function noAddresses(): array
    {
        return [[false], [null], ['']];
    }

    #[DataProvider('noAddresses')]
    public function testWithoutAProfileThereIsTheNameAndNoLink(mixed $url): void
    {
        $this->assertSame('@Some &lt;b&gt; Member', KunenaBBCodeLibrary::renderMention($url, 'Some &lt;b&gt; <i>Member</i>'));
    }

    public function testTagIsRenderedByTheMethodOnly(): void
    {
        $source = file_get_contents(self::BBCODE);

        $this->assertSame(1, preg_match('/public function DoMention\([^)]*\)\s*\{(.*?)\r?\n    \}\r?\n/s', $source, $m));
        $this->assertStringContainsString('self::renderMention(', $m[1]);
        $this->assertStringNotContainsString('title="', $m[1], 'The content is never put into an attribute here.');
        $this->assertStringNotContainsString("\$params['userid'])", $m[1], 'The id of the user is a number.');
    }
}
