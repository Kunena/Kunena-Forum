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

namespace Kunena\Tests\Unit\Libraries\Kunena\Menu;

use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Cache\CacheController;
use Joomla\CMS\Cache\CacheControllerFactoryInterface;
use Joomla\CMS\Factory;
use Joomla\CMS\Menu\AbstractMenu;
use Joomla\CMS\Menu\MenuItem;
use Joomla\CMS\User\User;
use Joomla\DI\Container;
use Joomla\Registry\Registry;
use Kunena\Forum\Libraries\Menu\KunenaMenuHelper;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The items of the menu of the forum are copies. The menu of Joomla gives the same objects to every menu module of the page, and
 * the list of Kunena wrote on them (the escaped title, the link, the level): a title was escaped for the other modules too (a note
 * said that the titles were escaped twice "for some reason"), and their items carried the marks of the menu of the forum.
 */
class KunenaMenuHelperListTest extends UnitTestCase
{
    private mixed $savedApplication = null;

    private mixed $savedContainer = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->requireJoomla();

        $this->savedApplication = Factory::$application;
        $this->savedContainer   = Factory::$container;
    }

    protected function tearDown(): void
    {
        Factory::$application = $this->savedApplication;
        Factory::$container   = $this->savedContainer;

        parent::tearDown();
    }

    public function testTheItemsOfJoomlaAreNotChanged(): void
    {
        // Separators: they have no link to route.
        $item   = ['menutype' => 'kunenamenu', 'type' => 'separator', 'level' => 1, 'parent_id' => 1, 'link' => ''];
        $home   = new MenuItem(['id' => 1, 'title' => 'Tips & tricks', 'tree' => [1]] + $item);
        $second = new MenuItem(['id' => 2, 'title' => 'Second', 'tree' => [2]] + $item);

        $menu = $this->createStub(AbstractMenu::class);
        $menu->method('getItems')->willReturn([$home, $second]);
        $menu->method('getActive')->willReturn($home);

        $identity = $this->createStub(User::class);
        $identity->method('getAuthorisedViewLevels')->willReturn([1]);

        $app = $this->createStub(CMSApplication::class);
        $app->method('getMenu')->willReturn($menu);
        $app->method('getIdentity')->willReturn($identity);
        Factory::$application = $app;

        $cache = $this->createStub(CacheController::class);
        $cache->method('__call')->willReturnCallback(static fn ($name) => $name === 'contains' ? false : null);
        $factory = $this->createStub(CacheControllerFactoryInterface::class);
        $factory->method('createCacheController')->willReturn($cache);
        $container = new Container();
        $container->share(CacheControllerFactoryInterface::class, static fn () => $factory);
        Factory::$container = $container;

        $items = KunenaMenuHelper::getList(new Registry(['menutype' => 'kunenamenu']));

        $this->assertCount(2, $items);
        $this->assertNotSame($home, $items[0]);
        $this->assertSame('Tips &amp; tricks', $items[0]->title, 'The title of the list of Kunena is escaped.');
        $this->assertSame('Tips & tricks', $home->title, 'The title of the item of Joomla is as it was.');
        $this->assertFalse(isset($home->flink), 'No link of Kunena on the item of Joomla.');
        $this->assertFalse(isset($home->deeper), 'No level mark of Kunena on the item of Joomla.');
        $this->assertFalse($items[0]->deeper);
    }
}
