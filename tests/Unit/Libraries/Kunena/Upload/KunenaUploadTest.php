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

namespace Kunena\Tests\Unit\Libraries\Kunena\Upload;

use Kunena\Forum\Libraries\Upload\KunenaUpload;
use Kunena\Tests\Unit\UnitTestCase;
use ReflectionProperty;

/**
 * SVG stays forbidden for every upload: the configuration cannot allow it through addExtensions(), only the avatar upload
 * allows it with allowSvgAvatar() (the file is cleaned before it is stored).
 */
class KunenaUploadTest extends UnitTestCase
{
    private static function validExtensions(KunenaUpload $upload): array
    {
        return (new ReflectionProperty(KunenaUpload::class, 'validExtensions'))->getValue($upload);
    }

    public function testAddExtensionsNeverAllowsSvg(): void
    {
        $upload = self::bare(KunenaUpload::class);
        $upload->addExtensions(['png', 'svg', '.SVG']);

        $extensions = self::validExtensions($upload);

        $this->assertContains('.png', $extensions);
        $this->assertNotContains('.svg', array_map('strtolower', $extensions));
    }

    public function testAllowSvgAvatarAllowsSvg(): void
    {
        $upload = self::bare(KunenaUpload::class);
        $upload->addExtensions(['png', 'svg']);

        $this->assertSame($upload, $upload->allowSvgAvatar());

        $extensions = self::validExtensions($upload);

        $this->assertContains('.png', $extensions);
        $this->assertContains('.svg', $extensions);
    }
}
