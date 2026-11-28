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
 * The Delete of a message which waits for approval is shown only with the right to delete it, as for the other messages: its author
 * saw Delete (with the full actions of the template) which the task refused, under "delete only without replies" or in a locked
 * topic. (The buttons need a whole site: their code is read.)
 */
class MessageActionsHeldDeleteTest extends UnitTestCase
{
    public function testTheDeleteOfAHeldMessageNeedsTheRight(): void
    {
        $source = str_replace("\r\n", "\n", Files::read('src/site/src/Controller/Message/Item/Actions/MessageItemActionsDisplay.php'));
        $held   = strpos($source, "if (\$this->message->hold == 1 && (int) \$this->message->getTopic()->hold < 2) {");
        $branch = substr($source, $held, (int) strpos($source, '} elseif ($this->message->hold == 2 || $this->message->hold == 3) {', $held) - $held);
        $right  = strpos($branch, "if (\$this->message->isAuthorised('delete')) {");

        $this->assertNotFalse($right);
        $this->assertLessThan(strpos($branch, "'delete',\n"), $right);
        $this->assertLessThan(strpos($branch, "'delete_dropdown',"), $right);
    }
}
