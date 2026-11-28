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

namespace Kunena\Tests\Unit\Component\Site\Template\Agora;

use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The layoutsets App and Chat of Agora:
 * - the tile of the new topics, the latest activity and the pane of the latest topics leave out the topics of the users whom the
 *   visitor ignores, as the lists do (they were counted and shown);
 * - the tiles link to the list of what they count: the new topics of the last 24 hours (the list had the period of the settings, a
 *   month), all the posts and thank yous of the member (the lists stopped at the last month).
 * (The layouts need a whole site: their code is read.)
 */
class AgoraAppIgnoredAndPeriodsTest extends UnitTestCase
{
    private const APP = 'src/site/template/agora/layoutsets/app/pages/category/list/default.php';

    private const CHAT = 'src/site/template/agora/layoutsets/chat/layouts/topic/item/default.php';

    public function testTheTopicsOfIgnoredUsersAreLeftOut(): void
    {
        $app = Files::read(self::APP);

        $this->assertStringContainsString('KunenaUserIgnore::getIgnored((int) $me->userid)', $app);
        $this->assertSame(2, substr_count($app, "'where' => \$ignoredOut"));

        $chat = Files::read(self::CHAT);

        $this->assertStringContainsString('KunenaUserIgnore::getIgnored(', $chat);
        $this->assertStringContainsString("['where' => \$paneIgnored ?", $chat);
    }

    public function testTheTilesLinkToTheListsOfWhatTheyCount(): void
    {
        $app = Files::read(self::APP);

        $this->assertStringContainsString("view=topics&layout=default&mode=topics&sel=24'", $app);
        $this->assertStringContainsString('mode=recent&sel=-1&userid=', $app);
        $this->assertStringContainsString('mode=thankyou&sel=-1&userid=', $app);
    }
}
