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

use Kunena\Forum\Libraries\Tools\KunenaContentLimits as T;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The tool "Content over the limits" finds the attachments and the signatures which a lowered limit no longer allows.
 */
class KunenaContentLimitsTest extends UnitTestCase
{
    public function testTheExtensionsOfASetting(): void
    {
        $this->assertSame(['jpg', 'png', 'gif'], T::extensions(' JPG, .png,gif,,jpg'));
        $this->assertSame([], T::extensions(''));
    }

    public function testTheAttachmentsOverTheLimits(): void
    {
        $items = T::attachments([
            ['id' => 1, 'mesid' => 1, 'userid' => 1, 'size' => 100, 'filetype' => 'image/png', 'filename' => 'a.png'],
            ['id' => 2, 'mesid' => 1, 'userid' => 1, 'size' => 100, 'filetype' => 'application/x-msdownload', 'filename' => 'b.exe'],
            ['id' => 3, 'mesid' => 1, 'userid' => 1, 'size' => 5000, 'filetype' => 'image/jpeg', 'filename' => 'c.JPG'],
            ['id' => 4, 'mesid' => 1, 'userid' => 1, 'size' => 5000, 'filetype' => 'application/pdf', 'filename' => 'd.pdf'],
            ['id' => 5, 'mesid' => 1, 'userid' => 1, 'size' => 900, 'filetype' => 'application/zip', 'filename' => 'e.zip'],
        ], ['png', 'jpg'], ['pdf', 'zip'], 1024, 0);

        // No size limit for the other files.
        $this->assertSame([2 => 'type', 3 => 'size'], array_column($items, 'problem', 'id'));
        $this->assertSame(1024, $items[1]['limit']);
    }

    public function testTheSignaturesOverTheLimit(): void
    {
        $rows = [['userid' => 1, 'username' => 'a', 'signature' => str_repeat('é', 12)], ['userid' => 2, 'username' => 'b', 'signature' => 'short'], ['userid' => 3, 'username' => 'c', 'signature' => str_repeat('x', 20)]];

        $this->assertSame([3, 1], array_column(T::signatures($rows, 10), 'userid'));
        $this->assertSame([], T::signatures($rows, 0));
    }

    public function testAShortenedSignature(): void
    {
        $this->assertSame('Visit [b]my', T::shorten('Visit [b]my[/b] site', 11));
        $this->assertSame('Visit', T::shorten('Visit [url=https://example.org]site[/url]', 12));
        $this->assertSame('ééé', T::shorten('éééé', 3));
        $this->assertSame('short', T::shorten('short', 10));
    }
}
