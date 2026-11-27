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

namespace Kunena\Tests\Unit\Component\Administrator\Tmpl;

use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The reason and the text of a report in the tool "Reports overview" were joined and trimmed with trim($text, ' —'): trim() takes
 * away bytes, so a text which ends with "р" (as "мусор") or begins with "“" lost a byte of its letter, and the escape of the
 * broken text was empty: the report showed nothing. (The page needs the backend: its code is read.)
 */
class ToolsReportsReasonTest extends UnitTestCase
{
    public function testTheReasonAndTheTextOfAReportAreShown(): void
    {
        // What the old line made of a Russian report.
        $this->assertSame('', htmlspecialchars(trim('Spam' . ' — ' . 'мусор', ' —'), ENT_QUOTES, 'UTF-8'));

        $source = Files::read('src/admin/tmpl/tools/reports.php');

        $this->assertStringNotContainsString("' —')", $source);
        $this->assertStringContainsString("implode(' — ', array_filter([\$item['reason'], \$item['text']], 'strlen'))", $source);

        // The parts as the page joins them now.
        $this->assertSame('Spam — мусор', implode(' — ', array_filter(['Spam', 'мусор'], 'strlen')));
        $this->assertSame('“мусор', implode(' — ', array_filter(['', '“мусор'], 'strlen')));
    }
}
