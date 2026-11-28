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

namespace Kunena\Tests\Unit\Component\Site\Controller;

use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The mail of a ban goes when the ban begins or ends, when the forum sends e-mails at all (sendEmails, as every other mail of the
 * forum), to an address. It went also with the e-mails of the forum off, and again at each change of an existing ban (a new end or a
 * new reason): "Your account has just been banned" a second time. (The task sends mails: its code is read.)
 */
class UserBanMailTest extends UnitTestCase
{
    public function testTheMailGoesWhenTheBanBeginsOrEnds(): void
    {
        $source = str_replace("\r\n", "\n", Files::read('src/site/src/Controllers/UserController.php'));

        $state = strpos($source, '$wasBanned = $ban->id && $ban->isEnabled();');
        $save  = strpos($source, '$ban->ban($user->userid, $ip, $banlevel, $expiration, $reason_private, $reason_public, $comment);');

        $this->assertNotFalse($state);
        $this->assertLessThan($save, $state);

        $this->assertStringContainsString(
            "\$this->config->sendMailUserBanned && \$this->config->sendEmails\n                && \$wasBanned !== \$ban->isEnabled() && MailHelper::isEmailAddress((string) \$user->email)",
            $source
        );
    }
}
