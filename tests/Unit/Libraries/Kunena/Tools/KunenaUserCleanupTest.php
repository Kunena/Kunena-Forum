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

namespace Kunena\Tests\Unit\Libraries\Kunena\Tools;

use Kunena\Forum\Libraries\Tools\KunenaUserCleanup as C;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The tool "Clean up a user" reads its options from the request: only known values, and nothing changes by default.
 */
class KunenaUserCleanupTest extends UnitTestCase
{
    public function testTheDefaultsKeepEverything(): void
    {
        $options = C::options([]);

        $this->assertSame(['messages' => C::MESSAGES_KEEP, 'attachments' => false, 'profile' => false, 'account' => C::ACCOUNT_KEEP], $options);
        $this->assertFalse(C::hasAction($options));
    }

    public function testAnUnknownValueKeeps(): void
    {
        $options = C::options(['messages' => 'purge', 'account' => 'delete']);

        $this->assertSame(C::MESSAGES_KEEP, $options['messages']);
        $this->assertSame(C::ACCOUNT_KEEP, $options['account']);
    }

    public function testTheChosenActions(): void
    {
        $options = C::options(['messages' => 'delete', 'attachments' => '1', 'profile' => '1', 'account' => 'block']);

        $this->assertSame(['messages' => C::MESSAGES_DELETE, 'attachments' => true, 'profile' => true, 'account' => C::ACCOUNT_BLOCK], $options);
        $this->assertTrue(C::hasAction($options));
        $this->assertTrue(C::hasAction(C::options(['account' => 'ban'])));
        $this->assertTrue(C::hasAction(C::options(['messages' => 'trash'])));
    }

    public function testNoUserHasNoSummary(): void
    {
        $this->assertNull(C::summary(0));
        $this->assertNull(C::summary(-3));
    }
}
