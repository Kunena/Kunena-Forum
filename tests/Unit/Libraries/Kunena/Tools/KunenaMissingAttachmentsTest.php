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

namespace Kunena\Tests\Unit\Libraries\Kunena\Tools;

use Kunena\Forum\Libraries\Tools\KunenaMissingAttachments as M;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The tool "Missing attachment files" lists the attachments whose file is gone, and only reads files of the folders of
 * Kunena.
 */
class KunenaMissingAttachmentsTest extends UnitTestCase
{
    public function testThePathOfAnAttachment(): void
    {
        $this->assertSame('media/kunena/attachments/42/a.jpg', M::path(['folder' => '/media/kunena/attachments/42/', 'filename' => 'a.jpg']));
        $this->assertSame('media/kunena/attachments/42/a.jpg', M::path(['folder' => 'media\\kunena\\attachments\\42', 'filename' => 'a.jpg']));
    }

    public function testAPathOutOfTheFoldersOfKunenaIsNotRead(): void
    {
        $this->assertSame('', M::path(['folder' => 'media/kunena/attachments/../../..', 'filename' => 'configuration.php']));
        $this->assertSame('', M::path(['folder' => 'images', 'filename' => 'a.jpg']));
        $this->assertSame('', M::path(['folder' => 'media/kunena/attachments', 'filename' => 'x/a.jpg']));
        $this->assertSame('', M::path(['folder' => '', 'filename' => 'a.jpg']));
    }

    public function testTheAttachmentsWithoutTheirFile(): void
    {
        $rows = [
            ['id' => 1, 'folder' => 'media/kunena/attachments/1', 'filename' => 'there.png'],
            ['id' => 2, 'folder' => 'media/kunena/attachments/1', 'filename' => 'gone.png'],
            ['id' => 3, 'folder' => '../etc', 'filename' => 'passwd'],
        ];
        $files = ['media/kunena/attachments/1/there.png'];

        $this->assertSame([2, 3], array_column(M::missing($rows, static fn ($path) => \in_array($path, $files, true)), 'id'));
    }
}
