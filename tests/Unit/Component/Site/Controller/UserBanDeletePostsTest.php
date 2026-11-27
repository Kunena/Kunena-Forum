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
 * The delete of the posts of a user with a ban. "Delete permanently" also takes the posts in the trash: with both boxes ticked, the
 * first one put the posts in the trash and the second found none (it read only the published and the unapproved ones), so nothing
 * was deleted for good although the message said so. And the posts of all the languages: on a multilingual site those in the topics
 * of another language than the page were left. (The task needs a whole site: its code is read.)
 */
class UserBanDeletePostsTest extends UnitTestCase
{
    public function testThePostsOfAllModesAndLanguagesAreTaken(): void
    {
        $source = str_replace("\r\n", "\n", Files::read('src/site/src/Controllers/UserController.php'));

        $this->assertStringContainsString("\$this->messagesOf((int) \$user->userid, ['recent', 'unapproved']);", $source);
        $this->assertStringContainsString("\$this->messagesOf((int) \$user->userid, ['recent', 'unapproved', 'deleted']);", $source);

        $this->assertSame(1, preg_match('/protected function messagesOf\(int \$userid, array \$modes\): array\n    \{(.*?)\n    \}\n/s', $source, $m));
        $this->assertStringContainsString("'mode' => \$mode, 'language' => 'all']", $m[1]);
    }
}
