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
 * The delete for good of the selected messages of a list takes their files off the number of files of the topic, as the delete for
 * good of one message does: the files went with the messages, the topic kept counting them (the paperclip of the lists stayed).
 * (The task needs a whole site: its code is read.)
 */
class TopicsPermdelPostsAttachmentsTest extends UnitTestCase
{
    public function testTheFilesOfTheMessagesAreTakenOffTheTopic(): void
    {
        $source = str_replace("\r\n", "\n", Files::read('src/site/src/Controllers/TopicsController.php'));
        $start  = strpos($source, '    public function permdel_posts()');
        $body   = substr($source, $start, (int) strpos($source, "\n    }\n", $start) - $start);

        $count = strpos($body, "\$files = \\count(\$message->getAttachments(false, 'none'));");

        $this->assertNotFalse($count);
        $this->assertLessThan(strpos($body, '$message->delete();'), $count);
        $this->assertStringContainsString('$topic->attachments = max(0, (int) $topic->attachments - $files);', $body);
        $this->assertStringContainsString('$topic->save(false);', $body);
    }
}
