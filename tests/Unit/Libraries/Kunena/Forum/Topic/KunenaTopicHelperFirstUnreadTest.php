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

namespace Kunena\Tests\Unit\Libraries\Kunena\Forum\Topic;

use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The link "first unread" of a topic goes to the unread message which is the earliest in time. It went to the lowest id: a scheduled
 * reply keeps the id it got when it was written and is published later (its time), so a reply written at 9:00 for 12:00 sent the link
 * past a reply of 11:00 which was unread too. (The query needs the session and the categories of a user: its code is read; it was run
 * on the test database.)
 */
class KunenaTopicHelperFirstUnreadTest extends UnitTestCase
{
    public function testTheFirstUnreadMessageIsTheEarliestInTime(): void
    {
        $source = Files::read('src/libraries/kunena/src/Forum/Topic/KunenaTopicHelper.php');

        $this->assertStringContainsString(
            "', CAST(SUBSTRING_INDEX(GROUP_CONCAT(' . \$db->quoteName('m.id') . ' ORDER BY ' . \$db->quoteName('m.time') . ', ' . \$db->quoteName('m.id') . \"), ',', 1) AS UNSIGNED) AS \" . \$db->quoteName('lastread')",
            $source
        );
        $this->assertStringNotContainsString("', MIN(' . \$db->quoteName('m.id') . ') AS ' . \$db->quoteName('lastread')", $source);
    }
}
