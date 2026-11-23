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

namespace Kunena\Tests\Unit\Libraries\Kunena\User;

use Kunena\Forum\Libraries\User\KunenaUser;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * A user without a ban has the null date of the database in the column banned. Only an empty value counted as no ban, so a user
 * who is blocked in Joomla (waiting for his activation, or blocked by an administrator) was banned too: the list of the users in the
 * backend showed him banned, its switch sent "unban" (no ban to lift), and his type was never "blocked".
 */
class KunenaUserIsBannedTest extends UnitTestCase
{
    public function testAUserBlockedInJoomlaWithoutABanIsNotBanned(): void
    {
        $this->requireJoomla();

        $user = self::bare(KunenaUser::class, ['userid' => 42, 'blocked' => 1, 'banned' => '1000-01-01 00:00:00']);

        $this->assertFalse($user->isBanned());
    }
}
