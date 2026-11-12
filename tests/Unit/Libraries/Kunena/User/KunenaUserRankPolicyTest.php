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

use Kunena\Forum\Libraries\User\KunenaUserRankPolicy as P;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * Who may give a rank to a user on his profile page: the administrators and the global moderators, but a moderator who is not an
 * administrator does not change his own rank nor the rank of an administrator.
 */
class KunenaUserRankPolicyTest extends UnitTestCase
{
    private function refusal(int $actor = 10, int $target = 20, bool $admin = false, bool $moderator = false, bool $exists = true, bool $targetAdmin = false, int $rank = 5, array $ranks = [0, 5, 6]): ?string
    {
        return P::refusal($actor, $target, $admin, $moderator, $exists, $targetAdmin, $rank, $ranks);
    }

    public function testAdministratorMayChangeTheRankOfAnybody(): void
    {
        $this->assertNull($this->refusal(admin: true, moderator: true), 'A plain user.');
        $this->assertNull($this->refusal(admin: true, moderator: true, targetAdmin: true), 'Another administrator.');
        $this->assertNull($this->refusal(10, 10, admin: true, moderator: true, targetAdmin: true), 'Himself.');
        $this->assertNull($this->refusal(admin: true), 'isModerator() is not needed by an administrator.');
    }

    public function testGlobalModeratorMayChangeTheRankOfAPlainUser(): void
    {
        $this->assertNull($this->refusal(moderator: true));
    }

    public function testGlobalModeratorDoesNotChangeTheRankOfAnAdministrator(): void
    {
        $this->assertSame(P::ADMIN, $this->refusal(moderator: true, targetAdmin: true));
    }

    public function testGlobalModeratorDoesNotChangeHisOwnRank(): void
    {
        $this->assertSame(P::SELF, $this->refusal(10, 10, moderator: true));
    }

    public function testUserWhoIsNeitherIsRefused(): void
    {
        // A category moderator is no global moderator: both flags are false.
        $this->assertSame(P::PERMISSION, $this->refusal());
        $this->assertSame(P::PERMISSION, $this->refusal(10, 10));
        $this->assertSame(P::PERMISSION, $this->refusal(exists: false), 'The permission comes first.');
    }

    public function testTargetWhichDoesNotExistIsRefused(): void
    {
        $this->assertSame(P::USER, $this->refusal(admin: true, exists: false));
        $this->assertSame(P::USER, $this->refusal(target: 0, admin: true));
        $this->assertSame(P::USER, $this->refusal(target: 999999, moderator: true, exists: false));
    }

    public function testRankOutsideTheListIsRefused(): void
    {
        $this->assertSame(P::RANK, $this->refusal(admin: true, rank: 999999));
        $this->assertSame(P::RANK, $this->refusal(moderator: true, rank: 7));
        $this->assertSame(P::RANK, $this->refusal(admin: true, rank: 0, ranks: [5, 6]), 'No rank given needs 0 in the list.');
    }

    public function testNoRankGivenIsAcceptedWhenInTheList(): void
    {
        $this->assertNull($this->refusal(admin: true, rank: 0));
        $this->assertNull($this->refusal(moderator: true, rank: 0, ranks: [0]));
    }
}
