<?php

/**
 * Kunena Component
 *
 * Regression test: a protected attachment from the classic upload must get a random name on the disk (so its address
 * can not be guessed and the access check can not be skipped), and the "real" name must be only what the user called
 * the file, never a path of the server.
 *
 * @package         Kunena.Tests
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 **/

namespace Kunena\Tests\Unit\Attachment;

use Kunena\Forum\Libraries\Attachment\KunenaAttachmentHelper;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ProtectedUploadNamingTest extends TestCase
{
    private const ATTACHMENT = __DIR__ . '/../../../src/libraries/kunena/src/Attachment/KunenaAttachment.php';

    public static function setUpBeforeClass(): void
    {
        if (!\defined('_JEXEC')) {
            \define('_JEXEC', 1);
        }

        require_once __DIR__ . '/../../../src/libraries/kunena/src/Attachment/KunenaAttachmentHelper.php';
    }

    #[DataProvider('names')]
    public function testClientFilename(mixed $input, string $expected): void
    {
        $this->assertSame($expected, KunenaAttachmentHelper::getClientFilename($input));
    }

    public static function names(): array
    {
        return [
            'plain'                    => ['report.pdf', 'report.pdf'],
            'spaces are kept'          => ['my holiday photo.jpg', 'my holiday photo.jpg'],
            'unicode'                  => ['résumé ünï.pdf', 'résumé ünï.pdf'],
            'unix path'                => ['/var/www/site/media/kunena/attachments/46/x.pdf', 'x.pdf'],
            'windows path'             => ['C:\\Users\\me\\Documents\\x.pdf', 'x.pdf'],
            'mixed separators'         => ['..\\..//etc/x.pdf', 'x.pdf'],
            'parent folder'            => ['../../x.pdf', 'x.pdf'],
            'control characters'       => ["x\0.pdf", 'x.pdf'],
            'line break'               => ["a\r\nb.pdf", 'ab.pdf'],
            'surrounding spaces'       => ['  x.pdf  ', 'x.pdf'],
            'only a dot'               => ['.', ''],
            'only two dots'            => ['..', ''],
            'ends with separator'      => ['folder/', 'folder'],
            'empty'                    => ['', ''],
            'null'                     => [null, ''],
            'array'                    => [['x.pdf'], ''],
        ];
    }

    public function testClientFilenameIsShortened(): void
    {
        $name = KunenaAttachmentHelper::getClientFilename(str_repeat('é', 400) . '.pdf');

        $this->assertSame(255, mb_strlen($name, 'UTF-8'));
    }

    public function testClientFilenameNeverContainsAServerPath(): void
    {
        $name = KunenaAttachmentHelper::getClientFilename(__FILE__);

        $this->assertSame(basename(__FILE__), $name);
        $this->assertStringNotContainsString(DIRECTORY_SEPARATOR, $name);
        $this->assertStringNotContainsString('/', $name);
    }

    private function upload(): string
    {
        $source = file_get_contents(self::ATTACHMENT);

        $this->assertSame(1, preg_match('/public function upload\([^)]*\)[^{]*\{(.*?)\n    \}\n/s', $source, $m));

        return $m[1];
    }

    public function testProtectedFilesGetARandomName(): void
    {
        $body = $this->upload();

        $this->assertMatchesRegularExpression(
            '/if \(\$protected\) \{.*?KunenaAttachmentHelper::getAvailableFilename\([^;]*,\s*true\);/s',
            $body,
            'A protected file must be stored under a random name.'
        );
    }

    public function testTheRandomNameIsChosenBeforeTheFileIsStored(): void
    {
        $body = $this->upload();

        $name  = strpos($body, 'KunenaAttachmentHelper::getAvailableFilename(');
        $store = strpos($body, '$upload->upload(');

        $this->assertNotFalse($name, 'upload() must ask for a random name.');
        $this->assertNotFalse($store);
        $this->assertLessThan($store, $name, 'The name has to be known before the file is written.');
    }

    public function testTheProtectionSettingIsReadOnceAndUsedForBoth(): void
    {
        $body = $this->upload();

        $this->assertSame(1, substr_count($body, '$config->attachmentProtection'), 'Name and flag must come from the same value.');
        $this->assertStringContainsString('$this->protected     = $protected;', $body);
    }

    public function testRealNameIsTheNameOfTheClient(): void
    {
        $body = $this->upload();

        $this->assertStringContainsString('KunenaAttachmentHelper::getClientFilename($fileInput[\'name\'])', $body);
        $this->assertStringContainsString('$this->filename_real = $clientName', $body);
    }

    public function testNoServerPathIsStored(): void
    {
        $body = $this->upload();

        $this->assertDoesNotMatchRegularExpression('/filename_real\s*=\s*\$uploadBasePath/', $body, 'filename_real was an absolute path of the server.');
    }

    public function testClientNameIsTakenBeforeTheNameIsChanged(): void
    {
        $body = $this->upload();

        $taken   = strpos($body, '$clientName = ');
        $changed = strpos($body, "\$fileInput['name'] = preg_replace(");

        $this->assertNotFalse($taken, 'upload() must keep the name the user sent.');
        $this->assertNotFalse($changed);
        $this->assertLessThan($changed, $taken, 'Take the name the user sent before the spaces are removed from it.');
    }
}
