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

namespace Kunena\Tests\Unit\Libraries\Kunena\Email;

use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The digest leaves out the posts of a user whom the subscriber ignores when it is sent: the posts are collected when they are posted,
 * and a user ignored after that (the same day) was still in the digest of the evening, while the e-mails of the subscriptions leave
 * him out. (The digest sends e-mails: its code is read.)
 */
class KunenaDigestIgnoredTest extends UnitTestCase
{
    public function testThePostsOfAnIgnoredUserAreLeftOutWhenTheDigestIsSent(): void
    {
        $source = str_replace("\r\n", "\n", Files::read('src/libraries/kunena/src/Email/KunenaDigest.php'));

        $send = strpos($source, 'public static function send(KunenaUser $user, array $ids): bool');

        $this->assertNotFalse($send);
        $this->assertStringContainsString('if (KunenaUserIgnore::isIgnoring((int) $user->userid, (int) $message->userid)) {', substr($source, $send, 2000));
    }
}
