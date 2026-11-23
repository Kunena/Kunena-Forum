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
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The name of an attachment which the forum shows (the list of the files, the name of a download). The file of an anonymous message
 * gets a random name on the disk (its folder and its name told who wrote the message), and its name is kept in filename_real. Without
 * the protection of the attachments (the default) the forum showed the name on the disk: 32 hexadecimal characters without an
 * extension, so the download had no extension either.
 */
class KunenaAttachmentShownNameTest extends UnitTestCase
{
    private const RANDOM = '3f9c0a1b2c3d4e5f60718293a4b5c6e1';

    public function testARandomNameOnTheDiskShowsTheNameOfTheUpload(): void
    {
        $this->requireJoomla();

        $attachment = self::bare(KunenaAttachment::class, ['protected' => 0, 'filename' => self::RANDOM, 'filename_real' => 'report.pdf']);

        $this->assertSame(['report.pdf', 'pdf'], [$attachment->getFilename(false), $attachment->getExtension(false)]);
    }
}
