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
use DOMNodeList;
use Joomla\CMS\Application\CMSApplicationInterface;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\DI\Container;
use Joomla\Input\Input;
use Joomla\Registry\Registry;
use Kunena\Forum\Libraries\BBCode\KunenaBBCode;
use Kunena\Forum\Libraries\BBCode\KunenaBBCodeLibrary;
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Forum\Libraries\Template\KunenaTemplate;
use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;
use Nbbc\BBCode;
use PHPUnit\Framework\Attributes\DataProvider;
use ReflectionMethod;
use ReflectionProperty;

/**
 * [url]address[/url]: the address inside the tag was auto-linked first (scheme removed, text shortened, a card or a player), and
 * DoURL() read a damaged address back out of that HTML: the tags were printed as text instead of one link to the address.
 */
class KunenaBBCodeUrlWithoutDefinitionTest extends UnitTestCase
{
    private const BBCODE = 'src/libraries/kunena/src/BBCode/KunenaBBCode.php';

    private const LAYOUTS = 'src/site/template/aurelia/layouts/bbcode/url';

    /**
     * The configuration of the parser: auto-linking on with the shortening of long addresses, smart linking off.
     */
    private const CONFIG = [
        'autoLink'            => 1,
        'trimLongUrls'        => 1,
        'trimLongUrlsFront'   => 10,
        'trimLongUrlsBack'    => 5,
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
        'template'            => 'aurelia',
        'debug'               => 0,
    ];

    private array $saved = [];

    protected function setUp(): void
    {
        $this->requireJoomla();

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

        $this->loadRoute();

        $site = $this->createStub(CMSApplicationInterface::class);
        $site->method('isClient')->willReturn(false);
        $site->method('getInput')->willReturn(new Input([]));

        // The layout BBCode/URL of the template is found in the folder of the template Aurelia.
        $template = $this->createStub(KunenaTemplate::class);
        $template->method('getTemplatePaths')->willReturn([Files::path(self::LAYOUTS)]);

        $this->saved = [
            'config'      => (new ReflectionProperty(KunenaConfig::class, 'instance'))->getValue(),
            'templates'   => (new ReflectionProperty(KunenaTemplate::class, '_instances'))->getValue(),
            'application' => Factory::$application,
            'container'   => Factory::$container,
            'base'        => (new ReflectionProperty(Uri::class, 'base'))->getValue(),
            'root'        => (new ReflectionProperty(Uri::class, 'root'))->getValue(),
        ];

        (new ReflectionProperty(KunenaConfig::class, 'instance'))->setValue(null, $this->config());
        (new ReflectionProperty(KunenaTemplate::class, '_instances'))->setValue(null, ['aurelia' => $template]);
        Factory::$application = $site;
        Factory::$container   = (new Container())->set('config', new Registry([]));

        // The forum is at https://example.com/forum/: the addresses of the tests are on other sites.
        (new ReflectionProperty(Uri::class, 'base'))->setValue(null, ['prefix' => 'https://example.com', 'path' => '/forum']);
        (new ReflectionProperty(Uri::class, 'root'))->setValue(null, ['prefix' => 'https://example.com', 'path' => '/forum']);
    }

    protected function tearDown(): void
    {
        if (!$this->saved) {
            return;
        }

        (new ReflectionProperty(KunenaConfig::class, 'instance'))->setValue(null, $this->saved['config']);
        (new ReflectionProperty(KunenaTemplate::class, '_instances'))->setValue(null, $this->saved['templates']);
        Factory::$application = $this->saved['application'];
        Factory::$container   = $this->saved['container'];
        (new ReflectionProperty(Uri::class, 'base'))->setValue(null, $this->saved['base']);
        (new ReflectionProperty(Uri::class, 'root'))->setValue(null, $this->saved['root']);
    }

    /**
     * Loading KunenaRoute runs KunenaRoute::initialize(), which needs the configuration and an application: give it an empty
     * configuration and an application of the command line, for which it stops at once, and take both back afterwards.
     */
    private function loadRoute(): void
    {
        if (class_exists(KunenaRoute::class, false)) {
            return;
        }

        $config      = new ReflectionProperty(KunenaConfig::class, 'instance');
        $application = Factory::$application;
        $cli         = $this->createStub(CMSApplicationInterface::class);
        $cli->method('isClient')->willReturn(true);

        $config->setValue(null, $this->bare(KunenaConfig::class));
        Factory::$application = $cli;

        try {
            class_exists(KunenaRoute::class);
        } finally {
            $config->setValue(null, null);
            Factory::$application = $application;
        }
    }

    private function config(): KunenaConfig
    {
        return $this->bare(KunenaConfig::class, ['config' => self::CONFIG]);
    }

    /**
     * The parser of Kunena as its constructor makes it (URL detection with parseUrl() as the pattern), without the plugins, the
     * smileys of the database and the application of a site.
     */
    private function parse(string $text): string
    {
        $config  = $this->config();
        $library = $this->bare(KunenaBBCodeLibrary::class, ['config' => $config]);
        $parser  = $this->bare(KunenaBBCode::class, ['config' => $config]);

        (new ReflectionMethod(BBCode::class, '__construct'))->invoke($parser);

        (new ReflectionProperty(KunenaBBCode::class, 'defaults'))->setValue($parser, $library);
        (new ReflectionProperty(KunenaBBCode::class, 'tag_rules'))->setValue($parser, $library->default_tag_rules);
        $parser->SetEnableSmileys(false);
        $parser->SetDetectURLs(true);
        $parser->SetURLPattern([$parser, 'parseUrl']);
        $parser->SetURLTarget('_blank');

        return (string) $parser->parse($text);
    }

    private function links(string $html): DOMNodeList
    {
        $previous = libxml_use_internal_errors(true);
        $dom      = new DOMDocument();
        $dom->loadHTML('<?xml encoding="UTF-8"><html><body>' . $html . '</body></html>');
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        return $dom->getElementsByTagName('a');
    }

    /**
     * The address as written, the expected href.
     */
    public static function addresses(): array
    {
        return [
            'the address of the issue'   => ['https://www.kunena.org/forum/recent', 'https://www.kunena.org/forum/recent'],
            'longer than the shortening' => [
                'https://www.kunena.org/forum/category/general-discussion/a-topic-with-a-long-title',
                'https://www.kunena.org/forum/category/general-discussion/a-topic-with-a-long-title',
            ],
            'query string and fragment' => [
                'https://www.kunena.org/forum/search?query=a&b=c#top',
                'https://www.kunena.org/forum/search?query=a&b=c#top',
            ],
            'without a scheme' => ['www.kunena.org/forum', 'http://www.kunena.org/forum'],
        ];
    }

    #[DataProvider('addresses')]
    public function testUrlWithoutDefinitionIsOneLinkToTheAddressAsWritten(string $address, string $href): void
    {
        $html  = $this->parse('[url]' . $address . '[/url]');
        $links = $this->links($html);

        $this->assertSame(1, $links->length, $html);
        $this->assertSame($href, $links->item(0)->getAttribute('href'), $html);
        $this->assertSame($address, trim($links->item(0)->textContent), $html);
        $this->assertStringNotContainsString('[url]', $html);
    }

    public function testTheLongAddressIsLongerThanTheShortening(): void
    {
        $config = self::CONFIG;

        $this->assertGreaterThan(
            $config['trimLongUrlsFront'] + $config['trimLongUrlsBack'] + 4,
            \strlen(preg_replace('#^https?://#', '', self::addresses()['longer than the shortening'][0]))
        );
    }

    public function testLineBreaksAroundTheAddressAreTrimmed(): void
    {
        $html  = $this->parse("[url]\nhttps://www.kunena.org/forum/recent\n[/url]");
        $links = $this->links($html);

        $this->assertSame(1, $links->length, $html);
        $this->assertSame('https://www.kunena.org/forum/recent', $links->item(0)->getAttribute('href'), $html);
    }

    public function testInvalidAddressIsNotLinked(): void
    {
        $html = $this->parse('[url]javascript:alert(1)[/url]');

        $this->assertSame(0, $this->links($html)->length, $html);
        $this->assertSame('[url]javascript:alert(1)[/url]', $html);
    }

    public function testAddressesOutsideTheTagAreStillAutoLinked(): void
    {
        $html  = $this->parse('[url]https://www.kunena.org/forum/recent[/url] and https://www.kunena.org/docs');
        $links = $this->links($html);

        $this->assertSame(2, $links->length, $html);
        $this->assertSame('https://www.kunena.org/docs', $links->item(1)->getAttribute('href'), $html);
        // Auto-linked as before: without the scheme, and shortened.
        $this->assertSame('www.kunena.../docs', trim($links->item(1)->textContent), $html);
    }
}
