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
use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * An attachment has two size limits: one for images and one for other files. While the upload ran, the limit was chosen by the
 * type which the browser named, so a client named the type with the larger limit (an archive as "image/png", or a picture as
 * "application/zip"). When the whole file is there, the limit of the type which is read from the file decides.
 */
class KunenaUploadSizeLimitTest extends UnitTestCase
{
    public static function uploads(): array
    {
        // The type read from the file, the size in bytes, the limit of images (KB), the limit of files (KB), what is exceeded.
        return [
            'image within its limit'               => ['image/png', 150 * 1024, 150, 120, null],
            'image one byte over'                  => ['image/png', 150 * 1024 + 1, 150, 120, 'image'],
            'image over, files may be larger'      => ['image/jpeg', 200 * 1024, 150, 5000, 'image'],
            'file within its limit'                => ['application/zip', 120 * 1024, 150, 120, null],
            'file one byte over'                   => ['application/zip', 120 * 1024 + 1, 150, 120, 'file'],
            'file over, images may be larger'      => ['application/pdf', 300 * 1024, 5000, 120, 'file'],
            'type in capitals'                     => ['IMAGE/GIF', 200 * 1024, 150, 5000, 'image'],
            'no type is a file'                    => ['', 200 * 1024, 5000, 120, 'file'],
            'text which names an image later'      => ['text/plain; image/png', 200 * 1024, 5000, 120, 'file'],
            'empty file'                           => ['image/png', 0, 150, 120, null],
        ];
    }

    #[DataProvider('uploads')]
    public function testLimitOfTheRealTypeDecides(string $mime, int $size, int $imageKb, int $fileKb, ?string $expected): void
    {
        $this->assertSame($expected, KunenaUpload::overLimit($mime, $size, $imageKb, $fileKb));
    }

    public function testFinishedUploadIsCheckedWithTheTypeReadFromTheFile(): void
    {
        $source = Files::read('src/libraries/kunena/src/Upload/KunenaUpload.php');

        $this->assertSame(1, preg_match('/public function ajaxUpload\(array \$options\): array\s*\{(.*?)\n    \}\n/s', $source, $m));

        $completed = strpos($m[1], "if (\$options['completed']) {\n            \$options['mime'] = KunenaFile::getMime(\$outFile);");
        $check     = strpos($m[1], "self::overLimit((string) \$options['mime'], (int) \$size,");

        $this->assertIsInt($completed, 'The type is read from the file when the upload is complete.');
        $this->assertIsInt($check);
        $this->assertLessThan($check, $completed, 'The limit is checked with the type that was read.');
        $this->assertLessThan(strpos($m[1], "\$options['hash'] = md5_file(\$outFile);"), $check, 'Before the upload is reported as complete.');
        $this->assertMatchesRegularExpression('/if \(\$over !== null\) \{\s*\$this->cleanup\(\);\s*throw new RuntimeException\(/', $m[1], 'The file is removed and the upload refused.');
        $this->assertStringContainsString("if (\$options['image_type'] != 'avatar') {", $m[1], 'An avatar has its own limit.');
    }
}
