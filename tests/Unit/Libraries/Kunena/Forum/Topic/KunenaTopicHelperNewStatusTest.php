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
 * The number of the new messages of a topic and the link to its first unread one count the messages after the time the user
 * marked the category read ("Mark Topics Read"). That time only decided whether the topic was looked at: a reply of five days
 * ago which he never opened stayed in the count ("2 new" for 1) and the link went to it. (The function needs the session of a
 * site: its query is read.)
 */
class KunenaTopicHelperNewStatusTest extends UnitTestCase
{
    public function testTheMessagesBeforeTheCategoryWasMarkedReadAreNotNew(): void
    {
        $source = str_replace("\r\n", "\n", Files::read('src/libraries/kunena/src/Forum/Topic/KunenaTopicHelper.php'));

        $this->assertSame(1, preg_match('/public static function fetchNewStatus\(.*?\n    \}\n/s', $source, $m));

        $this->assertStringContainsString("\$db->quoteName('#__kunena_user_categories', 'uc')", $m[0]);
        $this->assertStringContainsString("\$db->quoteName('uc.allreadtime') . ' IS NULL OR ' . \$db->quoteName('m.time') . ' > ' . \$db->quoteName('uc.allreadtime')", $m[0]);
        // The read rows of the user, named with their table now that two tables have a user.
        $this->assertStringContainsString("\$db->quoteName('ur.user_id') . ' = '", $m[0]);
    }
}
