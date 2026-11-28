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
use Kunena\Forum\Libraries\Forum\Message\KunenaMessage;
use Kunena\Forum\Libraries\User\KunenaUser;
use Kunena\Tests\Unit\UnitTestCase;
use ReflectionProperty;

/**
 * A private attachment is for a user of its private part who may read its message: a recipient got the file of a reply which waited
 * for approval, was deleted, or was moved into a category which he may not read (the text of the private part was hidden, the file,
 * with its id in the address, was not).
 */
class KunenaAttachmentPrivateMessageReadTest extends UnitTestCase
{
    private function attachment(bool $messageReadable): KunenaAttachment
    {
        $message = $this->createStub(KunenaMessage::class);
        $message->method('isAuthorised')->willReturn($messageReadable);

        $attachment = new class ($message) extends KunenaAttachment {
            public function __construct(private KunenaMessage $stubMessage)
            {
            }

            // The user 1 is a user of the private part.
            protected function loadPrivateAuthorisation(KunenaUser $user): ?KunenaExceptionAuthorise
            {
                return null;
            }

            public function getMessage(): KunenaMessage
            {
                return $this->stubMessage;
            }
        };

        $attachment->id        = 12;
        $attachment->mesid     = 30;
        $attachment->userid    = 5;
        $attachment->protected = KunenaAttachment::PROTECTION_PRIVATE;
        (new ReflectionProperty(KunenaAttachment::class, '_exists'))->setValue($attachment, true);

        return $attachment;
    }

    public function testTheMessageMustBeReadable(): void
    {
        $this->requireJoomla();

        $recipient = static::bare(KunenaUser::class, ['userid' => 1, '_exists' => true]);

        $this->assertNull($this->attachment(true)->tryAuthorise('read', $recipient, false), 'a message which he may read');
        $this->assertInstanceOf(KunenaExceptionAuthorise::class, $this->attachment(false)->tryAuthorise('read', $recipient, false), 'a message which he may not read');
    }
}
