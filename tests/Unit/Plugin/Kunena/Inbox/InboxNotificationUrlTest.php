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

namespace Kunena\Tests\Unit\Plugin\Kunena\Inbox;

use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The link of a notification of the inbox is the address as text: the list gives it in JSON and its script escapes it, so the
 * address as HTML (&amp; for each &) became &amp;amp; in the page, and on a site without SEF addresses the click on a notification
 * opened the wrong page (as the popups already do it). (The list needs a whole site: its code is read.)
 */
class InboxNotificationUrlTest extends UnitTestCase
{
    public function testTheLinkIsText(): void
    {
        $source = Files::read('src/plugins/plg_kunena_inbox/src/Helper/InboxHelper.php');

        $this->assertStringContainsString('$url  = (string) $message->getPermaUrl(null, false);', $source);
        $this->assertStringNotContainsString('$url  = (string) $message->getPermaUrl();', $source);
    }
}
