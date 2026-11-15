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
use Kunena\Forum\Libraries\Profiler\KunenaProfiler;
use Kunena\Forum\Libraries\Profiler\KunenaProfilerItem;
use Kunena\Forum\Libraries\Template\KunenaTemplate;
use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;
use ReflectionProperty;

/**
 * The BBCode parsing and the content plugins have profiler items of their own (issue #806): the own time of the parsing rows
 * of the profiler is the time of the BBCode parser, the content plugins are a nested row.
 */
class KunenaParserProfilerTest extends UnitTestCase
{
    private const HOST = 'https://forum.test';

    private const PLAIN = 'function Kunena\Forum\Libraries\Html\KunenaParser::plainBBCode()';

    private const STRIP = 'function Kunena\Forum\Libraries\Html\KunenaParser::stripBBCode()';

    private const PREPARE = 'function Kunena\Forum\Libraries\Html\KunenaParser::prepareContent()';

    /**
     * The values of the static properties which the test replaces, to put back afterwards.
     */
    private array $saved = [];

    /**
     * The open items of the profiler before the test, to put back afterwards.
     */
    private array $heap = [];

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

        // The profiler starts empty: no items and no open item. KunenaProfilerItem is in the file of KunenaProfiler.
        $heap       = new ReflectionProperty(KunenaProfiler::class, '_heap');
        $this->heap = $heap->getValue(KunenaProfiler::instance('Kunena'));
        $heap->setValue(KunenaProfiler::instance('Kunena'), []);
        $this->replace(KunenaProfilerItem::class, '_instances', []);
    }

    protected function tearDown(): void
    {
        if ($this->saved) {
            (new ReflectionProperty(KunenaProfiler::class, '_heap'))->setValue(KunenaProfiler::instance('Kunena'), $this->heap);
        }

        foreach ($this->saved as [$property, $value]) {
            $property->setValue(null, $value);
        }

        $this->saved = [];
        $this->heap  = [];
    }

    private function replace(string $class, string $name, mixed $value): void
    {
        $property      = new ReflectionProperty($class, $name);
        $this->saved[] = [$property, $property->getValue()];
        $property->setValue(null, $value);
    }

    private static function openItems(): array
    {
        return (new ReflectionProperty(KunenaProfiler::class, '_heap'))->getValue(KunenaProfiler::instance('Kunena'));
    }

    public function testPlainBBCodeHasAnItem(): void
    {
        $this->assertSame('<b>x</b>', KunenaParser::plainBBCode('[b]x[/b]'));

        $items = KunenaProfilerItem::getAll();
        $this->assertArrayHasKey(self::PLAIN, $items);
        $this->assertSame(1, $items[self::PLAIN]->calls);
        $this->assertSame([], self::openItems());
    }

    public function testStripBBCodeHasAnItem(): void
    {
        $this->assertSame('x', KunenaParser::stripBBCode('[b]x[/b]'));

        $items = KunenaProfilerItem::getAll();
        $this->assertArrayHasKey(self::STRIP, $items);
        $this->assertSame(1, $items[self::STRIP]->calls);
        $this->assertArrayHasKey(self::PREPARE, $items);
        $this->assertArrayHasKey(self::STRIP, $items[self::PREPARE]->callers);
        $this->assertSame([], self::openItems());
    }

    public function testTheContentPluginsAreANestedItem(): void
    {
        KunenaParser::plainBBCode('[b]x[/b]');

        $items = KunenaProfilerItem::getAll();
        $this->assertArrayHasKey(self::PREPARE, $items);
        $this->assertSame(1, $items[self::PREPARE]->calls);
        $this->assertSame([self::PLAIN => 1], $items[self::PREPARE]->callers);
        $this->assertSame([], self::openItems());
    }

    public function testAnEmptyTextHasNoItem(): void
    {
        $this->assertFalse(KunenaParser::plainBBCode(''));
        $this->assertFalse(KunenaParser::stripBBCode(''));

        $items = KunenaProfilerItem::getAll();
        $this->assertArrayNotHasKey(self::PLAIN, $items);
        $this->assertArrayNotHasKey(self::STRIP, $items);
        $this->assertSame([], self::openItems());
    }
}
