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
 * A moderator of the category has a link next to each user who thanked a message (task unthankyou, userid=<user>): the task
 * ignored the user and took back the thank you of the moderator himself ("you did not thank this message" when he had not), and
 * the thank you of the user stayed. (The task needs a whole site: its code is read.)
 */
class TopicUnthankyouOtherUserTest extends UnitTestCase
{
    public function testTheThankYouOfTheNamedUserIsTakenBackByAModerator(): void
    {
        $source = str_replace("\r\n", "\n", Files::read('src/site/src/Controllers/TopicController.php'));

        $this->assertSame(1, preg_match('/protected function setThankyou\(\$type\)\s*\{(.*?)\n    \}\n/s', $source, $m));

        $this->assertStringContainsString("\$other = \$this->app->getInput()->getInt('userid', 0);", $m[1]);
        $this->assertStringContainsString('if (!$this->me->isModerator($message->getCategory())) {', $m[1]);
        $this->assertStringContainsString('$whose = KunenaUserHelper::get($other);', $m[1]);
        $this->assertStringContainsString('$thankyou->delete($whose);', $m[1]);
        $this->assertStringNotContainsString('$thankyou->delete($this->me);', $m[1]);

        // The link which sends the user.
        $this->assertStringContainsString(
            'unthankyou&userid={$userid}',
            Files::read('src/site/src/Controller/Topic/Item/Message/TopicItemMessageDisplay.php')
        );
    }
}
