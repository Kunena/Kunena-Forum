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

namespace Kunena\Tests\Unit\Component\Site\Service;

use Joomla\CMS\Menu\AbstractMenu;
use Joomla\CMS\Menu\MenuItem;
use Kunena\Forum\Site\Service\Router;
use Kunena\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use ReflectionProperty;

/**
 * An address with the menu item of a list (the index, the recent topics) and another view was built as
 * /forum/index/topics/mode-replies: the name of the view in the path, which the parse of that menu item reads as a layout of its
 * own view, and the page was a 404 (the redirect of Joomla from index.php?option=com_kunena&view=topics&mode=replies&Itemid=<index>
 * went there). Such an address is built under the home menu item of the forum.
 */
class RouterPreprocessTest extends UnitTestCase
{
    private const HOME = 138;

    private const INDEX = 139;

    private const RECENT = 140;

    private const HOME_NL = 150;

    private Router $router;

    protected function setUp(): void
    {
        $this->requireJoomla();

        $this->router = $this->routerWith([
            self::item(self::HOME, ['view' => 'home'], '*'),
            self::item(self::INDEX, ['view' => 'category', 'layout' => 'list'], '*'),
            self::item(self::RECENT, ['view' => 'topics', 'mode' => 'replies'], '*'),
            self::item(self::HOME_NL, ['view' => 'home'], 'nl-NL'),
            self::item(self::INDEX + 100, ['view' => 'category', 'layout' => 'list'], 'nl-NL'),
        ]);
    }

    private static function item(int $id, array $query, string $language): MenuItem
    {
        $item            = new MenuItem();
        $item->id        = $id;
        $item->component = 'com_kunena';
        $item->query     = ['option' => 'com_kunena'] + $query;
        $item->language  = $language;

        return $item;
    }

    private function routerWith(array $items): Router
    {
        $byId = [];

        foreach ($items as $item) {
            $byId[$item->id] = $item;
        }

        $menu = $this->createStub(AbstractMenu::class);
        $menu->method('getItem')->willReturnCallback(static fn ($id) => $byId[(int) $id] ?? null);
        $menu->method('getItems')->willReturn(array_values($byId));

        $router = new class () extends Router {
            public function __construct()
            {
            }
        };

        (new ReflectionProperty($router, 'menu'))->setValue($router, $menu);

        return $router;
    }

    public static function carried(): array
    {
        return [
            'any view under the home menu item'       => [['view' => 'home'], ['view' => 'topics', 'mode' => 'replies']],
            'the view of the menu item'               => [['view' => 'topics', 'mode' => 'replies'], ['view' => 'topics', 'mode' => 'noreplies']],
            'a category under the category list'      => [['view' => 'category', 'layout' => 'list'], ['view' => 'category', 'catid' => 3]],
            'a topic under the category list'         => [['view' => 'category', 'layout' => 'list'], ['view' => 'topic', 'catid' => 3, 'id' => 9]],
            'an address without a view'               => [['view' => 'topics'], ['task' => 'x']],
        ];
    }

    #[DataProvider('carried')]
    public function testAMenuItemCarriesTheAddress(array $menuQuery, array $query): void
    {
        $this->assertTrue(Router::canCarry($menuQuery, $query));
    }

    public static function notCarried(): array
    {
        return [
            'the recent topics under the index' => [['view' => 'category', 'layout' => 'list'], ['view' => 'topics', 'mode' => 'replies']],
            'a profile under the index'          => [['view' => 'category', 'layout' => 'list'], ['view' => 'user', 'userid' => 797]],
            'the search under the recent topics' => [['view' => 'topics'], ['view' => 'search']],
            'a topic without its category'       => [['view' => 'topics'], ['view' => 'topic', 'id' => 9]],
            'a category under the recent topics' => [['view' => 'topics', 'mode' => 'replies'], ['view' => 'category', 'catid' => 3]],
            'a topic under the recent topics'    => [['view' => 'topics', 'mode' => 'replies'], ['view' => 'topic', 'catid' => 3, 'id' => 9]],
        ];
    }

    #[DataProvider('notCarried')]
    public function testAMenuItemOfAnotherViewDoesNotCarryTheAddress(array $menuQuery, array $query): void
    {
        $this->assertFalse(Router::canCarry($menuQuery, $query));
    }

    public function testTheRecentTopicsWithTheIndexMenuItemGoUnderTheHomeMenuItem(): void
    {
        $query = $this->router->preprocess(['option' => 'com_kunena', 'view' => 'topics', 'mode' => 'replies', 'Itemid' => self::INDEX]);

        $this->assertSame(self::HOME, $query['Itemid']);
    }

    /**
     * Joomla gives the variables of the menu item to the request: the layout of the index went with the recent topics to the home
     * menu item (/forum/topics/list/mode-replies, a 404 again), and the mode of the recent topics with the search.
     */
    public function testTheVariablesOfTheMenuItemWhichIsLeftStayBehind(): void
    {
        $query = $this->router->preprocess(['option' => 'com_kunena', 'view' => 'topics', 'mode' => 'replies', 'layout' => 'list', 'Itemid' => self::INDEX]);

        $this->assertSame(['option' => 'com_kunena', 'view' => 'topics', 'mode' => 'replies', 'Itemid' => self::HOME], $query);

        $query = $this->router->preprocess(['option' => 'com_kunena', 'view' => 'search', 'mode' => 'replies', 'query' => 'x', 'Itemid' => self::RECENT]);

        $this->assertSame(['option' => 'com_kunena', 'view' => 'search', 'query' => 'x', 'Itemid' => self::HOME], $query);
    }

    public function testAnAddressWhichTheMenuItemCarriesKeepsIt(): void
    {
        $query = $this->router->preprocess(['option' => 'com_kunena', 'view' => 'topic', 'catid' => 3, 'id' => 9, 'Itemid' => self::INDEX]);

        $this->assertSame(self::INDEX, $query['Itemid']);
    }

    public function testTheHomeMenuItemOfTheLanguageOfTheMenuItemIsTaken(): void
    {
        $query = $this->router->preprocess(['option' => 'com_kunena', 'view' => 'search', 'Itemid' => self::INDEX + 100]);

        $this->assertSame(self::HOME_NL, $query['Itemid']);
    }

    public function testWithoutAHomeMenuItemTheAddressHasNoMenuItem(): void
    {
        $router = $this->routerWith([self::item(self::INDEX, ['view' => 'category', 'layout' => 'list'], '*')]);

        $query = $router->preprocess(['option' => 'com_kunena', 'view' => 'topics', 'mode' => 'replies', 'Itemid' => self::INDEX]);

        $this->assertArrayNotHasKey('Itemid', $query);
    }
}
