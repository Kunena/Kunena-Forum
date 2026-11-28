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

namespace Kunena\Tests\Unit\Libraries\Kunena\Html;

use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * A message in an e-mail (the context "subscription") has the addresses of the site with their host: the emoticons and the links
 * of the mentions were root-relative ("/forum/..."), broken images and dead links in a mail program. The parser needs a whole site:
 * its code is read; the conversion itself is tested in KunenaParserAbsoluteUrlsTest.
 */
class KunenaParserMailLinksTest extends UnitTestCase
{
    public function testTheMessageOfAMailHasAbsoluteAddresses(): void
    {
        $source = str_replace("\r\n", "\n", Files::read('src/libraries/kunena/src/Html/KunenaParser.php'));

        $start = strpos($source, '    public static function parseBBCode(');
        $this->assertNotFalse($start);
        $body = substr($source, $start, (int) strpos($source, "\n    }\n", $start) - $start);

        $this->assertMatchesRegularExpression(
            "/if \\(\\\$context === 'subscription'\\) \\{\\s*\\\$txt = self::absoluteUrls\\(/",
            $body
        );
        $this->assertLessThan(strpos($body, 'self::absoluteUrls('), strpos($body, '$bbcode->Parse($txt)'));
    }

    public function testTheMessageOfAReportMailHasAbsoluteAddresses(): void
    {
        // The report is not in the context of a subscription (its hidden parts are for the moderators): its message is made
        // absolute where the mail is written, the mail template of the controller and both layouts.
        $controller = str_replace("\r\n", "\n", Files::read('src/site/src/Controllers/TopicController.php'));
        $this->assertStringContainsString("KunenaParser::absoluteUrls((string) \$message->displayField('message'), ", $controller);

        foreach (['aurelia', 'system'] as $template) {
            $layout = Files::read('src/site/template/' . $template . '/layouts/email/report/default.php');

            $this->assertStringContainsString("KunenaParser::absoluteUrls((string) \$this->message->displayField('message'), ", $layout, $template);
            $this->assertStringContainsString('use Kunena\\Forum\\Libraries\\Html\\KunenaParser;', $layout, $template);
        }
    }
}
