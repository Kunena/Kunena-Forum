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
use Kunena\Forum\Libraries\Html\KunenaParser;
use Kunena\Forum\Libraries\Template\KunenaTemplate;
use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;
use ReflectionProperty;

/**
 * The plain text of a message (the tooltips of the topic lists, the description of the page, the index of the search of the site)
 * took out the text in [color] with the tag: a word in colour was missing, and a message written in colour had no text at all,
 * so the search of the site did not find it.
 */
class KunenaParserStripBBCodeTest extends UnitTestCase
{
    private const HOST = 'https://forum.test';

    /**
     * The values of the static properties which the test replaces, to put back afterwards.
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

        // The address of the request and of the site, an application without plugins, the configuration and the template:
        // what the parser needs without a site.
        $config = $this->bare(KunenaConfig::class, ['config' => ['boardTitle' => 'The Board', 'template' => 'aurelia', 'debug' => 0, 'fallbackEnglish' => 0]]);
        $this->replace(Uri::class, 'instances', ['SERVER' => new Uri(self::HOST . '/forum/topic?x=1')]);
        $this->replace(Uri::class, 'root', ['prefix' => self::HOST, 'path' => '']);
        $this->replace(Uri::class, 'base', ['prefix' => self::HOST, 'path' => '']);
        $this->replace(PluginHelper::class, 'plugins', []);
        $this->replace(KunenaConfig::class, 'instance', $config);
        $this->replace(KunenaTemplate::class, '_instances', ['aurelia' => $this->bare(KunenaTemplate::class, ['params' => new Registry(['disableEmoticons' => 1])])]);

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
        foreach ($this->saved as [$property, $value]) {
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

    public function testTheTextInColourStays(): void
    {
        $this->assertSame('Important: the server is down', trim(KunenaParser::stripBBCode('[color=red]Important:[/color] the server is down', 0, false)));
    }

    public function testAMessageWrittenInColourHasItsText(): void
    {
        $this->assertSame('All of it in blue', trim(KunenaParser::stripBBCode('[color=#0000ff]All of it in blue[/color]', 0, false)));
    }

    public function testTheTextInHideStaysOut(): void
    {
        $this->assertSame('Open', trim(KunenaParser::stripBBCode('Open[hide]Secret[/hide]', 0, false)));
    }
}
