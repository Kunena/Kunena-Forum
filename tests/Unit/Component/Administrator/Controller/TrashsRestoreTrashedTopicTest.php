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

namespace Kunena\Tests\Unit\Component\Administrator\Controller;

use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * A reply of the trash whose topic is in the trash too is not restored alone: it published the topic with that reply as its only post
 * (the other messages stayed deleted in a published topic, which was no more in the trash). The topic is restored first, as the
 * frontend asks; the first message still restores its topic. (The task needs a whole forum: its code is read.)
 */
class TrashsRestoreTrashedTopicTest extends UnitTestCase
{
    public function testAMessageOfATrashedTopicIsNotRestoredAlone(): void
    {
        $source = str_replace("\r\n", "\n", Files::read('src/admin/src/Controller/TrashsController.php'));

        // A reply, not the first message: that one restores its topic (trash-topics.cy.js).
        $check   = strpos($source, 'if ((int) $topic->hold >= KunenaForum::DELETED && (int) $topic->first_post_id !== (int) $target->id) {');
        $publish = strpos($source, '$target->publish();');

        $this->assertNotFalse($check);
        $this->assertLessThan($publish, $check);
        $this->assertStringContainsString("Text::sprintf('COM_KUNENA_TRASH_RESTORE_TOPIC_FIRST', (int) \$target->id)", substr($source, $check, 300));
        $this->assertStringContainsString('COM_KUNENA_TRASH_RESTORE_TOPIC_FIRST = ', Files::read('src/admin/language/en-GB/com_kunena.controllers.ini'));
    }
}
