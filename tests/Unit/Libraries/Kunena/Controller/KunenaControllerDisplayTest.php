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

namespace Kunena\Tests\Unit\Libraries\Kunena\Controller;

use Joomla\CMS\Application\CMSApplicationInterface;
use Joomla\CMS\Document\HtmlDocument;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\Controller\KunenaControllerDisplay;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Tests\Unit\UnitTestCase;
use ReflectionProperty;

/**
 * The canonical link of the later pages of a topic or a category points to that page, without the tracking parameters and the
 * other variables of the request, and a page has only one canonical link (issue #6302).
 */
class KunenaControllerDisplayTest extends UnitTestCase
{
    private mixed $current;

    protected function setUp(): void
    {
        $this->requireJoomla();
        $this->loadRoute();

        $this->current        = KunenaRoute::$current;
        KunenaRoute::$current = new Uri(
            'index.php?option=com_kunena&view=topic&catid=3&id=12&mesid=345&us=search&utm_source=x&layout=threaded&limit=5&Itemid=101'
        );
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

    private function controller(): KunenaControllerDisplay
    {
        return $this->createStub(KunenaControllerDisplay::class);
    }

    private function canonicalUri(int $limitstart): Uri
    {
        return $this->invoke($this->controller(), 'getCanonicalUri', ['option', 'view', 'catid', 'id', 'Itemid'], $limitstart);
    }

    public function testOnlyTheNamedVariablesAndThePageAreKept(): void
    {
        $vars = $this->canonicalUri(40)->getQuery(true);
        ksort($vars);

        $expected = ['option' => 'com_kunena', 'view' => 'topic', 'catid' => '3', 'id' => '12', 'Itemid' => '101', 'limitstart' => 40];
        ksort($expected);

        $this->assertEquals($expected, $vars);
    }

    public function testTheFirstPageHasNoLimitstart(): void
    {
        $this->assertNull($this->canonicalUri(0)->getVar('limitstart'));
    }

    public function testAMissingVariableIsLeftOut(): void
    {
        $uri = $this->invoke($this->controller(), 'getCanonicalUri', ['option', 'view', 'catid', 'Itemid', 'missing'], 0);

        $this->assertNull($uri->getVar('missing'));
        $this->assertSame('3', $uri->getVar('catid'));
    }

    public function testTheCurrentAddressIsNotChanged(): void
    {
        $this->canonicalUri(40);

        $this->assertSame('search', KunenaRoute::$current->getVar('us'));
        $this->assertSame('345', KunenaRoute::$current->getVar('mesid'));
        $this->assertNull(KunenaRoute::$current->getVar('limitstart'));
    }

    public function testOnlyOneCanonicalLinkIsLeft(): void
    {
        $doc = $this->bare(HtmlDocument::class);
        $doc->addHeadLink('/forum/topic', 'canonical');
        $doc->addHeadLink('/forum/topic?us=search', 'canonical');
        $doc->addHeadLink('/forum/topic?limitstart=10', 'prev');

        $this->invoke($this->controller(), 'setCanonicalLink', $doc, '/forum/topic?limitstart=20');

        $canonical = array_filter($doc->_links, static fn ($link) => $link['relation'] === 'canonical');

        $this->assertSame(['/forum/topic?limitstart=20'], array_keys($canonical));
        $this->assertSame('prev', $doc->_links['/forum/topic?limitstart=10']['relation']);
    }
}
