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
 * An uploaded avatar (not an SVG, which is cleaned) is a picture which can be shown and resized before the old one is removed: any
 * content with the name of a picture became the avatar (the upload does not check the type of an avatar), served as it was, and every
 * page which showed it logged an error. The temporary file of the upload is removed (it stayed in the folder of the uploads for
 * good). (The task needs a whole site: its code is read.)
 */
class UserAvatarUploadImageTest extends UnitTestCase
{
    private static function picture(): string
    {
        $source = str_replace("\r\n", "\n", Files::read('src/site/src/Controllers/UserController.php'));
        $start  = strpos($source, '    public function upload()');
        $body   = substr($source, $start, (int) strpos($source, "\n    }\n", $start) - $start);

        // The branch of the files which are not an SVG.
        $else = strpos($body, "                } else {\n", (int) strpos($body, "strtolower(\$extension) === 'svg'"));

        return $else === false ? '' : substr($body, $else, (int) strpos($body, "\n                }\n", $else) - $else);
    }

    public function testTheFileIsAPictureBeforeTheOldAvatarGoes(): void
    {
        $branch = self::picture();
        $check  = strpos($branch, '$info = @getimagesize($uploadFile);');

        $this->assertNotFalse($check);
        $this->assertStringContainsString("stripos((string) \$info['mime'], 'image/') !== 0", $branch);
        $this->assertStringContainsString('KunenaImage::canDecode((int) $info[0], (int) $info[1])', $branch);
        $this->assertLessThan(strpos($branch, '$this->deleteOldAvatars($user);'), $check);
        $this->assertStringContainsString("\nCOM_KUNENA_UPLOAD_ERROR_AVATAR_NOT_IMAGE = \"", Files::read('src/site/language/en-GB/com_kunena.ini'));
    }

    public function testTheTemporaryFileIsRemoved(): void
    {
        $branch = self::picture();

        $this->assertGreaterThan(strpos($branch, 'File::copy($uploadFile,'), strrpos($branch, 'File::delete($uploadFile);'));
    }
}
