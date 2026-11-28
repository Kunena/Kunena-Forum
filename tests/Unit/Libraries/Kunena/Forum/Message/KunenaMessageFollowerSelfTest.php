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

namespace Kunena\Tests\Unit\Libraries\Kunena\Forum\Message;

use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The e-mail of a new topic to the followers of its author leaves out the current user, as the e-mail to the subscribers does
 * (notificationExcludeList()): a moderator who follows a user got the e-mail of the topic of that user which he had just approved
 * himself. (The notifications need a whole site: the code is read.)
 */
class KunenaMessageFollowerSelfTest extends UnitTestCase
{
    public function testTheCurrentUserGetsNoFollowerEmail(): void
    {
        $source = str_replace("\r\n", "\n", Files::read('src/libraries/kunena/src/Forum/Message/KunenaMessage.php'));

        $loop = strpos($source, 'foreach (KunenaUserFollow::getRecipients((int) $this->userid) as $follower) {');

        $this->assertNotFalse($loop);
        $this->assertStringContainsString('|| $followerId === (int) KunenaUserHelper::getMyself()->userid', substr($source, $loop, 700));
    }
}
