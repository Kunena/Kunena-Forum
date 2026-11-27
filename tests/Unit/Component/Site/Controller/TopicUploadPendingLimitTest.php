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
 * The limit of the files which wait for a message is checked again when a file is complete, before it becomes an attachment. It
 * was checked at the first part of a file only, and a file is counted once it is complete: the first parts of many files (sent at
 * once, or one after the other before their last parts) all passed, and the limit was passed. (The upload needs posted parts of a
 * file: its code is read.)
 */
class TopicUploadPendingLimitTest extends UnitTestCase
{
    public function testTheLimitIsCheckedAtTheStartAndWhenTheFileIsComplete(): void
    {
        $source = str_replace("\r\n", "\n", Files::read('src/site/src/Controllers/TopicController.php'));

        $this->assertSame(1, preg_match('/    public function upload\(\)\s*\{(.*?)\n    \}\n/s', $source, $m));

        $this->assertMatchesRegularExpression("/if \(\\\$this->input->getInt\('chunkStart', 0\) === 0\) \{\n\s*\\\$this->checkPendingLimit\(\);/", $m[1]);

        $completed = strpos($m[1], 'if (!empty($response->completed)) {');
        $check     = strpos($m[1], '$this->checkPendingLimit();', (int) $completed);
        $create    = strpos($m[1], '$attachment                 = new KunenaAttachment();');

        $this->assertNotFalse($completed);
        $this->assertNotFalse($check);
        $this->assertLessThan($create, $check, 'The limit is checked before the attachment is made.');

        $this->assertStringContainsString('protected function checkPendingLimit(): void', $source);
    }
}
