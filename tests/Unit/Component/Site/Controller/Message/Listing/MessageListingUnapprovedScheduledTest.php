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

namespace Kunena\Tests\Unit\Component\Site\Controller\Message\Listing;

use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The list of the posts which wait for approval leaves out the replies which wait for their date (scheduled, not held for review), as
 * the moderation queue of the backend does: they are for no moderator yet, and a bulk approval of the list published them at once,
 * before the date which their author chose. (The list needs a whole site: its code is read.)
 */
class MessageListingUnapprovedScheduledTest extends UnitTestCase
{
    public function testTheScheduledRepliesAreNotInTheUnapprovedList(): void
    {
        $display = str_replace("\r\n", "\n", Files::read('src/site/src/Controller/Message/Listing/Recent/MessageListingRecentDisplay.php'));

        $this->assertMatchesRegularExpression("/->filterByHold\(\[1\]\)\n\s*->filterNotScheduled\(\);/", $display);

        $finder = Files::read('src/libraries/kunena/src/Forum/Message/KunenaMessageFinder.php');

        $this->assertStringContainsString("\$this->query->where('(' . \$this->db->quoteName('a.scheduled_time') . ' = 0 OR ' . \$this->db->quoteName('a.scheduled_review') . ' <> 0)');", $finder);
    }
}
