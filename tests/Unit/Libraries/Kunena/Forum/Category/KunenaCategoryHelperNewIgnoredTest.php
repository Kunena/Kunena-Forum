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

namespace Kunena\Tests\Unit\Libraries\Kunena\Forum\Category;

use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The number of new topics of a category (the index of the forum) leaves out the topics of the users whom the user ignores, as the
 * lists of topics do: "1 new" was shown for a topic which no list gave him, until he marked everything read. (The count needs the
 * session and the read marks of a user: its code is read.)
 */
class KunenaCategoryHelperNewIgnoredTest extends UnitTestCase
{
    public function testTheTopicsOfIgnoredUsersAreNotNew(): void
    {
        $source = str_replace("\r\n", "\n", Files::read('src/libraries/kunena/src/Forum/Category/KunenaCategoryHelper.php'));

        $start = strpos($source, '    public static function getNewTopics(');
        $this->assertNotFalse($start);
        $body = substr($source, $start, (int) strpos($source, "\n    }\n", $start) - $start);

        $this->assertStringContainsString('KunenaUserIgnore::getIgnored((int) $user->userid)', $body);
        $this->assertStringContainsString("\$db->quoteName('t.first_post_userid') . ' NOT IN ('", $body);
        $this->assertLessThan(strpos($body, '$db->setQuery($query);'), strpos($body, 'NOT IN ('));
    }
}
