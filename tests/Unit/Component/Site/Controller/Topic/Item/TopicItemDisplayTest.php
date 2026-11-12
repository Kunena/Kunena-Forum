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

namespace Kunena\Tests\Unit\Component\Site\Controller\Topic\Item;

use Joomla\CMS\Application\CMSApplicationInterface;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Forum\Site\Controller\Topic\Item\TopicItemDisplay;
use Kunena\Tests\Unit\UnitTestCase;
use ReflectionProperty;

/**
 * The page links of a topic opened with the link to one message lead to their page (issue #4877: with the message id in the
 * links, every page link opened the page of that message again).
 */
class TopicItemDisplayTest extends UnitTestCase
{
    private mixed $current;

    protected function setUp(): void
    {
        $this->requireJoomla();
        $this->loadRoute();

        $this->current        = KunenaRoute::$current;
        KunenaRoute::$current = new Uri('index.php?option=com_kunena&view=topic&catid=3&id=12&mesid=345&Itemid=101');
    }

    protected function tearDown(): void
    {
        if (\defined('JVERSION')) {
            KunenaRoute::$current = $this->current;
        }
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

    private function paginationUri(): Uri
    {
        return $this->invoke($this->bare(TopicItemDisplay::class), 'getPaginationUri');
    }

    public function testTheMessageIsRemoved(): void
    {
        $this->assertNull($this->paginationUri()->getVar('mesid'));
    }

    public function testTheOtherVariablesAreKept(): void
    {
        $uri = $this->paginationUri();

        $this->assertSame('com_kunena', $uri->getVar('option'));
        $this->assertSame('topic', $uri->getVar('view'));
        $this->assertSame('3', $uri->getVar('catid'));
        $this->assertSame('12', $uri->getVar('id'));
        $this->assertSame('101', $uri->getVar('Itemid'));
    }

    public function testTheCurrentAddressIsNotChanged(): void
    {
        $this->paginationUri();

        $this->assertSame('345', KunenaRoute::$current->getVar('mesid'));
    }
}
