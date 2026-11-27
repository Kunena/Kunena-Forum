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
 * The removal of the files of a message on save takes only the files of this message off the count of the attachments of the
 * topic: an own upload which waited for its message (mesid 0, never counted) lowered the count too. (The save needs a whole
 * message with its topic: its code is read.)
 */
class KunenaMessageRemovePendingCountTest extends UnitTestCase
{
    public function testOnlyAFileOfThisMessageIsTakenOffTheCount(): void
    {
        $source = str_replace("\r\n", "\n", Files::read('src/libraries/kunena/src/Forum/Message/KunenaMessage.php'));

        $this->assertSame(1, preg_match('/foreach \(\$this->_attachments_del as \$attachment\) \{(.*?)\n        \}\n/s', $source, $m));
        $this->assertStringContainsString('$counted = (int) $attachment->mesid === (int) $this->id && $this->id;', $m[1]);
        $this->assertMatchesRegularExpression('/if \(\$counted\) \{\n\s*\$this->getTopic\(\)->attachments--;/', $m[1]);
    }
}
