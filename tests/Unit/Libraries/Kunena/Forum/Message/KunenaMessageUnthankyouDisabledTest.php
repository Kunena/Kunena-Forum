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

use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\Exception\KunenaExceptionAuthorise;
use Kunena\Forum\Libraries\Forum\Message\KunenaMessage;
use Kunena\Forum\Libraries\User\KunenaUser;
use Kunena\Tests\Unit\UnitTestCase;
use ReflectionProperty;

/**
 * With the thank yous turned off, no thank you is removed either: the right of the removal was the read of the message only (only
 * the button followed the option), so a request removed one's own thank yous, and a moderator those of anybody.
 */
class KunenaMessageUnthankyouDisabledTest extends UnitTestCase
{
    private function message(int $showThankYou): KunenaMessage
    {
        return self::bare(KunenaMessage::class, ['_config' => self::bare(KunenaConfig::class, ['config' => ['showThankYou' => $showThankYou]])]);
    }

    public function testTheRemovalFollowsTheOption(): void
    {
        $this->requireJoomla();

        $actions = (new ReflectionProperty(KunenaMessage::class, 'actions'))->getValue();
        $user    = self::bare(KunenaUser::class, ['userid' => 5, '_exists' => true]);

        $this->assertContains('ThankyouEnabled', $actions['unthankyou']);
        $this->assertInstanceOf(KunenaExceptionAuthorise::class, self::invoke($this->message(0), 'authoriseThankyouEnabled', $user));
        $this->assertNull(self::invoke($this->message(1), 'authoriseThankyouEnabled', $user));
    }
}
