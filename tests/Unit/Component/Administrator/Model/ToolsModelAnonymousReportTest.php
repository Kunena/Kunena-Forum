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

namespace Kunena\Tests\Unit\Component\Administrator\Model;

use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The anonymous system report (made to be posted in public) has nothing which names the site, as it leaves out the address of the
 * mails: it had the address of the site, and the user (an e-mail address, often) and the host of the mail server. The full report
 * keeps them. (The report needs a whole site: its code is read.)
 */
class ToolsModelAnonymousReportTest extends UnitTestCase
{
    private static function report(string $signature): string
    {
        $source = str_replace("\r\n", "\n", Files::read('src/admin/src/Model/ToolsModel.php'));
        $start  = strpos($source, $signature);

        return $start === false ? '' : substr($source, $start, (int) strpos($source, "\n    }\n", $start) - $start);
    }

    public function testTheAnonymousReportDoesNotNameTheSite(): void
    {
        $anonymous = self::report('    public function getSystemReportAnonymous(): string');

        $this->assertNotSame('', $anonymous);

        foreach (['Uri::root()', '$this->jconfigSmtpUser', "get('smtphost')", "get('mailfrom')"] as $value) {
            $this->assertStringNotContainsString($value, $anonymous, $value);
        }
    }

    public function testTheFullReportKeepsThem(): void
    {
        $full = Files::read('src/admin/src/Model/ToolsModel.php');

        $this->assertStringContainsString('[b]Base URL:[/b]\' . Uri::root()', $full);
        $this->assertStringContainsString('[b]SMTP User:[/b] \' . $this->jconfigSmtpUser', $full);
    }
}
