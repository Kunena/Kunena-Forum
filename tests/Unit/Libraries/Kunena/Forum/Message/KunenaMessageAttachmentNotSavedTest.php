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

namespace Kunena\Tests\Unit\Libraries\Kunena\Forum\Message;

use Joomla\CMS\Application\CMSApplicationInterface;
use Joomla\CMS\Factory;
use Kunena\Forum\Libraries\Attachment\KunenaAttachment;
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategory;
use Kunena\Forum\Libraries\Forum\Message\KunenaMessage;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopic;
use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;
use ReflectionProperty;
use UnexpectedValueException;

/**
 * A file of a post which was not saved (the upload without the uploader refused it: its type, its pixels; or its save failed) is not
 * counted in the topic, and its tag goes from the text: the topic counted one file more for good, and the text had an empty tag
 * [attachment=][/attachment]. The upload adds no attachment for a file which it refused.
 */
class KunenaMessageAttachmentNotSavedTest extends UnitTestCase
{
    private mixed $application = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->requireJoomla();

        $this->application    = (new ReflectionProperty(Factory::class, 'application'))->getValue();
        Factory::$application = $this->createStub(CMSApplicationInterface::class);
    }

    protected function tearDown(): void
    {
        Factory::$application = $this->application;

        parent::tearDown();
    }

    public function testAFileWhichIsNotSavedIsNotCountedNorTagged(): void
    {
        // A new file, as the upload left it when it refused it: no folder, no name (its table refuses to save it).
        $attachment = $this->createPartialMock(KunenaAttachment::class, ['exists', 'save', 'IsImage']);
        $attachment->method('exists')->willReturn(false);
        $attachment->method('IsImage')->willReturn(false);
        $attachment->expects($this->once())->method('save')->willThrowException(new UnexpectedValueException('No folder'));
        $attachment->mesid = 0;

        $category = $this->createStub(KunenaCategory::class);
        $category->method('tryAuthorise')->willReturn(null);

        $topic   = self::bare(KunenaTopic::class, ['attachments' => 2]);
        $message = $this->createPartialMock(KunenaMessage::class, ['getCategory']);
        $message->expects($this->once())->method('getCategory')->willReturn($category);

        foreach ([
            'id'               => 10,
            'message'          => 'See [attachment:1]photo.jpg[/attachment]',
            '_config'          => self::bare(KunenaConfig::class, ['config' => ['attachmentLimit' => 0]]),
            '_topic'           => $topic,
            '_attachments_add' => [1 => $attachment],
            '_attachments_del' => [],
        ] as $name => $value) {
            (new ReflectionProperty(KunenaMessage::class, $name))->setValue($message, $value);
        }

        self::invoke($message, 'updateAttachments');

        $this->assertSame(['attachments' => 2, 'message' => 'See'], ['attachments' => (int) $topic->attachments, 'message' => $message->message]);
    }

    public function testTheUploadAddsNoFileWhichItRefused(): void
    {
        $source = str_replace("\r\n", "\n", Files::read('src/libraries/kunena/src/Forum/Message/KunenaMessage.php'));
        $start  = strpos($source, '    public function uploadAttachment(');
        $body   = substr($source, $start, (int) strpos($source, "\n    }\n", $start) - $start);
        $check  = strpos($body, 'if (!$success) {');

        $this->assertNotFalse($check);
        $this->assertLessThan(strpos($body, '$this->_attachments_add[$tmpid] = $attachment;'), $check);
    }
}
