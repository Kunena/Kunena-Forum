<?php

/**
 * Kunena Component
 *
 * Regression test: the setting "Flood Protection" is the time a user has to wait between two consecutive posts, but it was
 * only checked for a new topic ("&& $isNew"), never for a reply, and it looked at the IP address only. A bot could fill a
 * thread (and mail every subscriber) with replies. It now covers every post, and counts the posts of the user (the IP
 * address only for a guest).
 *
 * @package         Kunena.Tests
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 **/

namespace Kunena\Tests\Unit\Forum;

use Kunena\Forum\Libraries\Forum\KunenaFloodPolicy;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class FloodProtectionTest extends TestCase
{
    private const CONTROLLER = __DIR__ . '/../../../src/site/src/Controllers/TopicController.php';

    public static function setUpBeforeClass(): void
    {
        if (!\defined('_JEXEC')) {
            \define('_JEXEC', 1);
        }

        require_once __DIR__ . '/../../../src/libraries/kunena/src/Forum/KunenaFloodPolicy.php';
    }

    #[DataProvider('cases')]
    public function testSecondsToWait(string $what, int $now, int $window, array $times, int $expected): void
    {
        $this->assertSame($expected, KunenaFloodPolicy::secondsToWait($now, $window, $times), $what);
    }

    public static function cases(): array
    {
        $now = 1_800_000_000;

        return [
            ['off (0)',                                   $now, 0,   [$now - 1],              0],
            ['off (negative)',                            $now, -5,  [$now - 1],              0],
            ['nothing posted before',                     $now, 30,  [],                      0],
            ['only empty values',                         $now, 30,  [0, null, '', false],    0],
            ['posted one second ago',                     $now, 30,  [$now - 1],              29],
            ['posted just now',                           $now, 30,  [$now],                  30],
            ['posted 10 seconds ago',                     $now, 30,  [$now - 10],             20],
            ['posted exactly the window ago',             $now, 30,  [$now - 30],             0],
            ['posted longer ago',                         $now, 30,  [$now - 3600],           0],
            ['the latest of several sources counts',      $now, 30,  [$now - 3600, $now - 5, 0], 25],
            ['the session is newer than the database',    $now, 60,  [$now - 600, $now - 2],  58],
            ['times as strings (from the database)',      $now, 30,  [(string) ($now - 7)],   23],
            ['a time in the future is capped',            $now, 30,  [$now + 100000],         30],
            ['a negative time is ignored',                $now, 30,  [-100],                  0],
        ];
    }

    public function testATopicFollowedByAReplyIsBlocked(): void
    {
        $window = 30;
        $start  = 1_800_000_000;
        $times  = [];

        // Post 1 (a new topic) is allowed ...
        $this->assertSame(0, KunenaFloodPolicy::secondsToWait($start, $window, $times));
        $times[] = $start;

        // ... a reply 5 seconds later is not (this used to be allowed) ...
        $this->assertSame(25, KunenaFloodPolicy::secondsToWait($start + 5, $window, $times));

        // ... and neither is another one at 29 seconds ...
        $this->assertSame(1, KunenaFloodPolicy::secondsToWait($start + 29, $window, $times));

        // ... but at 30 seconds the user may post again.
        $this->assertSame(0, KunenaFloodPolicy::secondsToWait($start + 30, $window, $times));
    }

    public function testABotWhichFloodsAThreadIsSlowedDown(): void
    {
        $window = 30;
        $now    = 1_800_000_000;
        $posts  = 0;
        $last   = [];

        // One post per second for ten minutes.
        for ($t = 0; $t < 600; $t++) {
            if (KunenaFloodPolicy::secondsToWait($now + $t, $window, $last) === 0) {
                $posts++;
                $last = [$now + $t];
            }
        }

        $this->assertSame(20, $posts, 'Twenty posts in ten minutes at 30 seconds between posts, not six hundred.');
    }

    // --- the controller ---

    private function post(): string
    {
        $source = file_get_contents(self::CONTROLLER);

        $this->assertSame(1, preg_match('/public function post\(\)\s*\{(.*?)\n    \}\n/s', $source, $m));

        return $m[1];
    }

    public function testFloodProtectionAppliesToRepliesToo(): void
    {
        $body = $this->post();

        $this->assertSame(1, preg_match('/\/\/ Flood protection[^\n]*\n\s*if \(([^\n]*)\) \{/', $body, $m), 'flood check not found');

        $this->assertStringContainsString('$this->config->floodProtection', $m[1]);
        $this->assertStringContainsString('!$this->me->isModerator($category)', $m[1], 'Moderators are still exempt.');
        $this->assertStringNotContainsString('$isNew', $m[1], 'The check must not be limited to a new topic.');
    }

    public function testTheDecisionComesFromThePolicy(): void
    {
        $body = $this->post();

        $this->assertStringContainsString('KunenaFloodPolicy::secondsToWait($dateNowUnix, (int) $this->config->floodProtection, $this->getLastPostTimes())', $body);
        $this->assertMatchesRegularExpression('/if \(\$wait > 0\) \{.*?COM_KUNENA_POST_TOPIC_FLOOD.*?return;/s', $body, 'A flood stops the post.');
        $this->assertDoesNotMatchRegularExpression('/ip=\'?\s*\.\s*\$this->db->quote\(\$ip\)/', $body, 'The old query by the IP address alone is gone.');
    }

    public function testThePostsOfTheUserAreCounted(): void
    {
        $source = file_get_contents(self::CONTROLLER);

        $this->assertSame(1, preg_match('/protected function getLastPostTimes\(\)[^{]*\{(.*?)\n    \}\n/s', $source, $m));
        $body = $m[1];

        $this->assertStringContainsString("quoteName('userid') . ' = ' . \$userid", $body, 'A logged-in user is counted by the user id.');
        $this->assertStringContainsString("quoteName('userid') . ' = 0 AND ' . \$this->db->quoteName('ip')", $body, 'A guest by the IP address.');
        $this->assertStringContainsString("getUserState('com_kunena.lastposttime'", $body, 'The session covers the anonymous posts.');
        $this->assertStringContainsString('MAX(', $body);
        $this->assertStringContainsString('$ip !== \'\'', $body, 'No post of "any" user with an empty address is matched.');
    }

    public function testTheSessionTimeIsOnlyKeptForAPostWhichWasSaved(): void
    {
        $body = $this->post();

        $this->assertSame(1, preg_match('/try \{\s*\$message->save\(\);(.*?)\} catch/s', $body, $m), 'save block');
        $this->assertStringContainsString("setUserState('com_kunena.lastposttime', \$dateNowUnix)", $m[1]);
    }

    public function testNoOtherPostPathSkipsTheCheck(): void
    {
        $source = file_get_contents(self::CONTROLLER);

        // newTopic()/newReply() are only reached from post(): both go through the check above.
        $this->assertSame(1, substr_count($source, 'newTopic('));
        $this->assertSame(1, substr_count($source, 'newReply('));
    }
}
