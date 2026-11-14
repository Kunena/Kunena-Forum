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

use Joomla\CMS\Application\CMSApplicationInterface;
use Joomla\CMS\Factory;
use Joomla\CMS\Menu\AbstractMenu;
use Joomla\CMS\Menu\MenuItem;
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Tests\Unit\UnitTestCase;
use ReflectionProperty;

/**
 * The menu item KunenaRoute chooses for a topic URL, which the action buttons of a topic and of a message carry: a view=user
 * item is never chosen, even when it is the first Kunena menu item of the site (issue #9529: the edit link of a message under
 * a "User" menu item read the message id as a user id).
 */
class KunenaRouteTopicItemidTest extends UnitTestCase
{
    private array $saved = [];

    protected function setUp(): void
    {
        $this->requireJoomla();
        $this->loadRoute();

        $this->saved = [
            'adminApp' => KunenaRoute::$adminApp,
            'menu'     => KunenaRoute::$menu,
            'menus'    => KunenaRoute::$menus,
            'home'     => KunenaRoute::$home,
            'search'   => KunenaRoute::$search,
        ];

        $menus = $this->createStub(AbstractMenu::class);
        $menus->method('authorise')->willReturn(true);

        KunenaRoute::$adminApp = false;
        KunenaRoute::$menus    = $menus;
    }

    protected function tearDown(): void
    {
        foreach ($this->saved as $name => $value) {
            KunenaRoute::$$name = $value;
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

    private function item(int $id, string $view): MenuItem
    {
        return new MenuItem([
            'id'        => $id,
            'parent_id' => 1,
            'component' => 'com_kunena',
            'type'      => 'component',
            'language'  => '*',
            'link'      => 'index.php?option=com_kunena&view=' . $view,
            'query'     => ['option' => 'com_kunena', 'view' => $view],
            'params'    => '{}',
        ]);
    }

    /**
     * The menu of the site, and the search table which KunenaRoute::build() would make of it: a home item under itself, every
     * other item at the top level under no home.
     *
     * @param   MenuItem[]  $items  The Kunena menu items, in their order.
     */
    private function menu(array $items, mixed $home): void
    {
        $menu   = [];
        $search = ['home' => []];

        foreach ($items as $item) {
            $menu[$item->id] = $item;
            $view            = $item->query['view'];
            $parent          = $view === 'home' ? $item->id : 0;

            $search[$view][$parent][$item->id] = $item->id;
        }

        KunenaRoute::$menu   = $menu;
        KunenaRoute::$search = $search;
        KunenaRoute::$home   = $home;
    }

    public function testAUserItemFirstGivesTheHomeItem(): void
    {
        $this->menu([$this->item(208, 'user'), $this->item(142, 'home')], false);

        $this->assertSame(142, (int) KunenaRoute::getItemID('index.php?option=com_kunena&view=topic&catid=9101&id=1232'));
    }

    public function testAUserItemAloneGivesNoItem(): void
    {
        $this->menu([$this->item(208, 'user')], false);

        $this->assertSame(0, (int) KunenaRoute::getItemID('index.php?option=com_kunena&view=topic&catid=9102&id=1232'));
    }

    public function testAHomeItemAloneGivesTheHomeItem(): void
    {
        $home = $this->item(142, 'home');
        $this->menu([$home], $home);

        $this->assertSame(142, (int) KunenaRoute::getItemID('index.php?option=com_kunena&view=topic&catid=9103&id=1232'));
    }
}
