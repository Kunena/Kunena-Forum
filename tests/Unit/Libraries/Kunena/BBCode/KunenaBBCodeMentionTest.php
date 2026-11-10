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
 * [mention]: the parsed content of the tag was printed into the title attribute of the link. Text is encoded by the parser, but
 * a tag inside the mention ([url]) is real markup with quotes: the quote closed the title, and the rest of the address became
 * attributes of the link (onmouseover...): stored XSS for every reader of the topic.
 */
class KunenaBBCodeMentionTest extends UnitTestCase
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

    private function mention(string $text): string
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

    public static function mentions(): array
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

    #[DataProvider('mentions')]
    public function testMentionIsOneLinkWithItsOwnAttributesOnly(string $text): void
    {
        $html  = $this->mention($text);
        $links = $this->links($html);

        $this->assertSame(1, $links->length, 'One link, nothing nested: ' . $html);

        $names = [];

        foreach ($links->item(0)->attributes as $attribute) {
            $names[] = $attribute->name;
        }

        sort($names);
        $this->assertSame(['data-bs-toggle', 'href', 'title'], $names, $html);
        $this->assertSame(0, $links->item(0)->getElementsByTagName('*')->length, 'The link holds text only: ' . $html);
    }

    public function testMentionKeepsTheNameAndTheAddress(): void
    {
        $this->assertSame(
            '<a href="/u?a=1&amp;b=2" data-bs-toggle="tooltip" title="Tom &amp; Jerry &lt;3">@Tom &amp; Jerry &lt;3</a>',
            KunenaBBCodeLibrary::renderMention('/u?a=1&amp;b=2', 'Tom &amp; Jerry &lt;3')
        );
        $this->assertSame('@Some Member', KunenaBBCodeLibrary::renderMention(false, 'Some <i>Member</i>'));
        $this->assertStringNotContainsString('" onclick="', KunenaBBCodeLibrary::renderMention('/u" onclick="alert(1)', 'x'));
    }

    public function testMentionIsRenderedByTheMethodOnly(): void
    {
        $this->assertSame(1, preg_match('/public function DoMention\([^)]*\)\s*\{(.*?)\n    \}\n/s', Files::read(self::BBCODE), $m));
        $this->assertStringContainsString('self::renderMention(', $m[1]);
        $this->assertStringNotContainsString('title="', $m[1], 'The content is never put into an attribute here.');
    }
}
