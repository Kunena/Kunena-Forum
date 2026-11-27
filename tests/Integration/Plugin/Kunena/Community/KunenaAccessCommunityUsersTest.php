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

namespace Kunena\Tests\Integration\Plugin\Kunena\Community;

use Joomla\CMS\Factory;
use Joomla\Database\DatabaseInterface;
use Joomla\DI\Container;
use Joomla\Registry\Registry;
use Kunena\Forum\Libraries\Database\KunenaDatabaseObject;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategory;
use Kunena\Forum\Plugin\Kunena\Community\Helper\KunenaAccessCommunity;
use Kunena\Tests\Integration\DBTestInterface;
use Kunena\Tests\Integration\DBTestTrait;
use Kunena\Tests\Integration\IntegrationTestCase;
use ReflectionClass;

/**
 * The subscribers of a category of a JomSocial group who get its notifications: the members of the group among them. The access
 * of JomSocial gave back the id of the category: the subscribers got nothing, and the user whose id was the id of the category got
 * the notifications.
 */
class KunenaAccessCommunityUsersTest extends IntegrationTestCase implements DBTestInterface
{
    use DBTestTrait;

    private mixed $savedContainer = null;

    protected function setUp(): void
    {
        parent::setUp();

        if (!\defined('JVERSION')) {
            $this->markTestSkipped('The framework of Joomla is not loaded: set KUNENA_TEST_JOOMLA to a Joomla installation.');
        }

        require_once \dirname(__DIR__, 5) . '/src/plugins/plg_kunena_community/src/Helper/KunenaAccessCommunity.php';

        $driver    = $this->getDBDriver();
        $container = new Container();
        $container->share('DatabaseDriver', static fn () => $driver);
        $container->share(DatabaseInterface::class, static fn () => $driver);
        $this->savedContainer = Factory::$container;
        Factory::$container   = $container;

        // The members of the groups of JomSocial (its table, for the test only): 101 and 102 are in the group 5, 103 waits for
        // the approval of the group.
        $driver->setQuery('CREATE TABLE IF NOT EXISTS #__community_groups_members (groupid int NOT NULL, memberid int NOT NULL, approved tinyint NOT NULL DEFAULT 0)')->execute();
        $driver->setQuery('INSERT INTO #__community_groups_members (groupid, memberid, approved) VALUES (5, 101, 1), (5, 102, 1), (5, 103, 0)')->execute();

        // The category 12 is for the group 5.
        $this->insertRow('#__kunena_categories', ['id' => 12, 'parentid' => 1, 'name' => 'Group', 'alias' => 'group', 'published' => 1, 'accesstype' => 'jomsocial', 'access' => 5]);
    }

    protected function tearDown(): void
    {
        if ($this->savedContainer !== null) {
            $this->getDBDriver()->setQuery('DROP TABLE IF EXISTS #__community_groups_members')->execute();
            Factory::$container = $this->savedContainer;
        }

        parent::tearDown();
    }

    public function testTheAllowedUsersAreTheMembersOfTheGroup(): void
    {
        $category     = (new ReflectionClass(KunenaCategory::class))->newInstanceWithoutConstructor();
        $category->id = 12;

        $topic = new class ($category) extends KunenaDatabaseObject {
            public function __construct(private KunenaCategory $category)
            {
            }

            public function getCategory(): KunenaCategory
            {
                return $this->category;
            }
        };

        [$allow, $deny] = (new KunenaAccessCommunity(new Registry()))->authoriseUsers($topic, [101, 103, 104, 12]);

        $allow = array_map('intval', $allow);
        sort($allow);

        $this->assertSame([101], $allow);
        $this->assertSame([], $deny);
    }
}
