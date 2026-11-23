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
use Kunena\Forum\Libraries\Forum\Message\KunenaMessage;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopic;
use Kunena\Tests\Unit\UnitTestCase;
use ReflectionProperty;

/**
 * A file removed from a message takes its tags out of the text, and only them. The name of the file went into a regular
 * expression as it was: a name with a bracket (photo(1).jpg, as the upload without JavaScript keeps it) made the expression
 * invalid, and the whole text of the message was saved empty.
 */
class KunenaMessageRemoveAttachmentTextTest extends UnitTestCase
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

    /**
     * The text of a message after the removal of its file with the name given.
     */
    private function removeFile(string $filename, string $text): string
    {
        $attachment = $this->createPartialMock(KunenaAttachment::class, ['tryAuthorise', 'delete']);
        $attachment->method('tryAuthorise')->willReturn(null);
        $attachment->expects($this->once())->method('delete')->willReturn(true);
        $attachment->id       = 7;
        $attachment->mesid    = 10;
        $attachment->filename = $filename;

        $config = self::bare(KunenaConfig::class, ['config' => ['attachmentLimit' => 0]]);
        $topic  = self::bare(KunenaTopic::class, ['attachments' => 1]);

        $message = self::bare(KunenaMessage::class, [
            'id'               => 10,
            'message'          => $text,
            '_config'          => $config,
            '_topic'           => $topic,
            '_attachments_add' => [],
            '_attachments_del' => [7 => $attachment],
        ]);

        self::invoke($message, 'updateAttachments');

        return $message->message;
    }

    public function testTheTextStaysWhenTheNameOfTheFileHasABracket(): void
    {
        $this->assertSame(
            'My holiday.',
            $this->removeFile('photo(1.jpg', 'My holiday. [attachment]photo(1.jpg[/attachment]')
        );
    }

    public function testTheTagOfAFileWithBracketsInItsNameGoes(): void
    {
        // The brackets were a group of the expression: it looked for "photo1.jpg", and the tag of the removed file stayed.
        $this->assertSame(
            'My holiday.',
            $this->removeFile('photo(1).jpg', 'My holiday. [attachment]photo(1).jpg[/attachment]')
        );
    }

    public function testOnlyTheTagOfThatFileGoesWhenTheNameHasCharactersOfARegularExpression(): void
    {
        // The dot and the plus of the name are no pattern: the tag of another file which they would match stays.
        $this->assertSame(
            'A [attachment]axb++.txt[/attachment]',
            $this->removeFile('a.b++.txt', 'A [attachment]axb++.txt[/attachment] [attachment]a.b++.txt[/attachment]')
        );
    }
}
