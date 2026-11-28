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
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\Exception\KunenaExceptionAuthorise;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategory;
use Kunena\Forum\Libraries\Forum\Message\KunenaMessage;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopic;
use Kunena\Forum\Libraries\User\KunenaUser;
use Kunena\Tests\Unit\UnitTestCase;
use ReflectionProperty;

/**
 * A guest gets no file of a reply in a category which hides the replies from the guests (they log in to read them): the text of the
 * reply was hidden, its files were shown under it and given. The files of the first post stay, as its text does.
 */
class KunenaAttachmentGuestTeaserTest extends UnitTestCase
{
    private mixed $config = null;

    protected function setUp(): void
    {
        $this->requireJoomla();

        $property     = new ReflectionProperty(KunenaConfig::class, 'instance');
        $this->config = $property->getValue();
        $property->setValue(null, self::bare(KunenaConfig::class, ['config' => ['showImgForGuest' => 1, 'showFileForGuest' => 1]]));
    }

    protected function tearDown(): void
    {
        (new ReflectionProperty(KunenaConfig::class, 'instance'))->setValue(null, $this->config);

        parent::tearDown();
    }

    /**
     * Whether a guest may read a file of the message $mesid of a topic whose first post is 30, in a category which hides the replies.
     */
    private function guestReads(int $mesid): bool
    {
        $topic = self::bare(KunenaTopic::class, ['first_post_id' => 30]);

        $category = $this->createStub(KunenaCategory::class);
        $category->method('hidesRepliesForGuests')->willReturn(true);

        $message = $this->createStub(KunenaMessage::class);
        $message->method('exists')->willReturn(true);
        $message->method('getTopic')->willReturn($topic);
        $message->method('getCategory')->willReturn($category);
        $message->method('tryAuthorise')->willReturn(null);
        $message->id = $mesid;

        $attachment = new class ($message) extends KunenaAttachment {
            public function __construct(private KunenaMessage $stubMessage)
            {
            }

            public function getMessage(): KunenaMessage
            {
                return $this->stubMessage;
            }
        };

        $attachment->id        = 12;
        $attachment->mesid     = $mesid;
        $attachment->userid    = 5;
        $attachment->protected = 0;
        $attachment->filetype  = 'image/png';
        (new ReflectionProperty(KunenaAttachment::class, '_exists'))->setValue($attachment, true);

        $guest = static::bare(KunenaUser::class, ['userid' => 0, '_exists' => false]);

        return !$attachment->tryAuthorise('read', $guest, false) instanceof KunenaExceptionAuthorise;
    }

    public function testAGuestGetsNoFileOfAHiddenReply(): void
    {
        $this->assertSame(['first post' => true, 'reply' => false], ['first post' => $this->guestReads(30), 'reply' => $this->guestReads(31)]);
    }
}
