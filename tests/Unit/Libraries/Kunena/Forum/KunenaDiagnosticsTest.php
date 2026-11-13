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

namespace Kunena\Tests\Unit\Libraries\Kunena\Forum;

use Joomla\CMS\Factory;
use Joomla\Database\DatabaseDriver;
use Joomla\Database\DatabaseInterface;
use Joomla\Database\DatabaseQuery;
use Joomla\DI\Container;
use Kunena\Forum\Libraries\Forum\KunenaDiagnostics;
use Kunena\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * The backend shows the rows of a failed check (issue #6245): the name of the check comes from the request, so only the names
 * of getList() are accepted, and at most ITEMS_LIMIT rows are loaded.
 */
class KunenaDiagnosticsTest extends UnitTestCase
{
    public function testIsCheckAcceptsEveryCheckOfTheList(): void
    {
        foreach (KunenaDiagnostics::getList() as $name) {
            $this->assertTrue(KunenaDiagnostics::isCheck($name), $name);
        }
    }

    public static function notChecks(): array
    {
        return [
            'empty'            => [''],
            'unknown'          => ['nonsense'],
            'another case'     => ['AttachmentOrphaned'],
            'a query method'   => ['query_attachmentOrphaned'],
            'a fields method'  => ['fields_attachmentOrphaned'],
            'a delete method'  => ['delete_attachmentOrphaned'],
            'a path'           => ['../attachmentOrphaned'],
            'sql characters'   => ["attachmentOrphaned' OR 1=1 --"],
            'a trailing space' => ['attachmentOrphaned '],
        ];
    }

    #[DataProvider('notChecks')]
    public function testIsCheckRejectsAnyOtherName(string $name): void
    {
        $this->assertFalse(KunenaDiagnostics::isCheck($name));
    }

    public function testTheLimitOfTheItemsIsPositive(): void
    {
        $this->assertGreaterThan(0, KunenaDiagnostics::ITEMS_LIMIT);
    }

    public static function limits(): array
    {
        return [
            'the limit of the page' => [KunenaDiagnostics::ITEMS_LIMIT, KunenaDiagnostics::ITEMS_LIMIT],
            'another limit'         => [5, 5],
            'no limit'              => [null, 0],
        ];
    }

    /**
     * getItems() gives its limit to the database, and asks for all the rows without one.
     */
    #[DataProvider('limits')]
    public function testGetItemsAsksTheDatabaseForAtMostTheLimit(?int $limit, int $expected): void
    {
        $this->requireJoomla();

        $query = $this->createStub(DatabaseQuery::class);

        foreach (['select', 'from', 'leftJoin', 'where'] as $method) {
            $query->method($method)->willReturnSelf();
        }

        $limits = [];
        $db     = $this->createStub(DatabaseDriver::class);
        $db->method('createQuery')->willReturn($query);
        $db->method('setQuery')->willReturnCallback(
            function ($query, $offset = 0, $limit = 0) use (&$limits, $db) {
                $limits[] = [$offset, $limit];

                return $db;
            }
        );
        $db->method('loadAssocList')->willReturn([['id' => 1, 'mesid' => 999999901]]);

        $saved              = Factory::$container;
        Factory::$container = (new Container())->set(DatabaseInterface::class, $db)->set('DatabaseDriver', $db);

        try {
            $rows = $limit === null
                ? KunenaDiagnostics::getItems('attachmentOrphaned')
                : KunenaDiagnostics::getItems('attachmentOrphaned', $limit);
        } finally {
            Factory::$container = $saved;
        }

        $this->assertSame([['id' => 1, 'mesid' => 999999901]], $rows);
        $this->assertSame([[0, $expected]], $limits);
    }
}
