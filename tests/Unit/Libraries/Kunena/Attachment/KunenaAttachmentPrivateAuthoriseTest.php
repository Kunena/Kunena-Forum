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

use Kunena\Forum\Libraries\Attachment\KunenaAttachment;
use Kunena\Forum\Libraries\Exception\KunenaExceptionAuthorise;
use Kunena\Forum\Libraries\User\KunenaUser;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The check of a private attachment reads the private message and its users (five queries): a page which asks it twice for one
 * file (may the user delete it, may he read it) asks the database once for each user; the answer is the same for every action.
 */
class KunenaAttachmentPrivateAuthoriseTest extends UnitTestCase
{
    /**
     * An attachment which counts the checks of its private message, and answers "no access" for the user 2.
     */
    private function attachment(): KunenaAttachment
    {
        $attachment = new class () extends KunenaAttachment {
            public int $checks = 0;

            public function __construct()
            {
            }

            protected function loadPrivateAuthorisation(KunenaUser $user): ?KunenaExceptionAuthorise
            {
                $this->checks++;

                return $user->userid == 2 ? new KunenaExceptionAuthorise('No access', 403) : null;
            }
        };
        $attachment->id = 12;

        return $attachment;
    }

    private function user(int $userid): KunenaUser
    {
        return static::bare(KunenaUser::class, ['userid' => $userid, '_exists' => true]);
    }

    public function testTheCheckOfAPrivateAttachmentIsDoneOncePerUser(): void
    {
        $attachment = $this->attachment();
        $reader     = $this->user(1);
        $stranger   = $this->user(2);

        // The list of the attachments of a profile asks "delete", then "read": one check, the answer is the same.
        $this->assertNull(static::invoke($attachment, 'authorisePrivate', $reader, 'delete'));
        $this->assertNull(static::invoke($attachment, 'authorisePrivate', $reader, 'read'));
        $this->assertNull(static::invoke($attachment, 'authorisePrivate', $reader, 'read'));
        $this->assertSame(1, $attachment->checks);

        // Another user is checked of his own, with his own answer.
        $refused = static::invoke($attachment, 'authorisePrivate', $stranger, 'read');
        $this->assertInstanceOf(KunenaExceptionAuthorise::class, $refused);
        $this->assertSame($refused, static::invoke($attachment, 'authorisePrivate', $stranger, 'delete'));
        $this->assertSame(2, $attachment->checks);
    }

    public function testAGuestAndANewAttachmentNeedNoCheck(): void
    {
        // The answer to a guest has a text of the language of Joomla.
        $this->requireJoomla();

        $attachment = $this->attachment();

        $guest = static::bare(KunenaUser::class, ['userid' => 0, '_exists' => false]);
        $this->assertInstanceOf(KunenaExceptionAuthorise::class, static::invoke($attachment, 'authorisePrivate', $guest, 'read'));
        $this->assertFalse(static::invoke($attachment, 'authorisePrivate', $this->user(1), 'create'));
        $this->assertSame(0, $attachment->checks);
    }
}
