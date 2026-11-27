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
use Kunena\Forum\Libraries\Forum\Message\KunenaMessage;
use Kunena\Forum\Libraries\User\KunenaUser;
use Kunena\Tests\Unit\UnitTestCase;
use ReflectionProperty;

/**
 * A file without protection ("Protect Attachments" off, the default) is for who may read its message, and a file which waits for
 * its message for its uploader. The right of the file did not look at the message: its address (view=attachment&id=N, the ids
 * follow each other) gave the files of the hidden categories, of the unapproved and of the deleted messages to any member, and the
 * uploads of the others.
 */
class KunenaAttachmentUnprotectedReadTest extends UnitTestCase
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

    private static function member(int $userid): KunenaUser
    {
        $user = static::bare(KunenaUser::class, ['userid' => $userid]);
        (new ReflectionProperty(KunenaUser::class, '_exists'))->setValue($user, true);

        return $user;
    }

    /**
     * An unprotected image of the user 5 in a message which the reader may read or not.
     */
    private function file(int $mesid, bool $readable): KunenaAttachment
    {
        $message = $this->createStub(KunenaMessage::class);
        $message->method('tryAuthorise')->willReturn($readable ? null : new KunenaExceptionAuthorise('COM_KUNENA_NO_ACCESS', 403));

        $file = new class () extends KunenaAttachment {
            public ?KunenaMessage $stub = null;

            public function __construct()
            {
            }

            public function getMessage(): KunenaMessage
            {
                return $this->stub;
            }

            public function isImage(): bool
            {
                return true;
            }
        };

        $file->stub = $message;

        foreach (['id' => 12, 'mesid' => $mesid, 'userid' => 5, 'protected' => 0, '_exists' => true] as $name => $value) {
            (new ReflectionProperty(KunenaAttachment::class, $name))->setValue($file, $value);
        }

        return $file;
    }

    public function testTheFileOfAMessageWhichTheReaderMayNotReadIsRefused(): void
    {
        $this->assertInstanceOf(KunenaExceptionAuthorise::class, $this->file(30, false)->tryAuthorise('read', self::member(7), false));
        $this->assertNull($this->file(30, true)->tryAuthorise('read', self::member(7), false));
    }

    public function testAnUploadWhichWaitsForItsMessageIsOnlyForItsUploader(): void
    {
        $this->assertInstanceOf(KunenaExceptionAuthorise::class, $this->file(0, true)->tryAuthorise('read', self::member(7), false));
        $this->assertNull($this->file(0, true)->tryAuthorise('read', self::member(5), false));
    }
}
