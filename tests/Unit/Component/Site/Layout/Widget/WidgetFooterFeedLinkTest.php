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

namespace Kunena\Tests\Unit\Component\Site\Layout\Widget;

use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The feed link of the footer (the icon and the link in the head of the page) goes to the layout of the feed also when Joomla adds
 * a suffix to the addresses: that branch used layout=default, which has no feed (a 404 on the test site, /forum/recent/mode-topics
 * ?format=feed, where /forum/recent/feed/mode-topics?format=feed answers). (The widget needs a whole site: its code is read.)
 */
class WidgetFooterFeedLinkTest extends UnitTestCase
{
    public function testBothLinksGoToTheLayoutOfTheFeed(): void
    {
        $source = Files::read('src/site/src/Layout/Widget/WidgetFooter.php');

        $this->assertSame(2, preg_match_all("/KunenaRoute::_\\([\"']index\\.php\\?option=com_kunena&view=topics&[^\\n]*/", $source, $m));

        foreach ($m[0] as $link) {
            $this->assertStringContainsString('layout=feed', $link);
            $this->assertStringNotContainsString('layout=default', $link);
        }
    }
}
