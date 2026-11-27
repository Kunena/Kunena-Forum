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
 * The permanent delete of the trash deletes only topics and messages which are in the trash: the ids come from the request, and a
 * request with the id of a live topic or message deleted it for good. (The delete needs a whole forum: its code is read.)
 */
class TrashsPurgeOnlyTrashTest extends UnitTestCase
{
    public function testOnlyTheItemsOfTheTrashAreDeleted(): void
    {
        $source = str_replace("\r\n", "\n", Files::read('src/admin/src/Model/TrashsModel.php'));

        foreach (['purgeTopics' => '$topic', 'purgeMessages' => '$message'] as $method => $item) {
            $this->assertSame(1, preg_match('/public function ' . $method . '\(.*?\n    \}\n/s', $source, $m), $method);

            $check  = strpos($m[0], "if ((int) {$item}->hold < KunenaForum::DELETED) {");
            $delete = strpos($m[0], "{$item}->delete();");

            $this->assertNotFalse($check, $method);
            $this->assertLessThan($delete, $check, $method);
        }
    }
}
