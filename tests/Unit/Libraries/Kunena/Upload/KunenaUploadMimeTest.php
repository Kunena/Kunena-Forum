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
 * The options "Check MIME Types" and "Legal MIME Types" of the attachments: an uploaded image whose real type is not in the list
 * is refused. Nothing read the two options (the security audit of the tools reported the check as done, and the text of the
 * refusal was in the language file): a file with the name of a picture and another content was accepted.
 */
class KunenaUploadMimeTest extends UnitTestCase
{
    private const ALLOWED = 'image/jpeg,image/jpg,image/gif,image/png,image/webp,image/avif';

    private const EXTENSIONS = 'jpg,jpeg,gif,png,webp,avif';

    public static function uploads(): array
    {
        // The name, the type read from the file, the option "Check MIME Types", refused.
        return [
            'a real picture'                    => ['photo.jpg', 'image/jpeg', true, false],
            'a type in capitals'                => ['photo.PNG', 'IMAGE/PNG', true, false],
            'a type with a character set'       => ['photo.gif', 'image/gif; charset=binary', true, false],
            'HTML with the name of a picture'   => ['photo.jpg', 'text/html', true, true],
            'a script with the name of a photo' => ['photo.png', 'text/x-php', true, true],
            'an image type which is not listed' => ['drawing.svg', 'image/svg+xml', true, true],
            'a picture with another extension'  => ['notes.txt', 'image/bmp', true, true],
            'another file'                      => ['archive.zip', 'application/zip', true, false],
            'a type which could not be read'    => ['photo.jpg', '', true, false],
            'the check switched off'            => ['photo.jpg', 'text/html', false, false],
        ];
    }

    #[DataProvider('uploads')]
    public function testAnImageOfAnotherTypeIsRefused(string $name, string $mime, bool $check, bool $refused): void
    {
        $this->assertSame($refused, KunenaUpload::mimeRefused($name, $mime, $check, self::ALLOWED, self::EXTENSIONS));
    }

    public function testAnEmptyListAllowsEveryType(): void
    {
        $this->assertFalse(KunenaUpload::mimeRefused('photo.jpg', 'text/html', true, ' ', self::EXTENSIONS));
    }

    public function testTheFinishedUploadIsChecked(): void
    {
        $source = Files::read('src/libraries/kunena/src/Upload/KunenaUpload.php');

        $this->assertSame(1, preg_match('/public function ajaxUpload\(array \$options\): array\s*\{(.*?)\n    \}\n/s', $source, $m));
        $this->assertStringContainsString('(bool) $config->checkMimeTypes, (string) $config->imageMimeTypes, (string) $config->imageTypes)', $m[1]);
        $this->assertStringContainsString("Text::sprintf('COM_KUNENA_UPLOAD_ERROR_MIME'", $m[1]);
    }
}
