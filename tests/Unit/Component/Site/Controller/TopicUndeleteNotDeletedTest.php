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
 * The restore of a message or a topic restores only what is deleted: a message which waits for approval (an old link, a stale page)
 * was published without the approval, its notifications and the mails of its mentions, as the approval refuses a message which does
 * not wait for it. (The task needs a whole site: its code is read.)
 */
class TopicUndeleteNotDeletedTest extends UnitTestCase
{
    public function testOnlyWhatIsDeletedIsRestored(): void
    {
        $source = str_replace("\r\n", "\n", Files::read('src/site/src/Controllers/TopicController.php'));
        $start  = strpos($source, '    public function undelete()');
        $body   = substr($source, $start, (int) strpos($source, "\n    }\n", $start) - $start);
        $check  = strpos($body, 'if ((int) $target->hold < KunenaForum::DELETED) {');

        $this->assertNotFalse($check);
        $this->assertGreaterThan(strpos($body, "\$target->tryAuthorise('undelete');"), $check);
        $this->assertLessThan(strpos($body, '$target->publish(KunenaForum::PUBLISHED);'), $check);
        $this->assertStringContainsString("Text::_('COM_KUNENA_MODERATE_UNDELETE_NOT_DELETED')", substr($body, $check, 200));
        $this->assertStringContainsString("\nCOM_KUNENA_MODERATE_UNDELETE_NOT_DELETED = \"", Files::read('src/site/language/en-GB/com_kunena.ini'));
    }
}
