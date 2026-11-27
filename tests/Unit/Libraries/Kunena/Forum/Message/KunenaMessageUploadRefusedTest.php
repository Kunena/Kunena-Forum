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
 * A file of the posting form without JavaScript which may not be uploaded (an extension which is not allowed, or no upload at all
 * for the user: a guest with the defaults) is a message, and the post is kept. The list of the extensions was false for a user
 * without uploads (a TypeError of KunenaUpload::getInstance()), and the refusal of an extension was an exception: neither was
 * caught by the task, and the post was lost on an error page. (The upload needs a posted file and the folders of a site: its code
 * is read.)
 */
class KunenaMessageUploadRefusedTest extends UnitTestCase
{
    public function testARefusedFileIsAMessageAndThePostIsKept(): void
    {
        $message = str_replace("\r\n", "\n", Files::read('src/libraries/kunena/src/Forum/Message/KunenaMessage.php'));

        $this->assertSame(1, preg_match('/public function uploadAttachment\(.*?\n    \}\n/s', $message, $m));
        $this->assertMatchesRegularExpression('/try \{\n\s*\$success = \$attachment->upload\(\$postvar, \$catid\);\n\s*\} catch \(RuntimeException \$e\) \{\n\s*Factory::getApplication\(\)->enqueueMessage\(\$e->getMessage\(\), \'warning\'\);/', $m[0]);

        $attachment = str_replace("\r\n", "\n", Files::read('src/libraries/kunena/src/Attachment/KunenaAttachment.php'));

        $this->assertMatchesRegularExpression('/if \(\$extensions === false\) \{\n\s*throw new RuntimeException\(/', $attachment);
        $this->assertStringContainsString('KunenaUpload::getInstance($extensions);', $attachment);
    }
}
