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
 * The moderation form with "Enter topic ID" and no id (or not a number) changes nothing: the target became the category, so the
 * message was moved into a new topic (a split which nobody asked for) and the topic into the category, with "saved". (The task needs
 * a whole site: its code is read.)
 */
class TopicMoveEmptyTargetIdTest extends UnitTestCase
{
    public function testAMoveWithoutTheIdOfTheTargetTopicChangesNothing(): void
    {
        $source = str_replace("\r\n", "\n", Files::read('src/site/src/Controllers/TopicController.php'));
        $start  = strpos($source, '    public function move()');
        $body   = substr($source, $start, (int) strpos($source, "\n    }\n", $start) - $start);

        $read   = strpos($body, "\$targetTopic = \$this->app->getInput()->getInt('targetid', 0);");
        $check  = strpos($body, 'if ($targetTopic <= 0) {', (int) $read);
        $target = strpos($body, '$target = KunenaTopicHelper::get($targetTopic);');

        $this->assertNotFalse($read);
        $this->assertNotFalse($check);
        $this->assertLessThan($target, $check);
        $this->assertStringContainsString("Text::_('COM_KUNENA_MODERATION_NO_TARGET_ID')", substr($body, $check, 300));
        $this->assertStringContainsString("\nCOM_KUNENA_MODERATION_NO_TARGET_ID = \"", Files::read('src/site/language/en-GB/com_kunena.ini'));
    }
}
