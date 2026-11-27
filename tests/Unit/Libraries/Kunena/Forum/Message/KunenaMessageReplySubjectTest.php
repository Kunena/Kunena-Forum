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

namespace Kunena\Tests\Unit\Libraries\Kunena\Forum\Message;

use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * A reply renames its topic only with a subject which the poster changed, and only for a moderator of the category (or the author
 * of the topic where the subject may be changed). The forms send the subject of the message replied to, so the quick reply of a
 * moderator to an older post renamed the topic back to the subject of that post; and a moderator of any category was enough.
 * (newReply() needs the user, the configuration and the quotes of a site: its code is read.)
 */
class KunenaMessageReplySubjectTest extends UnitTestCase
{
    public function testOnlyAChangedSubjectOfAModeratorOfTheCategoryRenamesTheTopic(): void
    {
        $source = str_replace("\r\n", "\n", Files::read('src/libraries/kunena/src/Forum/Message/KunenaMessage.php'));

        $this->assertSame(1, preg_match('/public function newReply\(.*?\n    \}\n/s', $source, $m));
        $this->assertSame(1, preg_match('/\n\s*if \((.*?)\) \{\n\s*if \((.*?)\) \{\n\s*\$topic->subject = \$fields\[\'subject\'\];/', $m[0], $condition));
        $this->assertStringContainsString('KunenaUserHelper::getMyself()->isModerator($category)', $condition[1]);
        $this->assertSame("isset(\$fields['subject']) && (string) \$fields['subject'] !== (string) \$this->subject", $condition[2]);
    }
}
