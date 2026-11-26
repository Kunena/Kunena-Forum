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

namespace Kunena\Tests\Unit\Libraries\Kunena\Integration;

use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\Integration\KunenaProfile;
use Kunena\Forum\Libraries\User\KunenaUserFollow;
use Kunena\Tests\Unit\UnitTestCase;
use ReflectionProperty;

/**
 * The friends of a user, for the files which he gives to his friends only: in Kunena, the users whom he follows (he chose them).
 * Following somebody does not make him a friend of the other way round, and nobody is a friend when following is turned off.
 */
class KunenaProfileFriendTest extends UnitTestCase
{
    private array $saved = [];

    protected function setUp(): void
    {
        $this->requireJoomla();

        $this->saved = [
            'config' => (new ReflectionProperty(KunenaConfig::class, 'instance'))->getValue(),
            'lists'  => (new ReflectionProperty(KunenaUserFollow::class, 'lists'))->getValue(),
        ];

        // The user 5 follows the user 7 (the list as the helper keeps it for the request: no database).
        (new ReflectionProperty(KunenaUserFollow::class, 'lists'))->setValue(null, [5 => [7], 7 => []]);
    }

    protected function tearDown(): void
    {
        if ($this->saved) {
            (new ReflectionProperty(KunenaConfig::class, 'instance'))->setValue(null, $this->saved['config']);
            (new ReflectionProperty(KunenaUserFollow::class, 'lists'))->setValue(null, $this->saved['lists']);
        }
    }

    private function friend(int $userid, int $friendid, int $enableFollow = 1): bool
    {
        (new ReflectionProperty(KunenaConfig::class, 'instance'))->setValue(null, self::bare(KunenaConfig::class, ['config' => ['enableFollow' => $enableFollow]]));

        return self::bare(KunenaProfile::class)->isFriend($userid, $friendid);
    }

    public function testAUserWhomTheAuthorFollowsIsHisFriend(): void
    {
        $this->assertTrue($this->friend(5, 7));
    }

    public function testFollowingTheAuthorMakesNobodyHisFriend(): void
    {
        $this->assertFalse($this->friend(7, 5));
    }

    public function testNobodyIsAFriendWhenFollowingIsOff(): void
    {
        $this->assertFalse($this->friend(5, 7, 0));
    }

    public function testAGuestIsNobodysFriend(): void
    {
        $this->assertFalse($this->friend(5, 0));
        $this->assertFalse($this->friend(0, 7));
    }
}
