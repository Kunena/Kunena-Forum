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
 * The thank you and its removal log the category of the message, not the one of the request: a request with the catid of another
 * category wrote the log there, and the moderators of that category saw a moderation which was none, with the subject of a topic which
 * they may not read. The removal logs the user whose thank you went (a moderator removes the one of another user), not the author of
 * the post. (The tasks need a whole site: their code is read.)
 */
class TopicThankyouCategoryTest extends UnitTestCase
{
    public function testTheCategoryOfTheMessageAndTheUserOfTheThanksAreLogged(): void
    {
        $source = str_replace("\r\n", "\n", Files::read('src/site/src/Controllers/TopicController.php'));

        $start = strpos($source, '    protected function setThankyou(');
        $this->assertNotFalse($start);
        $body = substr($source, $start, (int) strpos($source, "\n    }\n", $start) - $start);

        $this->assertStringContainsString('$category            = $message->getCategory();', $body);
        $this->assertStringNotContainsString('KunenaCategoryHelper::get($this->catid)', $body);
        $this->assertStringContainsString("['mesid' => \$message->id, 'userid' => (int) \$whose->userid],", $body);
    }
}
