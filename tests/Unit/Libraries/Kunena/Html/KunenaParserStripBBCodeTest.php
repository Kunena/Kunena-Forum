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

namespace Kunena\Tests\Unit\Libraries\Kunena\Html;

use Joomla\CMS\Application\CMSApplicationInterface;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Language;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Uri\Uri;
use Joomla\Event\Dispatcher;
use Joomla\Input\Input;
use Joomla\Registry\Registry;
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\BBCode\KunenaBBCode;
use Kunena\Forum\Libraries\Html\KunenaParser;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Forum\Libraries\Template\KunenaTemplate;
use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use ReflectionProperty;

/**
 * The plain text of a post (stripBBCode()), from which the tooltips of the links, the description of a topic, the feeds and the
 * search index are made: the text in [color] and [url] was taken out with its tags (a post in colour or which started with a
 * link had an empty tooltip and description), and the blocks which are left out were only found in lower case and not in a
 * block of the same tag (the text of [QUOTE] and [SPOILER] was shown, and the text after a quote in a quote).
 */
class KunenaParserStripBBCodeTest extends UnitTestCase
{
    private const HOST = 'https://forum.test';

    /**
     * The values of the static properties which the test replaces, to put back afterwards (in the reverse order).
     */
    private array $saved = [];

    protected function setUp(): void
    {
        $this->requireJoomla();

        // The parser is in the folder External of the library.
        spl_autoload_register(static function ($class) {
            if (strpos($class, 'Nbbc\\') === 0) {
                $file = Files::path('src/libraries/kunena/External/Nbbc/src/' . substr($class, 5) . '.php');

                if (is_file($file)) {
                    require_once $file;
                }
            }
        });

        if (!\defined('KPATH_FRAMEWORK')) {
            \define('KPATH_FRAMEWORK', Files::path('src/libraries/kunena'));
        }

        // Loading KunenaRoute (for the links) runs KunenaRoute::initialize(), which stops at once for the command line.
        if (!class_exists(KunenaRoute::class, false)) {
            $cli = $this->createStub(CMSApplicationInterface::class);
            $cli->method('isClient')->willReturn(true);
            $this->replace(KunenaConfig::class, 'instance', $this->bare(KunenaConfig::class));
            $this->replace(Factory::class, 'application', $cli);
            class_exists(KunenaRoute::class);
        }

        // The address of the request and of the site, an application without plugins, the configuration and the template:
        // what the parser needs without a site.
        $config = $this->bare(KunenaConfig::class, ['config' => [
            'boardTitle'          => 'The Board',
            'template'            => 'aurelia',
            'debug'               => 0,
            'fallbackEnglish'     => 0,
            'autoLink'            => 1,
            'trimLongUrls'        => 0,
            'smartLinking'        => 0,
            'linkPreview'         => 0,
            'linkPreviewMax'      => 0,
            'autoEmbedYoutube'    => 0,
            'autoEmbedSpotify'    => 0,
            'autoEmbedDeezer'     => 0,
            'autoEmbedSoundcloud' => 0,
            'autoEmbedInstagram'  => 0,
            'autoEmbedEbay'       => 0,
            'ownDomains'          => '',
        ]]);

        $template         = $this->createStub(KunenaTemplate::class);
        $template->params = new Registry(['disableEmoticons' => 1]);
        $template->method('getTemplatePaths')->willReturn([Files::path('src/site/template/aurelia/layouts/bbcode/url')]);

        $this->replace(Uri::class, 'instances', ['SERVER' => new Uri(self::HOST . '/forum/topic?x=1')]);
        $this->replace(Uri::class, 'root', ['prefix' => self::HOST, 'path' => '']);
        $this->replace(Uri::class, 'base', ['prefix' => self::HOST, 'path' => '']);
        $this->replace(PluginHelper::class, 'plugins', []);
        $this->replace(KunenaConfig::class, 'instance', $config);
        $this->replace(KunenaTemplate::class, '_instances', ['aurelia' => $template]);

        $language    = $this->createStub(Language::class);
        $application = $this->createStub(CMSApplicationInterface::class);
        $application->method('getDispatcher')->willReturn(new Dispatcher());
        $application->method('getLanguage')->willReturn($language);
        $application->method('getInput')->willReturn(new Input([]));
        $application->method('isClient')->willReturn(false);
        $this->replace(Factory::class, 'application', $application);
        $this->replace(Factory::class, 'language', $language);
    }

    protected function tearDown(): void
    {
        foreach (array_reverse($this->saved) as [$property, $value]) {
            $property->setValue(null, $value);
        }

        $this->saved = [];
    }

    private function replace(string $class, string $name, mixed $value): void
    {
        $property      = new ReflectionProperty($class, $name);
        $this->saved[] = [$property, $property->getValue()];
        $property->setValue(null, $value);
    }

    /**
     * The BBCode of a post, its plain text.
     */
    public static function posts(): array
    {
        return [
            // The text which is shown on the page stays.
            'a word in colour'        => ['Hello [color=red]important[/color] world', 'Hello important world'],
            'a post in colour'        => ['[color=#ff0000]Everything in red[/color]', 'Everything in red'],
            'the text of a link'      => ['See [url=https://x.test/doc]the manual[/url] please', 'See the manual please'],
            'a post which is a link'  => ['[url=https://x.test/doc]Read this first[/url]', 'Read this first'],
            'a link without a text'   => ['See [url]https://x.test/doc[/url] please', 'See https://x.test/doc please'],
            'formatting'              => ['[b]bold[/b] and [size=5]big[/size]', 'bold and big'],
            // The blocks which are left out, in any case and in a block of the same tag.
            'a quote'                 => ['[quote="Bob" post=12]old[/quote] new answer', 'new answer'],
            'a quote in capitals'     => ['[QUOTE]old[/QUOTE] new answer', 'new answer'],
            'a quote in a quote'      => ['[quote][quote]oldest[/quote]older[/quote] new answer', 'new answer'],
            'a spoiler in capitals'   => ['Open [SPOILER]the ending[/SPOILER] text', 'Open text'],
            'a spoiler in a spoiler'  => ['[spoiler][spoiler]a[/spoiler]the ending[/spoiler] text', 'text'],
            'code in capitals'        => ['Try [CODE]echo 1;[/CODE] this', 'Try this'],
            'code with a type'        => ['Try [code type=php]echo 1;[/code] this', 'Try this'],
            'an image in capitals'    => ['[IMG]https://x.test/a.png[/IMG] caption', 'caption'],
            'an image in a link'      => ['[url=https://x.test][img]https://x.test/a.png[/img][/url] caption', 'https://x.test caption'],
            'an attachment'           => ['Look [attachment=5]file.jpg[/attachment] here [ATTACHMENT] now', 'Look here now'],
            'a longer name of a tag'  => ['pre [codex] b [/code] post', 'pre [codex] b [/code] post'],
            // The text for some readers only.
            'hidden text'             => ['open [hide]secret[/hide] [PRIVATE]secret[/PRIVATE] end', 'open end'],
        ];
    }

    #[DataProvider('posts')]
    public function testThePlainTextOfAPost(string $bbcode, string $expected): void
    {
        $this->assertSame($expected, KunenaParser::stripBBCode($bbcode, 0, false));
    }

    public function testAnExcerptOfAPostWhichStartsWithATagIsCut(): void
    {
        $excerpt = KunenaParser::stripBBCode('[b]Welcome[/b] ' . str_repeat('to the forum of the club ', 20), 50, false);

        $this->assertStringStartsWith('Welcome to the forum', $excerpt);
        $this->assertStringEndsWith('...', $excerpt);
        $this->assertLessThanOrEqual(53, \strlen($excerpt), $excerpt);
    }

    public function testAnExcerptOfATextWithoutSpacesIsNotEmpty(): void
    {
        $text    = str_repeat('这是一个很长的中文消息没有空格', 10);
        $excerpt = KunenaParser::stripBBCode($text, 200, false);

        $this->assertSame(mb_strcut($text, 0, 200, 'UTF-8') . '...', $excerpt);
    }

    /**
     * The plain text of a post is made with the parser which also writes the posts of the page. For a text in HTML it tells the
     * parser to leave "&" as it is, and it left the parser that way: every post shown later in the request kept its "&" and its
     * single quotes unescaped. The parser escapes again when it is asked for the next text.
     */
    public function testTheParserEscapesAgainAfterThePlainText(): void
    {
        $relative = (new ReflectionProperty(KunenaParser::class, 'relative'))->getValue();

        KunenaParser::stripBBCode('Tom & Jerry', 0, true);

        $parser = KunenaBBCode::getInstance($relative);

        $this->assertFalse($parser->GetAllowAmpersand());
        $this->assertSame('Tom &amp; Jerry&#039;s', $parser->htmlEncode("Tom & Jerry's"));
    }
}
