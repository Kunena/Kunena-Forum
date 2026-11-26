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

namespace Kunena\Tests\Unit\Component\Site\Layout\Widget;

use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Document\HtmlDocument;
use Joomla\CMS\Document\Renderer\Html\ModulesRenderer;
use Joomla\CMS\Event\Content\ContentPrepareEvent;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\Registry;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\DI\Container;
use Joomla\Event\Dispatcher;
use Joomla\Event\DispatcherInterface;
use Kunena\Forum\Libraries\Layout\KunenaModuleContent;
use Kunena\Forum\Site\Layout\Widget\WidgetModule;
use Kunena\Tests\Unit\UnitTestCase;
use ReflectionProperty;

/**
 * A module position of the forum (Kunena 8.0): a topic page asks for about seven positions for each message, nearly always empty.
 * An empty position renders nothing without looking for its layout file; a position with modules is rendered as before. The
 * modules of a position with a target (index_top, topic_top, topic_list_default) go through the Joomla content plugins when the
 * system plugin of Kunena has that target chosen.
 */
class WidgetModuleTest extends UnitTestCase
{
    private mixed $application = null;

    private mixed $container = null;

    private mixed $plugins = null;

    protected function setUp(): void
    {
        $this->requireJoomla();

        $this->application = Factory::$application;
        $this->container   = Factory::$container;
        $this->plugins     = (new ReflectionProperty(PluginHelper::class, 'plugins'))->getValue();
    }

    protected function tearDown(): void
    {
        Factory::$application = $this->application;
        Factory::$container   = $this->container;
        (new ReflectionProperty(PluginHelper::class, 'plugins'))->setValue(null, $this->plugins);
        KunenaModuleContent::reset();
    }

    /**
     * The system plugin of Kunena with the content plugins on for the targets, and a content plugin which marks the text.
     */
    private function contentPlugins(array $targets): void
    {
        $params = json_encode(['jcontentevents' => 1, 'jcontentevent_target' => $targets, 'jcontentevent_plugins' => []]);
        (new ReflectionProperty(PluginHelper::class, 'plugins'))->setValue(null, [(object) ['type' => 'system', 'name' => 'kunena', 'params' => $params, 'id' => 1]]);

        $dispatcher = new Dispatcher();
        $dispatcher->addListener('onContentPrepare', static function (ContentPrepareEvent $event) {
            $event->getItem()->text = str_replace('A module', 'A prepared module (' . $event->getContext() . ')', $event->getItem()->text);
        });
        // HTMLHelper finds its helpers (content.prepare) in the registry of the container.
        Factory::$container = (new Container())->set(DispatcherInterface::class, $dispatcher)->set(Registry::class, new Registry());
    }

    /**
     * The application of the test, with a document which has $count modules in every position.
     */
    private function document(int $count): HtmlDocument
    {
        $document = $this->createStub(HtmlDocument::class);
        $document->method('countModules')->willReturn($count);

        $renderer = $this->createStub(ModulesRenderer::class);
        $renderer->method('render')->willReturn('<div class="module">A module</div>');
        $document->method('loadRenderer')->willReturn($renderer);

        $application = $this->createStub(CMSApplication::class);
        $application->method('getDocument')->willReturn($document);
        Factory::$application = $application;

        return $document;
    }

    private function widget(?string $position): WidgetModule
    {
        // A widget without paths of layout files: looking for its file would throw.
        $widget           = self::bare(WidgetModule::class, ['_name' => 'Widget/Module', 'includePaths' => [], 'after' => []]);
        $widget->position = $position;

        return $widget;
    }

    public function testAnEmptyPositionRendersNothingWithoutItsLayout(): void
    {
        $this->document(0);

        $widget = $this->widget('kunena_msg_3');

        $this->assertFalse($widget->hasModules());
        $this->assertSame('', $widget->render());
        $this->assertSame('', $widget->renderPosition());
    }

    public function testWithoutAPositionNothingIsAsked(): void
    {
        $this->document(1);

        $this->assertFalse($this->widget(null)->hasModules());
        $this->assertSame('', $this->widget('')->render());
    }

    public function testAPositionWithModulesIsRendered(): void
    {
        $this->document(2);

        $widget = $this->widget('kunena_topictitle');

        $this->assertTrue($widget->hasModules());
        $this->assertSame('<div class="module">A module</div>', $widget->renderPosition());
    }

    public function testTheModulesOfAPositionWithAChosenTargetGoThroughTheContentPlugins(): void
    {
        $this->document(1);
        $this->contentPlugins(['index_top']);
        WidgetModule::prepareWith('kunena_index_top', 'index_top');

        $this->assertSame('<div class="module">A prepared module (kunena.contentprepare.index_top)</div>', $this->widget('kunena_index_top')->renderPosition());
    }

    public function testATargetWhichIsNotChosenLeavesTheModules(): void
    {
        $this->document(1);
        $this->contentPlugins(['body']);
        WidgetModule::prepareWith('kunena_index_top', 'index_top');

        $this->assertSame('<div class="module">A module</div>', $this->widget('kunena_index_top')->renderPosition());
    }

    public function testAPositionWithoutATargetIsNotPrepared(): void
    {
        $this->document(1);
        $this->contentPlugins(['index_top', 'topic_top', 'topic_list_default']);
        WidgetModule::prepareWith('kunena_index_top', 'index_top');

        $this->assertSame('<div class="module">A module</div>', $this->widget('kunena_bottom')->renderPosition());
    }

    public function testThePositionsWhichStartTheSameGoThroughTheContentPlugins(): void
    {
        $this->document(1);
        $this->contentPlugins(['topic_messages']);
        WidgetModule::prepareWith('kunena_msg_*', 'topic_messages');

        $this->assertSame('<div class="module">A prepared module (kunena.contentprepare.topic_messages)</div>', $this->widget('kunena_msg_4')->renderPosition());
    }

    public function testTheMorePreciseTargetWinsAndThePluginsRunOnce(): void
    {
        $this->document(1);
        WidgetModule::prepareWith('kunena_top', 'page_top');
        WidgetModule::prepareWith('kunena_top', 'topic_list_default');

        // Both chosen: the target of the list, once (a second run would prepare the text twice).
        $this->contentPlugins(['page_top', 'topic_list_default']);
        $this->assertSame('<div class="module">A prepared module (kunena.contentprepare.topic_list_default)</div>', $this->widget('kunena_top')->renderPosition());

        // Only the target of every page chosen: that one.
        $this->contentPlugins(['page_top']);
        $this->assertSame('<div class="module">A prepared module (kunena.contentprepare.page_top)</div>', $this->widget('kunena_top')->renderPosition());
    }
}
