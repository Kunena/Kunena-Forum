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
 * Text which is escaped for HTML is escaped once. Two kinds were escaped twice:
 * - The address of a post in an e-mail (the notification of an approved post, the digest): getPermaUrl() escapes by default, and
 *   the mail escaped it again or sent it as plain text. On a site without SEF addresses the link had "&amp;" between its values
 *   and led nowhere.
 * - The name of a user (the digest, the list of attachments and a message of the categories in the backend): getName() escapes by
 *   default too, so "Tom & Jerry" was shown as "Tom &amp; Jerry".
 * (The mails need the router and the mailer of a site: the code is read. htmlspecialchars() itself is shown below.)
 */
class MailLinksAndNamesTest extends UnitTestCase
{
    public function testEscapingTwiceBreaksAnAddressAndAName(): void
    {
        $url = 'index.php?option=com_kunena&view=topic&id=5';

        $this->assertSame('index.php?option=com_kunena&amp;amp;view=topic&amp;amp;id=5', htmlspecialchars(htmlspecialchars($url)));
        $this->assertSame('Tom &amp;amp; Jerry', htmlspecialchars(htmlspecialchars('Tom & Jerry')));
    }

    public function testTheAddressOfAPostInAMailIsNotEscapedBeforeTheMail(): void
    {
        $message = Files::read('src/libraries/kunena/src/Forum/Message/KunenaMessage.php');
        $digest  = Files::read('src/libraries/kunena/src/Email/KunenaDigest.php');

        $this->assertStringContainsString(". \$this->getPermaUrl(null, false);\n        }\n\n        \$topic = \$this->getTopic();", str_replace("\r\n", "\n", $message));
        $this->assertSame(0, preg_match('/getPermaUrl\(\)/', $message), 'No escaped address in the notifications.');
        $this->assertSame(2, substr_count($digest, '->getPermaUrl(null, false))'));
        $this->assertSame(0, preg_match('/getPermaUrl\(\)/', $digest), 'No escaped address in the digest.');
    }

    public function testTheNameOfAUserIsEscapedOnce(): void
    {
        foreach (['src/libraries/kunena/src/Email/KunenaDigest.php', 'src/admin/src/Controller/CategoryController.php', 'src/admin/tmpl/attachments/default.php'] as $file) {
            $source = Files::read($file);

            $this->assertSame(0, preg_match('/escape\([^;\n]*->getName\(\)\)/', $source), $file);
            $this->assertSame(1, preg_match('/escape\([^;\n]*->getName\(\'\', false\)\)/', $source), $file);
        }
    }
}
