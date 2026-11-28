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
 * The layoutsets App and Chat of Agora: the tile of the new topics, the latest activity and the pane of the latest topics leave
 * out the topics of the users whom the visitor ignores, as the lists do (they were counted and shown).
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
}
