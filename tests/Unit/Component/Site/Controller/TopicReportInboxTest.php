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
 * A report which the inbox gave to moderators who do not want the e-mail reached them: the reporter was told "The report can't be
 * sent" (in a box of success) and the report was not logged when none of them wanted the e-mail, or when no e-mail could be sent.
 * (The task needs a whole site: its code is read.)
 */
class TopicReportInboxTest extends UnitTestCase
{
    private static function report(): string
    {
        $source = str_replace("\r\n", "\n", Files::read('src/site/src/Controllers/TopicController.php'));
        $start  = strpos($source, '    public function report()');

        return substr($source, $start, (int) strpos($source, "\n    }\n", $start) - $start);
    }

    public function testAReportInTheInboxIsSentAndLogged(): void
    {
        $body = self::report();

        $this->assertStringContainsString('$inboxed = $noMail !== [];', $body);
        $this->assertStringContainsString('if (!$handled && !$canMail && !$inboxed) {', $body);
        $this->assertStringContainsString('if ($handled || $mailSent || $inboxed) {', $body);
    }

    public function testAReportWhichIsNotSentIsAnError(): void
    {
        $this->assertStringContainsString("Text::_('COM_KUNENA_REPORT_NOT_SEND'), 'error');", self::report());
        $this->assertStringNotContainsString("Text::_('COM_KUNENA_REPORT_NOT_SEND'), 'success');", self::report());
    }
}
