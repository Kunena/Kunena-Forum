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
 * "Mark as not solved" takes the accepted answer away too, as the removal of the answer takes the icon "solved" away
 * (KunenaTopicAnswer::toggle()): the topic was marked as not solved and still showed the box "Accepted answer". (The task needs a
 * whole site: its code is read.)
 */
class TopicNotSolvedAnswerTest extends UnitTestCase
{
    public function testATopicMarkedAsNotSolvedHasNoAnswer(): void
    {
        $source = str_replace("\r\n", "\n", Files::read('src/site/src/Controllers/TopicController.php'));

        $start = strpos($source, '    public function solved()');
        $this->assertNotFalse($start);
        $body = substr($source, $start, (int) strpos($source, "\n    }\n", $start) - $start);

        $reset = strpos($body, "if (\$solved) {\n            \$topic->answer_id = 0;\n        }");

        $this->assertNotFalse($reset);
        $this->assertLessThan(strpos($body, '$topic->save();'), $reset);

        // The removal of the answer, which takes the icon away.
        $this->assertStringContainsString('$topic->icon_id = 0;', Files::read('src/libraries/kunena/src/Forum/Topic/KunenaTopicAnswer.php'));
    }
}
