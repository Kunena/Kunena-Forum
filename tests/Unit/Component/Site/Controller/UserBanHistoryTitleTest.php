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
 * The title and the description of the page "ban history of a user" have the name of the user as text: Joomla escapes them. They
 * were made from the text of the page, which has the name escaped for HTML, so "Tom & Jerry" was "Tom &amp; Jerry" in the title of
 * the browser. (The page needs the menu and the document of a site: its code is read.)
 */
class UserBanHistoryTitleTest extends UnitTestCase
{
    public function testTheTitleHasTheNameAsText(): void
    {
        $source = Files::read('src/site/src/Controller/User/Ban/History/UserBanHistoryDisplay.php');

        $this->assertSame(1, preg_match('/protected function prepareDocument\(\)\s*\{(.*?)\n    \}\n/s', str_replace("\r\n", "\n", $source), $m));

        $this->assertStringContainsString("\$headerText = Text::sprintf('COM_KUNENA_BAN_BANHISTORYFOR', \$this->profile->getName('', false));", $m[1]);
        $this->assertStringContainsString('$this->setTitle($headerText);', $m[1]);
        $this->assertStringContainsString('$this->setDescription($headerText);', $m[1]);
        $this->assertStringNotContainsString('$this->headerText', $m[1], 'The text of the page (with the escaped name) is not the title.');
    }
}
