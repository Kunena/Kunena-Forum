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

namespace Kunena\Tests\Integration\Libraries\Kunena\Forum\Topic\Poll;

use Kunena\Forum\Libraries\Forum\Topic\Poll\KunenaPoll;
use Kunena\Tests\Integration\DBTestInterface;
use Kunena\Tests\Integration\DBTestTrait;
use Kunena\Tests\Integration\IntegrationTestCase;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;

/**
 * The votes of an option which the edit of a poll removes ("TODO: Votes in #__kunena_polls_users will be off"): a user whose only
 * vote it was had voted for nothing which is counted and could not vote again.
 */
class KunenaPollRemovedOptionTest extends IntegrationTestCase implements DBTestInterface
{
    use DBTestTrait;

    private const POLL = 4;

    private const REMOVED = 11;

    private const KEPT = 12;

    protected function setUp(): void
    {
        parent::setUp();

        $this->insertRows('#__kunena_polls_users', [
            // His only vote was for the removed option.
            ['pollid' => self::POLL, 'userid' => 101, 'votes' => 1, 'lastvote' => self::REMOVED, 'lasttime' => '2026-01-01 00:00:00'],
            // Two votes, the last one for the removed option.
            ['pollid' => self::POLL, 'userid' => 102, 'votes' => 2, 'lastvote' => self::REMOVED, 'lasttime' => '2026-01-01 00:00:00'],
            // A vote for the kept option, and a vote of another poll for an option of the same id.
            ['pollid' => self::POLL, 'userid' => 103, 'votes' => 1, 'lastvote' => self::KEPT, 'lasttime' => '2026-01-01 00:00:00'],
            ['pollid' => 5, 'userid' => 101, 'votes' => 1, 'lastvote' => self::REMOVED, 'lasttime' => '2026-01-01 00:00:00'],
        ]);
    }

    public function testTheVotesOfARemovedOptionGo(): void
    {
        $poll = (new ReflectionClass(KunenaPoll::class))->newInstanceWithoutConstructor();
        (new ReflectionProperty(KunenaPoll::class, '_db'))->setValue($poll, $this->getDBDriver());
        $poll->id = self::POLL;

        (new ReflectionMethod(KunenaPoll::class, 'dropVotesOf'))->invoke($poll, self::REMOVED);

        $this->assertSame(
            [[4, 102, 1, 0], [4, 103, 1, self::KEPT], [5, 101, 1, self::REMOVED]],
            array_map(
                static fn ($row) => array_map('intval', array_values($row)),
                $this->fetchAll('SELECT pollid, userid, votes, lastvote FROM #__kunena_polls_users ORDER BY pollid, userid')
            )
        );
    }
}
