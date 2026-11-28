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
 * An edit without a reason has no reason: when the form has no field of the reason (editMarkup off), the reason of the last edit
 * ("insults removed" of a moderator) was taken, and saved again with the next edit of the author, also in its history. (The task
 * needs a whole site: its code is read.)
 */
class TopicEditReasonTest extends UnitTestCase
{
    public function testAnEditWithoutAReasonHasNone(): void
    {
        $source = Files::read('src/site/src/Controllers/TopicController.php');

        $this->assertStringContainsString("'modified_reason'   => \$this->app->getInput()->getString('modified_reason', ''),", $source);
        $this->assertStringNotContainsString("getString('modified_reason', \$message->modified_reason)", $source);
    }
}
