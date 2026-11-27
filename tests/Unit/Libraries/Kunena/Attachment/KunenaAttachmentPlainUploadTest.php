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

use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The upload of the posting form without JavaScript (KunenaAttachment::upload()). Three things it did not do:
 * - the options "Check MIME Types" and "Legal MIME Types" (as the upload of the editor: KunenaUploadMimeTest);
 * - a file which is refused after it was stored (a picture which cannot be read, with too many pixels, or which cannot be made
 *   smaller) stayed in the folder of the user, reachable by its address when the attachments are not protected;
 * - a picture which was made smaller kept the size of the upload in its record.
 * (The upload needs a posted file and the folders of a site: its code is read.)
 */
class KunenaAttachmentPlainUploadTest extends UnitTestCase
{
    private function upload(): string
    {
        $source = str_replace("\r\n", "\n", Files::read('src/libraries/kunena/src/Attachment/KunenaAttachment.php'));

        $this->assertSame(1, preg_match('/\n    public function upload\(.*?\n    \}\n/s', $source, $m));

        return $m[0];
    }

    public function testTheTypeOfAnImageIsChecked(): void
    {
        $this->assertStringContainsString(
            'KunenaUpload::mimeRefused($fileNameWithExt, (string) $type, (bool) $config->checkMimeTypes, (string) $config->imageMimeTypes, (string) $config->imageTypes)',
            $this->upload()
        );
    }

    public function testARefusedFileIsTakenAwayAgain(): void
    {
        $body  = $this->upload();
        $start = strpos($body, '$stored = $uploadBasePath . $fileNameWithExt;');

        $this->assertIsInt($start);

        // After the file is stored, every refusal removes it first.
        $after = substr($body, $start);
        $end   = strpos($after, '$this->protected     = $protected ? 1 : 0;');

        $this->assertIsInt($end);

        $refusals = substr_count(substr($after, 0, $end), 'return false;');

        $this->assertSame(4, $refusals);
        $this->assertSame($refusals, substr_count(substr($after, 0, $end), "@unlink(\$stored);\n\n"), 'Each refusal removes the stored file.');
    }

    public function testTheSizeIsTheOneOfTheStoredFile(): void
    {
        $body = $this->upload();

        $this->assertStringContainsString('$this->size          = (int) (filesize($stored) ?: $file->size);', $body);
        $this->assertStringNotContainsString('$this->size          = $file->size;', $body);
    }
}
