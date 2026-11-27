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

namespace Kunena\Tests\Unit\Component\Site\Controller;

use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The options "Allow Avatar Upload" and "Use Avatars Gallery" were applied by the form only: with the upload off, a request to the
 * task stored an uploaded avatar, and with the gallery off a profile save took a picture of the gallery and its list was given.
 * (The tasks need a whole site: their code is read.)
 */
class UserAvatarOptionsTest extends UnitTestCase
{
    private function method(string $name): string
    {
        $source = str_replace("\r\n", "\n", Files::read('src/site/src/Controllers/UserController.php'));

        $this->assertSame(1, preg_match('/(?:public|protected) function ' . $name . '\(\)[^{]*\{(.*?)\n    \}\n/s', $source, $m), $name);

        return $m[1];
    }

    public function testTheUploadIsRefusedWhenItIsOff(): void
    {
        $upload = $this->method('upload');
        $check  = strpos($upload, 'if (!$this->config->allowAvatarUpload) {');

        $this->assertNotFalse($check);
        $this->assertLessThan(strpos($upload, 'KunenaUpload::getInstance()'), $check, 'Refused before anything is uploaded.');
    }

    public function testTheGalleryIsNotUsedWhenItIsOff(): void
    {
        $this->assertMatchesRegularExpression('/\$this->config->allowAvatarGallery\s*&& !empty\(\$avatar_gallery\)/', $this->method('saveProfile'));
        $this->assertStringContainsString('if ($this->me->exists() && $this->config->allowAvatarGallery) {', $this->method('galleryimages'));
    }
}
