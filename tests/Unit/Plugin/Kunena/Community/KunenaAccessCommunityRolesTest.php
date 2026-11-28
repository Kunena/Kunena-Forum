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

namespace Kunena\Tests\Unit\Plugin\Kunena\Community;

use Joomla\CMS\Factory;
use Joomla\Database\DatabaseDriver;
use Joomla\Database\Mysqli\MysqliQuery;
use Joomla\DI\Container;
use Kunena\Forum\Plugin\Kunena\Community\Helper\KunenaAccessCommunity;
use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;
use ReflectionClass;

/**
 * The roles of the categories of JomSocial give the administrators of a group the rights of an administrator in the categories of
 * the group: the query gave select() the columns as several arguments, select() takes one, so the category and the role were left
 * out and the administrators of the groups got no rights.
 */
class KunenaAccessCommunityRolesTest extends UnitTestCase
{
    private mixed $container = null;

    protected function setUp(): void
    {
        $this->requireJoomla();

        if (!\defined('COMMUNITY_GROUP_ADMIN')) {
            \define('COMMUNITY_GROUP_ADMIN', 1);
        }

        require_once Files::path('src/plugins/plg_kunena_community/src/Helper/KunenaAccessCommunity.php');

        $this->container = Factory::$container;
    }

    protected function tearDown(): void
    {
        Factory::$container = $this->container;

        parent::tearDown();
    }

    public function testTheQueryHasTheCategoryAndTheRole(): void
    {
        $driver = $this->createStub(DatabaseDriver::class);
        $driver->method('quoteName')->willReturnCallback(static fn ($name, $as = null) => '`' . $name . '`' . ($as ? ' AS `' . $as . '`' : ''));
        $driver->method('quote')->willReturnCallback(static fn ($text) => "'" . $text . "'");
        $driver->method('createQuery')->willReturnCallback(static fn () => new MysqliQuery($driver));
        $driver->method('loadObjectList')->willReturn([]);

        $queries = [];
        $driver->method('setQuery')->willReturnCallback(function ($query) use (&$queries, $driver) {
            $queries[] = (string) $query;

            return $driver;
        });

        $container = new Container();
        $container->share('DatabaseDriver', static fn () => $driver);
        Factory::$container = $container;

        (new ReflectionClass(KunenaAccessCommunity::class))->newInstanceWithoutConstructor()->loadCategoryRoles();

        $this->assertCount(1, $queries);
        $this->assertStringContainsString('`c.id` AS `category_id`', $queries[0]);
        $this->assertStringContainsString(' AS role', $queries[0]);
    }
}
