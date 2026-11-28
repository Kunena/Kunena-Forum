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

namespace Kunena\Tests\Integration\Libraries\Kunena\User;

use Joomla\CMS\Access\Access;
use Joomla\CMS\Factory;
use Joomla\Database\DatabaseInterface;
use Joomla\DI\Container;
use Kunena\Forum\Libraries\User\KunenaUser;
use Kunena\Tests\Integration\DBTestInterface;
use Kunena\Tests\Integration\DBTestTrait;
use Kunena\Tests\Integration\IntegrationTestCase;
use ReflectionClass;
use ReflectionProperty;

/**
 * The groups of a user next to his posts and in his profile (rank images "Usergroup" or "Both"): all his groups. The ids of his
 * groups were joined ("2,8") and made a number (2), so a member of Registered and Super Users, or of Registered and a group of the
 * site, was shown as Registered only.
 */
class KunenaUserGroupTitlesTest extends IntegrationTestCase implements DBTestInterface
{
    use DBTestTrait;

    private ?Container $savedContainer = null;

    public function getSchemasToLoad(): array
    {
        return ['kunena', 'joomla'];
    }

    protected function setUp(): void
    {
        parent::setUp();

        if (!\defined('JVERSION')) {
            $this->markTestSkipped('The framework of Joomla is not loaded: set KUNENA_TEST_JOOMLA to a Joomla installation.');
        }

        $driver    = $this->getDBDriver();
        $container = new Container();
        $container->share(DatabaseInterface::class, static fn () => $driver);
        $container->share('DatabaseDriver', static fn () => $driver);

        $this->savedContainer = Factory::$container;
        Factory::$container   = $container;
        Access::clearStatics();

        $this->insertRows('#__usergroups', [
            ['id' => 1, 'parent_id' => 0, 'lft' => 1, 'rgt' => 10, 'title' => 'Public'],
            ['id' => 2, 'parent_id' => 1, 'lft' => 2, 'rgt' => 5, 'title' => 'Registered'],
            ['id' => 8, 'parent_id' => 1, 'lft' => 6, 'rgt' => 7, 'title' => 'Super Users'],
            ['id' => 10, 'parent_id' => 2, 'lft' => 3, 'rgt' => 4, 'title' => 'VIP & friends'],
        ]);
        // The direct groups of the user 60, as the access of Joomla keeps them (it reads them through the cache of an application).
        $groups = new ReflectionProperty(Access::class, 'groupsByUser');
        $groups->setValue(null, ['60:0' => [2, 8, 10]] + (array) $groups->getValue());
    }

    protected function tearDown(): void
    {
        if ($this->savedContainer !== null) {
            Factory::$container = $this->savedContainer;
            Access::clearStatics();
        }

        parent::tearDown();
    }

    public function testEveryGroupOfTheUserIsShown(): void
    {
        $user = (new ReflectionClass(KunenaUser::class))->newInstanceWithoutConstructor();

        $this->assertSame('Registered<br/>VIP &amp; friends<br/>Super Users<br/>', $user->GetUserGroup(60));
    }
}
