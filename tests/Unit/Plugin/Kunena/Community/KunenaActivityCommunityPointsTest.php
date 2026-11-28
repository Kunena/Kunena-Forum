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

namespace Kunena\Tests\Unit\Plugin\Kunena\Community;

use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The points of JomSocial for a new topic and a reply go to the author of the post, as the points of a thank you go to its target.
 * They were given without a user, so JomSocial gave them to the current user: the moderator who approved or restored the post, or the
 * visitor whose request published a scheduled reply. A post of a guest gets none. (The plugin needs JomSocial: its code is read.)
 */
class KunenaActivityCommunityPointsTest extends UnitTestCase
{
    public function testThePointsGoToTheAuthor(): void
    {
        $plugin = Files::read('src/plugins/plg_kunena_community/src/Helper/KunenaActivityCommunity.php');

        foreach (['new', 'reply'] as $action) {
            $this->assertStringContainsString("CUserPoints::assignPoint('com_kunena.thread.{$action}', (int) \$message->userid);", $plugin);
            $this->assertStringNotContainsString("CUserPoints::assignPoint('com_kunena.thread.{$action}');", $plugin);
        }

        // The thank you, which gave the user already.
        $this->assertStringContainsString("CUserPoints::assignPoint('com_kunena.thread.thankyou', \$target);", $plugin);
    }
}
