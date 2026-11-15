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
use Joomla\CMS\Factory;
use Kunena\Forum\Site\Layout\Widget\WidgetModule;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * A module position of the forum (Kunena 8.0): a topic page asks for about seven positions for each message, nearly always empty.
 * An empty position renders nothing without looking for its layout file; a position with modules is rendered as before.
 */
class WidgetModuleTest extends UnitTestCase
{
    private mixed $application = null;

    protected function setUp(): void
    {
        $this->requireJoomla();

        $this->application = Factory::$application;
    }

    protected function tearDown(): void
    {
        Factory::$application = $this->application;
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
}
