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

namespace Kunena\Tests\Integration\Plugin\Kunena\Joomla;

use Joomla\CMS\Factory;
use Joomla\DI\Container;
use Joomla\Registry\Registry;
use Kunena\Forum\Libraries\Forum\KunenaForum;
use Kunena\Forum\Plugin\Kunena\Joomla\Helper\KunenaAccessJoomla;
use Kunena\Tests\Integration\DBTestInterface;
use Kunena\Tests\Integration\DBTestTrait;
use Kunena\Tests\Integration\IntegrationTestCase;

/**
 * The moderator groups of a category (the category parameter moderatorGroups), turned into moderator roles by the Joomla access
 * plugin, on the real tables of the categories and of the user groups.
 */
class KunenaAccessJoomlaModeratorGroupsTest extends IntegrationTestCase implements DBTestInterface
{
    use DBTestTrait;

    /**
     * @var     Container|null  The container of Joomla before the test.
     */
    private $container;

    /**
     * The groups:  1 Public > 2 Registered > 3 Staff > 4 Helpers, and 1 Public > 5 Other.
     * The users:  10 in Staff, 11 in Helpers, 12 in Other, 13 in Registered.
     *
     * @return  void
     */
    protected function setUp(): void
    {
        parent::setUp();

        require_once \dirname(__DIR__, 5) . '/src/plugins/plg_kunena_joomla/src/Helper/KunenaAccessJoomla.php';

        // The plugin reads the database of the container of Joomla: that is the database of the tests here.
        $driver             = $this->getDBDriver();
        $this->container    = Factory::$container;
        Factory::$container = new Container();
        Factory::$container->share('DatabaseDriver', static fn () => $driver);

        $this->insertRows('#__usergroups', [
            ['id' => 1, 'parent_id' => 0, 'lft' => 1, 'rgt' => 10, 'title' => 'Public'],
            ['id' => 2, 'parent_id' => 1, 'lft' => 2, 'rgt' => 7, 'title' => 'Registered'],
            ['id' => 3, 'parent_id' => 2, 'lft' => 3, 'rgt' => 6, 'title' => 'Staff'],
            ['id' => 4, 'parent_id' => 3, 'lft' => 4, 'rgt' => 5, 'title' => 'Helpers'],
            ['id' => 5, 'parent_id' => 1, 'lft' => 8, 'rgt' => 9, 'title' => 'Other'],
        ]);

        $this->insertRows('#__user_usergroup_map', [
            ['user_id' => 10, 'group_id' => 3],
            ['user_id' => 11, 'group_id' => 4],
            ['user_id' => 12, 'group_id' => 5],
            ['user_id' => 13, 'group_id' => 2],
        ]);
    }

    /**
     * @return  void
     */
    protected function tearDown(): void
    {
        Factory::$container = $this->container;

        parent::tearDown();
    }

    /**
     * @return  string[]
     */
    public function getSchemasToLoad(): array
    {
        return ['kunena', 'joomla'];
    }

    private function category(int $id, string $params): void
    {
        $this->insertRow('#__kunena_categories', ['id' => $id, 'parentid' => 1, 'name' => "Category $id", 'params' => $params]);
    }

    /**
     * The moderator roles which the plugin gives, as "category:user", sorted.
     *
     * @return  string[]
     */
    private function moderators(): array
    {
        // The rules of the assets of Joomla are not part of the test: no group is a global administrator.
        $plugin = new class (new Registry()) extends KunenaAccessJoomla {
            protected function getActionGroups(string $action, $asset = null): array
            {
                return ['allow' => [], 'deny' => []];
            }
        };

        $found = [];

        foreach ($plugin->loadCategoryRoles() as $item) {
            if ($item->role === KunenaForum::MODERATOR) {
                $found[] = $item->category_id . ':' . $item->user_id;
            }
        }

        sort($found);

        return $found;
    }

    public function testAMemberOfASelectedGroupModeratesThatCategoryOnly(): void
    {
        $this->category(7, '{"moderatorGroups":[3]}');
        $this->category(8, '{}');

        $moderators = $this->moderators();

        $this->assertContains('7:10', $moderators);
        $this->assertNotContains('8:10', $moderators);
    }

    public function testAMemberOfAChildGroupCounts(): void
    {
        $this->category(7, '{"moderatorGroups":[3]}');

        $this->assertContains('7:11', $this->moderators());
    }

    public function testAUserOfAnUnrelatedOrAParentGroupDoesNotCount(): void
    {
        $this->category(7, '{"moderatorGroups":[3]}');

        $this->assertSame(['7:10', '7:11'], $this->moderators());
    }

    public function testTwoCategoriesWithDifferentGroupsGetTheirOwnMembers(): void
    {
        $this->category(7, '{"moderatorGroups":[3]}');
        $this->category(8, '{"moderatorGroups":[5]}');

        $this->assertSame(['7:10', '7:11', '8:12'], $this->moderators());
    }

    public function testAUserInTwoSelectedGroupsIsListedOnce(): void
    {
        $this->category(7, '{"moderatorGroups":[3,4]}');

        $this->assertSame(['7:10', '7:11'], $this->moderators());
    }

    public function testACategoryWithoutTheParameterGivesNoRoles(): void
    {
        $this->category(7, '{"ownTopicsOnly":"1"}');
        $this->category(8, '');

        $this->assertSame([], $this->moderators());
    }

    public function testADeletedGroupGivesNoRoles(): void
    {
        $this->category(7, '{"moderatorGroups":[99]}');

        $this->assertSame([], $this->moderators());
    }

    public function testAnEmptySelectionGivesNoRoles(): void
    {
        $this->category(7, '{"moderatorGroups":[]}');

        $this->assertSame([], $this->moderators());
    }

    public function testAUserRemovedFromTheGroupIsNoModeratorAnyMore(): void
    {
        $this->category(7, '{"moderatorGroups":[3]}');

        $this->assertContains('7:10', $this->moderators());

        $this->getDBDriver()->setQuery('DELETE FROM ' . $this->getDBDriver()->quoteName('#__user_usergroup_map') . ' WHERE user_id = 10')->execute();

        $this->assertNotContains('7:10', $this->moderators());
    }
}
