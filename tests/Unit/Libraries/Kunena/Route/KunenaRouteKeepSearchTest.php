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

namespace Kunena\Tests\Unit\Libraries\Kunena\Route;

use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Application\CMSApplicationInterface;
use Joomla\CMS\Factory;
use Joomla\Input\Input;
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Tests\Unit\UnitTestCase;
use ReflectionProperty;

/**
 * The redirect of a search without an Itemid (a link of another site, a bookmark) to the address of the menu item keeps every option
 * of the search: only the words, the titles, the user, the period and the order were kept, so the search went through every
 * category, every date and state, without a word about it.
 */
class KunenaRouteKeepSearchTest extends UnitTestCase
{
    private mixed $application = null;

    protected function setUp(): void
    {
        $this->requireJoomla();

        $this->application = Factory::$application;
        $this->loadRoute();
    }

    /**
     * Loading KunenaRoute runs KunenaRoute::initialize() (as in KunenaRouteCurrentValueTest): an empty configuration and an
     * application of the command line, for which it stops at once, both taken back afterwards.
     */
    private function loadRoute(): void
    {
        if (class_exists(KunenaRoute::class, false)) {
            return;
        }

        $config = new ReflectionProperty(KunenaConfig::class, 'instance');
        $cli    = $this->createStub(CMSApplicationInterface::class);
        $cli->method('isClient')->willReturn(true);

        $config->setValue(null, $this->bare(KunenaConfig::class));
        Factory::$application = $cli;

        try {
            class_exists(KunenaRoute::class);
        } finally {
            $config->setValue(null, null);
            Factory::$application = $this->application;
        }
    }

    protected function tearDown(): void
    {
        Factory::$application = $this->application;
    }

    public function testTheOptionsOfTheSearchAreKept(): void
    {
        $options = [
            'query'        => 'forum',
            'catids'       => '3 4',
            'childforums'  => '0',
            'exactname'    => '1',
            'beforeafter'  => 'before',
            'searchatdate' => '2026-11-28',
            'show'         => '1',
            'ids'          => '12',
        ];

        $application = $this->createStub(CMSApplication::class);
        $application->method('getInput')->willReturn(new Input($options));
        Factory::$application = $application;

        $params = KunenaRoute::keepQuery(['option' => 'com_kunena', 'view' => 'search', 'Itemid' => 5]);

        $this->assertSame(['option' => 'com_kunena', 'view' => 'search', 'Itemid' => 5] + $options, $params);
    }
}
