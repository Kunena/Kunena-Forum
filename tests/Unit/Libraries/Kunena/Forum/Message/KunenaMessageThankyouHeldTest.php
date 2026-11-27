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

use Kunena\Forum\Libraries\Forum\Message\KunenaMessage;
use Kunena\Forum\Libraries\User\KunenaUser;
use Kunena\Tests\Unit\UnitTestCase;
use ReflectionMethod;
use ReflectionProperty;

/**
 * A thank you is for a published message, as a reaction (KunenaMessageReactions::mayReact()): a moderator, who reads the messages
 * which wait for approval or are in the trash, could thank them (the button and the task ask the same right), and the counter of
 * the author counted a message which nobody else saw.
 */
class KunenaMessageThankyouHeldTest extends UnitTestCase
{
    public function testAHeldMessageIsNotThanked(): void
    {
        $this->requireJoomla();

        $actions = (new ReflectionProperty(KunenaMessage::class, 'actions'))->getValue();

        $this->assertContains('NotHold', $actions['thankyou']);

        // The rule refuses a message which waits or is in the trash, and lets a published one pass.
        $rule = new ReflectionMethod(KunenaMessage::class, 'authoriseNotHold');

        foreach ([1 => true, 2 => true, 3 => true, 0 => false] as $hold => $refused) {
            $message = self::bare(KunenaMessage::class, ['hold' => $hold]);
            $this->assertSame($refused, $rule->invoke($message, self::bare(KunenaUser::class, [])) !== null, "hold $hold");
        }
    }
}
