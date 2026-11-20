<?php

/**
 * Kunena Component
 *
 * @package  Kunena.Tests
 * @license  https://www.gnu.org/copyleft/gpl.html GNU/GPL
 **/

namespace Kunena\Tests\Unit\User;

use Joomla\CMS\Factory;
use Kunena\Forum\Libraries\Exception\KunenaException;
use Kunena\Forum\Libraries\Exception\KunenaExceptionAuthorise;
use Kunena\Forum\Libraries\Tables\KunenaUserBans;
use Kunena\Forum\Libraries\User\KunenaBan;
use Kunena\Forum\Libraries\User\KunenaUser;
use Kunena\Forum\Libraries\User\KunenaUserHelper;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\TestCase;

#[RunTestsInSeparateProcesses]
#[PreserveGlobalState(false)]
class IpBanTest extends TestCase
{
    protected function setUp(): void
    {
        \defined('_JEXEC') || \define('_JEXEC', 1);
        require_once __DIR__ . '/fixtures/ip_ban_stubs.php';
        require_once __DIR__ . '/../../../src/libraries/kunena/src/User/KunenaIpAddress.php';
        require_once __DIR__ . '/../../../src/libraries/kunena/src/Tables/KunenaUserBans.php';
        require_once __DIR__ . '/../../../src/libraries/kunena/src/User/KunenaBan.php';
        require_once __DIR__ . '/../../../src/libraries/kunena/src/Exception/KunenaExceptionAuthorise.php';
        require_once __DIR__ . '/../../../src/libraries/kunena/src/Forum/Category/KunenaCategory.php';
        require_once __DIR__ . '/fixtures/ip_ban_test_helpers.php';

        Factory::$database = new IpBanDatabase();
        Factory::$identity = (object) ['id' => 42];
        KunenaUserHelper::$user = new KunenaUser();
        KunenaUserHelper::$user->userid = 42;
        KunenaUserHelper::$user->moderator = true;
        KunenaUserHelper::$user->exists = true;
        KunenaUserHelper::$users = [];
        KunenaUserHelper::$ip = '';
    }

    public function testModeratorCanSaveAndLoadAnIpOnlyBan(): void
    {
        $ban = new KunenaBan();
        $ban->ban(null, '2001:0db8:0:0:0:0:0:1');

        self::assertSame(0, $ban->userid);
        self::assertTrue($ban->save());
        self::assertSame('2001:db8::1', Factory::$database->rows[1]['ip']);

        $loaded = new KunenaBan();
        self::assertTrue($loaded->loadByIP('2001:db8::1'));
        self::assertSame(1, $loaded->id);
        self::assertTrue($loaded->isLifetime());
    }

    public function testOnlyModeratorsCanSaveIpBansAndUserProtectionsRemain(): void
    {
        $ban = new KunenaBan();
        $ban->ban(null, '203.0.113.8');
        KunenaUserHelper::$user->moderator = false;

        try {
            $ban->canBan();
            self::fail('A non-moderator could ban an IP.');
        } catch (KunenaException $e) {
            self::assertSame('COM_KUNENA_MODERATION_ERROR_NOT_MODERATOR', $e->getMessage());
        }

        KunenaUserHelper::$user->moderator = true;
        $target = new KunenaUser();
        $target->userid = 7;
        $target->exists = true;
        $target->admin = true;
        KunenaUserHelper::$users[7] = $target;
        $ban->ban(7, '203.0.113.8');

        $this->expectException(KunenaException::class);
        $this->expectExceptionMessage('COM_KUNENA_LIB_USER_BAN_ERROR_ADMIN');
        $ban->canBan();
    }

    public function testActiveAndExpiredBansAreDistinguished(): void
    {
        $ban = new KunenaBan();
        $ban->ban(null, '203.0.113.8', 0, 'tomorrow');
        $ban->save();
        Factory::$database->rows[2] = Factory::$database->rows[1];
        Factory::$database->rows[2]['id'] = 2;
        Factory::$database->rows[2]['expiration'] = '2000-01-01 00:00:00';

        $table = new KunenaUserBans(Factory::$database);
        self::assertTrue($table->loadByIP('203.0.113.8'));
        self::assertSame(1, $table->id);
        self::assertTrue($table->loadByIP('203.0.113.8', KunenaUserBans::ANY));
        self::assertSame(2, $table->id);
        self::assertFalse($table->loadByIP('203.0.113.9'));
        self::assertFalse($table->loadByIP(''));
    }

    public function testIpFactoryLoadsAnExistingBan(): void
    {
        Factory::$database->rows[1] = [
            'id' => 1,
            'userid' => 0,
            'ip' => '203.0.113.8',
            'expiration' => '9999-12-31 23:59:59',
        ];

        self::assertSame(1, KunenaBan::getInstanceByIP('203.0.113.8')->id);
        self::assertNull(KunenaBan::getInstanceByIP('203.0.113.9'));
    }

    public function testPostingIsDeniedOnlyForTheCurrentAddressWhileTheBanIsActive(): void
    {
        $ban = new KunenaBan();
        $ban->ban(null, '203.0.113.8', 0, 'tomorrow');
        $ban->save();
        KunenaUserHelper::$ip = '203.0.113.8';

        foreach (['topic.create', 'topic.reply', 'topic.post.reply'] as $action) {
            $error = (new IpBanCategory())->tryAuthorise($action, null, false);
            self::assertInstanceOf(KunenaExceptionAuthorise::class, $error, $action);
            self::assertSame(403, $error->getCode());
        }

        $other = new KunenaUser();
        $other->userid = 99;
        $other->moderator = true;
        self::assertNull((new IpBanCategory())->tryAuthorise('topic.create', $other, false));

        KunenaUserHelper::$ip = '203.0.113.9';
        self::assertNull((new IpBanCategory())->tryAuthorise('topic.create', null, false));
        KunenaUserHelper::$ip = '';
        self::assertNull((new IpBanCategory())->tryAuthorise('topic.create', null, false));

        KunenaUserHelper::$ip = '203.0.113.8';
        Factory::$database->rows[1]['expiration'] = '2000-01-01 00:00:00';
        self::assertNull((new IpBanCategory())->tryAuthorise('topic.create', null, false));
    }

    public function testGuestAndLifetimeBanAreEnforced(): void
    {
        $ban = new KunenaBan();
        $ban->ban(null, '203.0.113.8');
        $ban->save();
        KunenaUserHelper::$user->userid = 0;
        KunenaUserHelper::$ip = '203.0.113.8';

        $error = (new IpBanCategory())->tryAuthorise('topic.post.reply', null, false);
        self::assertInstanceOf(KunenaExceptionAuthorise::class, $error);
        self::assertSame('COM_KUNENA_POST_ERROR_USER_BANNED_NOACCESS', $error->getMessage());
    }

    public function testUnbanningAllowsPostingAgain(): void
    {
        $ban = new KunenaBan();
        $ban->ban(null, '203.0.113.8');
        $ban->save();
        KunenaUserHelper::$ip = '203.0.113.8';
        self::assertInstanceOf(KunenaExceptionAuthorise::class, (new IpBanCategory())->tryAuthorise('topic.create', null, false));

        $ban->unBan();
        $ban->save();
        self::assertNull((new IpBanCategory())->tryAuthorise('topic.create', null, false));
    }
}
