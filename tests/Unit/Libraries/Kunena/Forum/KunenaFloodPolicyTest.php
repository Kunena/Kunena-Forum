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

use Kunena\Forum\Libraries\Forum\KunenaFloodPolicy;
use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * The flood protection (Kunena 7.1): the time a poster waits between two consecutive posts. It was checked for a new topic only
 * ("&& $isNew") and by the IP address only, so a bot could fill a thread (and mail every subscriber) with replies.
 */
class KunenaFloodPolicyTest extends UnitTestCase
{
    public static function cases(): array
    {
        $now = 1_800_000_000;

        // now, setting, times of the last post, seconds to wait
        return [
            'off (0)'                                => [$now, 0, [$now - 1], 0],
            'off (negative)'                         => [$now, -5, [$now - 1], 0],
            'nothing posted before'                  => [$now, 30, [], 0],
            'only empty values'                      => [$now, 30, [0, null, '', false], 0],
            'posted one second ago'                  => [$now, 30, [$now - 1], 29],
            'posted just now'                        => [$now, 30, [$now], 30],
            'posted exactly the setting ago'         => [$now, 30, [$now - 30], 0],
            'posted longer ago'                      => [$now, 30, [$now - 3600], 0],
            'the latest of several places counts'    => [$now, 30, [$now - 3600, $now - 5, 0], 25],
            'the session is newer than the database' => [$now, 60, [$now - 600, $now - 2], 58],
            'times as strings (from the database)'   => [$now, 30, [(string) ($now - 7)], 23],
            'a time in the future is capped'         => [$now, 30, [$now + 100000], 30],
            'a negative time is ignored'             => [$now, 30, [-100], 0],
        ];
    }

    #[DataProvider('cases')]
    public function testTheSecondsToWait(int $now, int $window, array $times, int $expected): void
    {
        $this->assertSame($expected, KunenaFloodPolicy::secondsToWait($now, $window, $times));
    }

    public function testABotWhichFloodsAThreadIsSlowedDown(): void
    {
        $now   = 1_800_000_000;
        $posts = 0;
        $last  = [];

        // One post a second for ten minutes.
        for ($t = 0; $t < 600; $t++) {
            if (KunenaFloodPolicy::secondsToWait($now + $t, 30, $last) === 0) {
                $posts++;
                $last = [$now + $t];
            }
        }

        $this->assertSame(20, $posts);
    }

    public function testThePostTaskChecksTheRepliesToo(): void
    {
        $source = Files::read('src/site/src/Controllers/TopicController.php');

        $this->assertSame(1, preg_match('/public function post\(\)\s*\{(.*?)\n    \}\n/s', $source, $m));
        $this->assertStringContainsString(
            'if ($this->config->floodProtection && !$this->me->isModerator($category)) {',
            $m[1]
        );
        $this->assertStringNotContainsString('floodProtection && !$this->me->isModerator($category) && $isNew', $m[1]);
        $this->assertStringContainsString('KunenaFloodPolicy::secondsToWait(', $m[1]);
        // The time of the post is remembered for a post which keeps no user and no address (anonymous).
        $this->assertStringContainsString("setUserState('com_kunena.lastposttime', \$dateNowUnix)", $m[1]);
    }

    public function testTheApiHasTheSameRule(): void
    {
        $this->assertStringContainsString(
            'KunenaFloodPolicy::secondsToWait($now, $seconds, [$lastPostTime])',
            Files::read('src/libraries/kunena/src/Api/KunenaApiReply.php')
        );
    }
}
