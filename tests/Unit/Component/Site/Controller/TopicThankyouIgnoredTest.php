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
 * The thanks of a user whom the author ignores do not come to the inbox of the author, as the mentions, the private messages and the
 * popups of an ignored user are left out. (The task needs a whole site: its code is read.)
 */
class TopicThankyouIgnoredTest extends UnitTestCase
{
    public function testTheThanksOfAnIgnoredUserAreNoNotification(): void
    {
        $source = str_replace("\r\n", "\n", Files::read('src/site/src/Controllers/TopicController.php'));

        $check  = strpos($source, '&& !KunenaUserIgnore::isIgnoring((int) $message->userid, (int) $this->me->userid)');
        $notify = strpos($source, "KunenaPrivate::getInstance()->notify(\$message, 'thanks',");

        $this->assertNotFalse($check);
        $this->assertNotFalse($notify);
        $this->assertLessThan($notify, $check);
    }
}
