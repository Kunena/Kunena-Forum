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

use Kunena\Forum\Libraries\Tools\KunenaStorageReport;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The tool "Storage" sorts the attachments by kind from their extension.
 */
class KunenaStorageReportTest extends UnitTestCase
{
    public function testTheKindOfAFileComesFromItsExtension(): void
    {
        $this->assertSame('image', KunenaStorageReport::kindOf('Photo.JPG'));
        $this->assertSame('video', KunenaStorageReport::kindOf('clip.mp4'));
        $this->assertSame('document', KunenaStorageReport::kindOf('notes.pdf'));
        $this->assertSame('archive', KunenaStorageReport::kindOf('backup.tar'));
        $this->assertSame('other', KunenaStorageReport::kindOf('program.exe'));
        $this->assertSame('other', KunenaStorageReport::kindOf('no-extension'));
    }
}
