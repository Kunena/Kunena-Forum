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

namespace Kunena\Tests\Integration\Libraries\Kunena\Tools;

use Kunena\Forum\Libraries\Tools\KunenaUserMerge;
use Kunena\Tests\Integration\DBTestInterface;
use Kunena\Tests\Integration\DBTestTrait;
use Kunena\Tests\Integration\IntegrationTestCase;
use ReflectionMethod;

/**
 * The tool "Merge users" and the polls: when both accounts voted in a poll, the vote of the merged account is dropped (a user
 * has one row in a poll). Its option kept the vote, so the person behind both accounts stayed counted twice.
 */
class KunenaUserMergePollVotesTest extends IntegrationTestCase implements DBTestInterface
{
    use DBTestTrait;

    private const FROM = 101;

    private const INTO = 102;

    protected function setUp(): void
    {
        parent::setUp();

        $this->insertRows('#__kunena_polls_options', [
            ['id' => 11, 'pollid' => 4, 'text' => 'Yes', 'votes' => 2],
            ['id' => 12, 'pollid' => 4, 'text' => 'No', 'votes' => 1],
            ['id' => 21, 'pollid' => 5, 'text' => 'Yes', 'votes' => 1],
            ['id' => 22, 'pollid' => 5, 'text' => 'No', 'votes' => 3],
        ]);
        $this->insertRows('#__kunena_polls_users', [
            // Both accounts voted in the poll 4: the first one for "Yes" (with another user), the second one for "No".
            ['pollid' => 4, 'userid' => self::FROM, 'votes' => 1, 'lastvote' => 11, 'lasttime' => '2026-01-01 00:00:00'],
            ['pollid' => 4, 'userid' => self::INTO, 'votes' => 1, 'lastvote' => 12, 'lasttime' => '2026-01-01 00:00:00'],
            ['pollid' => 4, 'userid' => 103, 'votes' => 1, 'lastvote' => 11, 'lasttime' => '2026-01-01 00:00:00'],
            // Only the first account voted in the poll 5.
            ['pollid' => 5, 'userid' => self::FROM, 'votes' => 1, 'lastvote' => 21, 'lasttime' => '2026-01-01 00:00:00'],
        ]);
    }

    public function testTheDroppedVoteOfTheMergedAccountIsTakenOffItsOption(): void
    {
        $moved = (new ReflectionMethod(KunenaUserMerge::class, 'moveKeyed'))
            ->invoke(null, $this->getDBDriver(), '#__kunena_polls_users', 'userid', 'pollid', self::FROM, self::INTO);

        // The vote in the poll 5 is moved, the one in the poll 4 is dropped.
        $this->assertSame(1, $moved);
        $this->assertSame(
            [[4, self::INTO, 12], [4, 103, 11], [5, self::INTO, 21]],
            array_map(
                static fn ($row) => array_map('intval', array_values($row)),
                $this->fetchAll('SELECT pollid, userid, lastvote FROM #__kunena_polls_users ORDER BY pollid, userid')
            )
        );

        // The options have the votes of the voters who are left: the moved vote is still counted, the dropped one is not.
        $this->assertSame(
            ['11' => 1, '12' => 1, '21' => 1, '22' => 3],
            array_map('intval', array_column($this->fetchAll('SELECT id, votes FROM #__kunena_polls_options ORDER BY id'), 'votes', 'id'))
        );
    }
}
