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
 * Showing a file in the text or not (setinline, removeinlineonattachment) asks the right to edit its message, as the removal of a
 * file does: the rule of the file alone (its uploader) let the author change how the files of a post are shown after the edit time,
 * in a locked topic, in read only mode. (The tasks need a whole site: their code is read.)
 */
class TopicSetInlineEditRightTest extends UnitTestCase
{
    private function method(string $source, string $signature): string
    {
        $start = strpos($source, $signature);
        $this->assertNotFalse($start, $signature);

        return substr($source, $start, (int) strpos($source, "\n    }\n", $start) - $start);
    }

    public function testTheMessageMustBeEditable(): void
    {
        $source = str_replace("\r\n", "\n", Files::read('src/site/src/Controllers/TopicController.php'));

        $check = $this->method($source, '    protected function checkpermissions(KunenaAttachment $attachment)');
        $this->assertStringContainsString('$attachment->getMessage()', $check);
        $this->assertStringContainsString("!\$message->isAuthorised('edit')", $check);

        // Both tasks which change the inline state check it before the change.
        $this->assertLessThan(
            strpos($this->method($source, '    public function setinline()'), '$this->changeinline('),
            strpos($this->method($source, '    public function setinline()'), '$this->checkpermissions(')
        );
        $remove = $this->method($source, '    public function removeinlineonattachment()');
        $this->assertLessThan(strpos($remove, '->setInline(0)'), strpos($remove, '$this->checkpermissions('));
    }
}
