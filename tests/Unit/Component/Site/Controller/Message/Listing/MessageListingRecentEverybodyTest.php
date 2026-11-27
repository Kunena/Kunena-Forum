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

namespace Kunena\Tests\Unit\Component\Site\Controller\Message\Listing;

use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * A list of posts of the user 0 is the list of everybody (a menu item "recent posts" or "deleted posts" without a user, as the
 * feeds). The user 0 became the user of the guests, and the list had only the posts of guests and of anonymous posters. (The list
 * needs a whole site: its code is read.)
 */
class MessageListingRecentEverybodyTest extends UnitTestCase
{
    public function testTheUserZeroIsNoFilter(): void
    {
        $source = Files::read('src/site/src/Controller/Message/Listing/Recent/MessageListingRecentDisplay.php');

        $this->assertStringContainsString('$user = is_numeric($userid) && (int) $userid > 0 ? KunenaUserHelper::get($userid) : null;', $source);
    }
}
