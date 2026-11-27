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
 * The delete of files from the list of the attachments of a profile removes a file of a message only for who may edit the
 * message, as the editor does: the rule of the file alone (its uploader) let the author take it out of a post which he may edit no
 * more (after the edit time, in a locked topic, in read only mode). (The task needs a whole site: its code is read.)
 */
class UserDeleteFileEditRightTest extends UnitTestCase
{
    public function testAFileOfAMessageIsRemovedOnlyByWhoMayEditTheMessage(): void
    {
        $source = str_replace("\r\n", "\n", Files::read('src/site/src/Controllers/UserController.php'));

        $this->assertSame(1, preg_match('/public function delfile\(\)\s*\{(.*?)\n    \}\n/s', $source, $m));

        $check  = strpos($m[1], "if (!\$message->isAuthorised('edit')) {");
        $remove = strpos($m[1], '$message->removeAttachments([$attachment->id]);');

        $this->assertNotFalse($check);
        $this->assertLessThan($remove, $check);
    }
}
