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

namespace Kunena\Tests\Unit\Plugin\Kunena\Comprofiler;

use Joomla\CMS\Factory;
use Joomla\Database\DatabaseDriver;
use Joomla\Database\Mysqli\MysqliQuery;
use Joomla\DI\Container;
use Kunena\Forum\Plugin\Kunena\Comprofiler\Helper\KunenaLoginComprofiler;
use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;
use ReflectionClass;

/**
 * "Remember me" of the login of Community Builder is the option of its login module: the query was never given to the database (the
 * last query of the request was run again and its first column read as the parameters), and it read the table of the extensions
 * with a join to an unknown alias.
 */
class KunenaLoginComprofilerRememberTest extends UnitTestCase
{
    private mixed $container = null;

    protected function setUp(): void
    {
        $this->requireJoomla();

        // The registry of Community Builder, as the module stores its parameters (JSON).
        if (!class_exists('CBLib\Registry\Registry')) {
            eval('namespace CBLib\Registry; class Registry extends \Joomla\Registry\Registry {}');
        }

        require_once Files::path('src/plugins/plg_kunena_comprofiler/src/Helper/KunenaLoginComprofiler.php');

        $this->container = Factory::$container;
    }

    protected function tearDown(): void
    {
        Factory::$container = $this->container;

        parent::tearDown();
    }

    public function testTheOptionOfTheLoginModuleIsRead(): void
    {
        $driver = $this->createStub(DatabaseDriver::class);
        $driver->method('quoteName')->willReturnCallback(static fn ($name, $as = null) => '`' . $name . '`');
        $driver->method('quote')->willReturnCallback(static fn ($text) => "'" . $text . "'");
        $driver->method('createQuery')->willReturnCallback(static fn () => new MysqliQuery($driver));

        $queries = [];
        $driver->method('setQuery')->willReturnCallback(function ($query) use (&$queries, $driver) {
            $queries[] = (string) $query;

            return $driver;
        });
        $driver->method('loadResult')->willReturnCallback(static function () use (&$queries) {
            return $queries ? '{"remember_enabled":"0"}' : 'the last query of the request';
        });

        $container = new Container();
        $container->share('DatabaseDriver', static fn () => $driver);
        Factory::$container = $container;

        $remember = (new ReflectionClass(KunenaLoginComprofiler::class))->newInstanceWithoutConstructor()->getRememberMe();

        $this->assertFalse($remember);
        $this->assertCount(1, $queries);
        $this->assertStringContainsString("FROM `#__modules`", $queries[0]);
        $this->assertStringContainsString("`module` = 'mod_cblogin'", $queries[0]);
    }
}
