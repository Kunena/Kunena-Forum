<?php

/**
 * Kunena Component
 *
 * Regression test: an offline board and a forum for registered users only must also hide the raw and json output.
 * Those views have no common view, and the checks used to run only when there was one.
 *
 * @package         Kunena.Tests
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 **/

namespace Kunena\Tests\Unit\Access;

use Kunena\Forum\Libraries\Access\KunenaForumAvailability;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ForumAvailabilityTest extends TestCase
{
    private const VIEW = __DIR__ . '/../../../src/libraries/kunena/src/View/KunenaView.php';

    public static function setUpBeforeClass(): void
    {
        if (!\defined('_JEXEC')) {
            \define('_JEXEC', 1);
        }

        require_once __DIR__ . '/../../../src/libraries/kunena/src/Access/KunenaForumAvailability.php';
    }

    #[DataProvider('cases')]
    public function testStatus(bool $offline, bool $isAdmin, bool $regOnly, bool $isMember, bool $teaser, ?int $expected): void
    {
        $this->assertSame($expected, KunenaForumAvailability::getBlockingStatus($offline, $isAdmin, $regOnly, $isMember, $teaser));
    }

    public static function cases(): array
    {
        //                          offline admin  regOnly member teaser expected
        return [
            'open forum, guest'              => [false, false, false, false, false, null],
            'open forum, member'             => [false, false, false, true, false, null],
            'offline, guest'                 => [true, false, false, false, false, 503],
            'offline, member'                => [true, false, false, true, false, 503],
            'offline, administrator'         => [true, true, false, true, false, null],
            'registered only, guest'         => [false, false, true, false, false, 403],
            'registered only, member'        => [false, false, true, true, false, null],
            'registered only, guest teaser'  => [false, false, true, false, true, null],
            'offline wins over registered'   => [true, false, true, false, false, 503],
            'offline, teaser is no excuse'   => [true, false, false, false, true, 503],
            'offline and registered, admin'  => [true, true, true, true, false, null],
        ];
    }

    private function displayLayout(): string
    {
        $source = file_get_contents(self::VIEW);

        $this->assertSame(1, preg_match('/public function displayLayout\([^)]*\)\s*\{(.*?)\n    \}\n/s', $source, $m));

        return $m[1];
    }

    public function testRawAndJsonViewsAreCheckedBeforeTheViewRuns(): void
    {
        $body = $this->displayLayout();

        $check = strpos($body, 'KunenaForumAvailability::getBlockingStatus(');
        $run   = strpos($body, '$this->$layoutFunction($tpl)');

        $this->assertNotFalse($check, 'displayLayout must ask KunenaForumAvailability.');
        $this->assertNotFalse($run);
        $this->assertLessThan($run, $check, 'The check has to run before the view method.');
    }

    public function testTheCheckAppliesWhenThereIsNoCommonView(): void
    {
        $body = $this->displayLayout();

        // The branch for the formats without a common view (raw, json).
        $this->assertMatchesRegularExpression('/if \(!\$this->embedded && !isset\(\$this->common\)\)\s*\{\s*\$status = KunenaForumAvailability::getBlockingStatus\(/s', $body);
    }

    public function testBlockedRequestStopsTheView(): void
    {
        $body = $this->displayLayout();

        $this->assertMatchesRegularExpression('/\$this->sendBlocked\(\$status\);.*?return;/s', $body);
    }

    public function testTheRealConfigurationIsUsed(): void
    {
        $body = $this->displayLayout();

        // Only the new branch (raw, json), not the html one which has the same settings further down.
        $this->assertSame(1, preg_match('/!isset\(\$this->common\)\)\s*\{(.*?)\$this->sendBlocked/s', $body, $m));

        foreach (['$this->config->boardOffline', '$this->me->isAdmin()', '$this->config->regOnly', '$this->me->exists()', '$this->teaser'] as $needle) {
            $this->assertStringContainsString($needle, $m[1], $needle);
        }
    }

    public function testBlockedAnswerIsAnErrorStatus(): void
    {
        $source = file_get_contents(self::VIEW);

        $this->assertSame(1, preg_match('/function sendBlocked\([^)]*\)[^{]*\{(.*?)\n    \}\n/s', $source, $m));
        $this->assertStringContainsString("'503 Service Temporarily Unavailable'", $m[1]);
        $this->assertStringContainsString("'403 Forbidden'", $m[1]);
        $this->assertStringContainsString('application/json', $m[1]);
        $this->assertStringContainsString('json_encode(', $m[1], 'The json answer has to be valid json.');
    }

    public function testHtmlBehaviourIsKept(): void
    {
        $body = $this->displayLayout();

        // The html page still shows the forum offline / login message through the common view.
        $this->assertStringContainsString("\$this->common->header = Text::_('COM_KUNENA_FORUM_IS_OFFLINE');", $body);
        $this->assertStringContainsString("\$this->common->header = Text::_('COM_KUNENA_LOGIN_NOTIFICATION');", $body);
    }
}
