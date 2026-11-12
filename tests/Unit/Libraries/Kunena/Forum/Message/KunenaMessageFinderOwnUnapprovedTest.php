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

namespace Kunena\Tests\Unit\Libraries\Kunena\Forum\Message;

use Joomla\Database\DatabaseDriver;
use Joomla\Database\QueryInterface;
use Kunena\Forum\Libraries\Forum\Message\KunenaMessageFinder;
use Kunena\Forum\Libraries\User\KunenaUser;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The hold filter of the messages of a topic which waits for approval: its author finds his own unapproved messages too
 * (issue #5381), never those of the others.
 */
class KunenaMessageFinderOwnUnapprovedTest extends UnitTestCase
{
    /**
     * The conditions which the finder adds to the query.
     *
     * @return  string[]
     */
    private function where(callable $filter): array
    {
        // The finder and the user need the framework of Joomla.
        $this->requireJoomla();

        $db = $this->createStub(DatabaseDriver::class);
        $db->method('quoteName')->willReturnCallback(static fn ($name) => '`' . str_replace('.', '`.`', $name) . '`');

        $finder = self::bare(KunenaMessageFinder::class, ['db' => $db]);
        $filter($finder);

        $where = [];
        $query = $this->createStub(QueryInterface::class);
        $query->method('where')->willReturnCallback(static function ($condition) use (&$where, $query) {
            $where[] = $condition;

            return $query;
        });

        $this->invoke($finder, 'build', $query);

        return $where;
    }

    private static function user(int $userid, bool $exists = true): KunenaUser
    {
        return self::bare(KunenaUser::class, ['userid' => $userid, '_exists' => $exists]);
    }

    public function testTheAuthorFindsThePublishedMessagesAndHisOwnUnapprovedOnes(): void
    {
        $where = $this->where(static fn (KunenaMessageFinder $finder) => $finder->filterByHoldOrOwnUnapproved([0], self::user(42)));

        $this->assertSame(['(`a`.`hold` IN (0) OR (`a`.`hold` = 1 AND `a`.`userid` = 42))'], $where);
    }

    public function testAGuestFindsOnlyTheHoldsOfTheList(): void
    {
        $where = $this->where(static fn (KunenaMessageFinder $finder) => $finder->filterByHoldOrOwnUnapproved([0], self::user(0, false)));

        $this->assertSame(['`a`.`hold` IN (0)'], $where);
    }

    public function testTheHoldFilterAloneIsUnchanged(): void
    {
        $where = $this->where(static fn (KunenaMessageFinder $finder) => $finder->filterByHold([0, 1]));

        $this->assertSame(['`a`.`hold` IN (0,1)'], $where);
    }
}
