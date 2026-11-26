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

namespace Kunena\Tests\Unit\Libraries\Kunena\Attachment;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Language;
use Kunena\Forum\Libraries\Attachment\KunenaAttachment;
use Kunena\Forum\Libraries\Exception\KunenaExceptionAuthorise;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategory;
use Kunena\Forum\Libraries\Integration\KunenaProfile;
use Kunena\Forum\Libraries\User\KunenaUser;
use Kunena\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use ReflectionProperty;

/**
 * A file for the moderators or for the administrators (PROTECTION_MODERATORS, PROTECTION_ADMINS: "TODO: Add support for
 * PROTECTION_MODERATORS / PROTECTION_ADMINS"): only its author could read it. The moderators, or the administrators, read it now.
 */
class KunenaAttachmentStaffProtectionTest extends UnitTestCase
{
    private ?Language $language = null;

    protected function setUp(): void
    {
        // The refusals have a text of the language of Joomla.
        $this->requireJoomla();

        $language = $this->createStub(Language::class);
        $language->method('_')->willReturnArgument(0);
        $this->language    = Factory::$language;
        Factory::$language = $language;
    }

    protected function tearDown(): void
    {
        Factory::$language = $this->language;
    }

    /**
     * A user who is a moderator, an administrator (who moderates too) or neither.
     */
    private static function user(int $userid, bool $moderator, bool $admin): KunenaUser
    {
        $user = new class () extends KunenaUser {
            public bool $isMod = false;

            public bool $isAdm = false;

            public function __construct()
            {
            }

            public function isModerator(?KunenaCategory $category = null): bool
            {
                return $this->isMod || $this->isAdm;
            }

            public function isAdmin(?KunenaCategory $category = null): bool
            {
                return $this->isAdm;
            }
        };

        $user->userid    = $userid;
        $user->isMod     = $moderator;
        $user->isAdm     = $admin;
        (new ReflectionProperty(KunenaUser::class, '_exists'))->setValue($user, true);

        return $user;
    }

    /**
     * A file of the user 5 without a message (its check is the one of the global moderators), with the given protection.
     */
    private static function attachment(int $protected): KunenaAttachment
    {
        return static::bare(KunenaAttachment::class, ['id' => 12, 'mesid' => 0, 'userid' => 5, 'protected' => $protected, '_exists' => true]);
    }

    public static function readers(): array
    {
        $moderators = KunenaAttachment::PROTECTION_MODERATORS;
        $admins     = KunenaAttachment::PROTECTION_ADMINS;

        return [
            'a moderator, a file of the moderators'      => [$moderators, false, true, false],
            'an administrator, a file of the moderators' => [$moderators, false, false, true],
            'a member, a file of the moderators'         => [$moderators, true, false, false],
            'a moderator, a file of the administrators'  => [$admins, true, true, false],
            'an administrator, a file of the admins'     => [$admins, false, false, true],
            'a member, a file of the administrators'     => [$admins, true, false, false],
        ];
    }

    #[DataProvider('readers')]
    public function testWhoMayReadAFileOfTheStaff(int $protected, bool $refused, bool $moderator, bool $admin): void
    {
        $answer = self::attachment($protected)->tryAuthorise('read', self::user(7, $moderator, $admin), false);

        $this->assertSame($refused, $answer instanceof KunenaExceptionAuthorise);
    }

    public function testTheAuthorReadsHisFileOfTheStaff(): void
    {
        $this->assertNull(self::attachment(KunenaAttachment::PROTECTION_ADMINS)->tryAuthorise('read', self::user(5, false, false), false));
    }

    /**
     * A file for the friends of its author ("TODO: Add support for PROTECTION_FRIENDS"): only the author could read it. The friends
     * of the author (KunenaProfile::isFriend(): the users he follows) read it now; the other users do not.
     */
    public function testTheFriendsOfTheAuthorReadAFileOfHisFriends(): void
    {
        // The user 7 is a friend of the author 5, the user 8 is not.
        $profile = $this->createStub(KunenaProfile::class);
        $profile->method('isFriend')->willReturnCallback(static fn (int $userid, int $friendid): bool => $userid === 5 && $friendid === 7);

        $instance = new ReflectionProperty(KunenaProfile::class, 'instance');
        $saved    = $instance->getValue();
        $instance->setValue(null, $profile);

        try {
            $file = self::attachment(KunenaAttachment::PROTECTION_FRIENDS);

            $this->assertNull($file->tryAuthorise('read', self::user(7, false, false), false), 'a friend');
            $this->assertInstanceOf(KunenaExceptionAuthorise::class, $file->tryAuthorise('read', self::user(8, false, false), false), 'another user');
            $this->assertNull($file->tryAuthorise('read', self::user(5, false, false), false), 'the author');
        } finally {
            $instance->setValue(null, $saved);
        }
    }
}
