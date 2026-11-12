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

use Joomla\CMS\Menu\AbstractMenu;
use Joomla\CMS\Menu\MenuItem;
use Kunena\Forum\Site\Layout\Widget\WidgetBreadcrumb;
use Kunena\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * The breadcrumb showed "Forum > Index" on the index page, and both crumbs opened the same page (issue #2579).
 */
class WidgetBreadcrumbTest extends UnitTestCase
{
    protected function setUp(): void
    {
        $this->requireJoomla();
    }

    /**
     * A menu whose getItem() returns the given items by id.
     *
     * @param   array  $items  The menu items as id => [component, query].
     *
     * @return  AbstractMenu
     */
    private function menu(array $items): AbstractMenu
    {
        $objects = [];

        foreach ($items as $id => [$component, $query]) {
            $link         = 'index.php?' . http_build_query($query);
            $objects[$id] = new MenuItem(['id' => $id, 'component' => $component, 'query' => $query, 'link' => $link, 'type' => 'component']);
        }

        $menu = $this->createStub(AbstractMenu::class);
        $menu->method('getItem')->willReturnCallback(static fn ($id) => $objects[$id] ?? null);

        return $menu;
    }

    /**
     * The standard menu of Kunena: Forum (101, default = Index), Index (102), Recent Topics (103), and an article (104).
     *
     * @param   mixed  $default  The defaultmenu of Forum.
     *
     * @return  AbstractMenu
     */
    private function standardMenu(mixed $default = 102): AbstractMenu
    {
        return $this->menu([
            101 => ['com_kunena', ['option' => 'com_kunena', 'view' => 'home', 'defaultmenu' => $default]],
            102 => ['com_kunena', ['option' => 'com_kunena', 'view' => 'category', 'layout' => 'list']],
            103 => ['com_kunena', ['option' => 'com_kunena', 'view' => 'topics', 'mode' => 'replies']],
            104 => ['com_content', ['option' => 'com_content', 'view' => 'article', 'id' => 1]],
        ]);
    }

    private static function crumb(string $name, ?string $link): object
    {
        return (object) ['name' => $name, 'link' => $link];
    }

    private static function forum(): object
    {
        return self::crumb('Forum', 'index.php?option=com_kunena&view=home&defaultmenu=102&Itemid=101');
    }

    private static function index(): object
    {
        return self::crumb('Index', 'index.php?option=com_kunena&view=category&layout=list&Itemid=102');
    }

    private static function recent(): object
    {
        return self::crumb('Recent Topics', 'index.php?option=com_kunena&view=topics&mode=replies&Itemid=103');
    }

    private static function category(): object
    {
        return self::crumb('Category', 'index.php?option=com_kunena&view=category&catid=2&Itemid=102');
    }

    /**
     * The names of the crumbs, after checking that they are a list.
     *
     * @param   array  $pathway  The crumbs.
     *
     * @return  array
     */
    private function names(array $pathway): array
    {
        $this->assertTrue(array_is_list($pathway));

        return array_map(static fn ($crumb) => $crumb->name, $pathway);
    }

    public function testTheDefaultPageAfterTheHomeIsLeftOut(): void
    {
        $result = WidgetBreadcrumb::withoutDefaultPageCrumb([self::forum(), self::index()], $this->standardMenu());

        $this->assertSame(['Forum'], $this->names($result));
    }

    public function testTheCrumbsAfterTheDefaultPageAreKept(): void
    {
        $forum    = self::forum();
        $category = self::category();
        $result   = WidgetBreadcrumb::withoutDefaultPageCrumb([$forum, self::index(), $category], $this->standardMenu());

        $this->assertSame(['Forum', 'Category'], $this->names($result));
        $this->assertSame($forum->link, $result[0]->link);
        $this->assertSame($category->link, $result[1]->link);
    }

    public function testAPageRoutedToTheDefaultItemIsKept(): void
    {
        $result = WidgetBreadcrumb::withoutDefaultPageCrumb([self::forum(), self::category()], $this->standardMenu());

        $this->assertSame(['Forum', 'Category'], $this->names($result));
    }

    public function testTheDefaultPageNotDirectlyAfterTheHomeIsKept(): void
    {
        $result = WidgetBreadcrumb::withoutDefaultPageCrumb([self::forum(), self::recent(), self::index()], $this->standardMenu());

        $this->assertSame(['Forum', 'Recent Topics', 'Index'], $this->names($result));
    }

    public function testAnotherPageAfterTheHomeIsKept(): void
    {
        $result = WidgetBreadcrumb::withoutDefaultPageCrumb([self::forum(), self::recent()], $this->standardMenu());

        $this->assertSame(['Forum', 'Recent Topics'], $this->names($result));
    }

    public function testTheDefaultPageIsTheOneOfTheHomeItem(): void
    {
        $menu = $this->standardMenu(103);

        $this->assertSame(['Forum', 'Index'], $this->names(WidgetBreadcrumb::withoutDefaultPageCrumb([self::forum(), self::index()], $menu)));
        $this->assertSame(['Forum'], $this->names(WidgetBreadcrumb::withoutDefaultPageCrumb([self::forum(), self::recent()], $menu)));
    }

    public static function invalidDefaults(): array
    {
        return [
            'empty'         => [''],
            'none'          => [null],
            'own id'        => [101],
            'missing item'  => [999],
            'not of Kunena' => [104],
        ];
    }

    #[DataProvider('invalidDefaults')]
    public function testAHomeWithoutAValidDefaultKeepsTheCrumbs(mixed $default): void
    {
        $result = WidgetBreadcrumb::withoutDefaultPageCrumb([self::forum(), self::index()], $this->standardMenu($default));

        $this->assertSame(['Forum', 'Index'], $this->names($result));
    }

    public function testADefaultWithoutAViewKeepsTheCrumbs(): void
    {
        $menu = $this->menu([
            101 => ['com_kunena', ['option' => 'com_kunena', 'view' => 'home', 'defaultmenu' => 102]],
            102 => ['com_kunena', ['option' => 'com_kunena']],
        ]);

        $result = WidgetBreadcrumb::withoutDefaultPageCrumb([self::forum(), self::index()], $menu);

        $this->assertSame(['Forum', 'Index'], $this->names($result));
    }

    public function testAChainOfHomeItemsIsFollowed(): void
    {
        $menu = $this->menu([
            101 => ['com_kunena', ['option' => 'com_kunena', 'view' => 'home', 'defaultmenu' => 105]],
            105 => ['com_kunena', ['option' => 'com_kunena', 'view' => 'home', 'defaultmenu' => 102]],
            102 => ['com_kunena', ['option' => 'com_kunena', 'view' => 'category', 'layout' => 'list']],
        ]);

        $result = WidgetBreadcrumb::withoutDefaultPageCrumb([self::forum(), self::index()], $menu);

        $this->assertSame(['Forum'], $this->names($result));
    }

    public function testALoopOfHomeItemsKeepsTheCrumbs(): void
    {
        $menu = $this->menu([
            101 => ['com_kunena', ['option' => 'com_kunena', 'view' => 'home', 'defaultmenu' => 105]],
            105 => ['com_kunena', ['option' => 'com_kunena', 'view' => 'home', 'defaultmenu' => 101]],
            102 => ['com_kunena', ['option' => 'com_kunena', 'view' => 'category', 'layout' => 'list']],
        ]);

        $result = WidgetBreadcrumb::withoutDefaultPageCrumb([self::forum(), self::index()], $menu);

        $this->assertSame(['Forum', 'Index'], $this->names($result));
    }

    public function testAPathwayWithoutAKunenaHomeIsKept(): void
    {
        $result = WidgetBreadcrumb::withoutDefaultPageCrumb([self::index(), self::category()], $this->standardMenu());

        $this->assertSame(['Index', 'Category'], $this->names($result));
    }

    public function testCrumbsInFrontOfTheHomeAreHandled(): void
    {
        $article = self::crumb('Article', 'index.php?option=com_content&view=article&id=1&Itemid=104');
        $heading = self::crumb('Heading', null);
        $pathway = [$article, $heading, self::forum(), self::index(), self::category()];
        $result  = WidgetBreadcrumb::withoutDefaultPageCrumb($pathway, $this->standardMenu());

        $this->assertSame(['Article', 'Heading', 'Forum', 'Category'], $this->names($result));
    }

    public function testCrumbsWithoutAnItemidAreHandled(): void
    {
        $pathway = [self::crumb('Heading', null), self::crumb('Link', 'index.php?option=com_kunena&view=topics'), self::crumb('Empty', '')];
        $result  = WidgetBreadcrumb::withoutDefaultPageCrumb($pathway, $this->standardMenu());

        $this->assertSame(['Heading', 'Link', 'Empty'], $this->names($result));
        $this->assertSame([], WidgetBreadcrumb::withoutDefaultPageCrumb([], $this->standardMenu()));
    }

    public function testAnAliasOfTheDefaultPageIsLeftOut(): void
    {
        $result = WidgetBreadcrumb::withoutDefaultPageCrumb([self::forum(), self::crumb('Alias', 'index.php?Itemid=102')], $this->standardMenu());

        $this->assertSame(['Forum'], $this->names($result));
    }

    public function testTheResultIsAList(): void
    {
        $pathway = [3 => self::forum(), 7 => self::index(), 9 => self::category()];
        $result  = WidgetBreadcrumb::withoutDefaultPageCrumb($pathway, $this->standardMenu());

        $this->assertSame([0, 1], array_keys($result));
        $this->assertSame([0, 1], array_keys(WidgetBreadcrumb::withoutDefaultPageCrumb([4 => self::forum(), 8 => self::recent()], $this->standardMenu())));
    }
}
