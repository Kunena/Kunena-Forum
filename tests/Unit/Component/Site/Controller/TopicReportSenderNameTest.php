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
 * The name of the sender of the report mail is text (a header of the mail, not HTML): getName() escapes it by default, so a reporter
 * named "Tom & Jerry" was "Board: Tom &amp; Jerry" in the From of the mail. (The report sends mails: its code is read.)
 */
class TopicReportSenderNameTest extends UnitTestCase
{
    public function testTheNameOfTheSenderIsNotEscaped(): void
    {
        $source = Files::read('src/site/src/Controllers/TopicController.php');

        $this->assertStringContainsString("MailHelper::cleanAddress(\$this->config->boardTitle . ': ' . \$this->me->getName('', false))", $source);
        $this->assertStringNotContainsString("MailHelper::cleanAddress(\$this->config->boardTitle . ': ' . \$this->me->getName())", $source);
    }
}
