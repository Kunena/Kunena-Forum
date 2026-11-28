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
 * A restore of the trash counts again what it restored: its topics, their categories and their authors, as the purge of the trash
 * does. Every restore, also of one message, counted all the users, topics and categories of the forum again, which takes long on a
 * big forum. (The task needs a whole forum: its code is read.)
 */
class TrashsRestoreRecountTest extends UnitTestCase
{
    public function testOnlyWhatWasRestoredIsCountedAgain(): void
    {
        $source = str_replace("\r\n", "\n", Files::read('src/admin/src/Controller/TrashsController.php'));

        $this->assertStringNotContainsString("KunenaTopicHelper::recount();\n", $source);
        $this->assertStringNotContainsString("KunenaCategoryHelper::recountBatch();\n", $source);
        $this->assertStringContainsString('KunenaTopicHelper::recount(array_values($topicIds));', $source);
        $this->assertStringContainsString('KunenaCategoryHelper::recountBatch(array_values($categoryIds));', $source);
        $this->assertStringContainsString('KunenaUserHelper::recount(array_values(array_unique(array_merge(array_values($userIds), $authors))));', $source);
    }
}
