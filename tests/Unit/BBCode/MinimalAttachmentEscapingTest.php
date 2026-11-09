<?php

/**
 * Kunena Component
 *
 * @package  Kunena.Tests
 * @license  https://www.gnu.org/copyleft/gpl.html GNU/GPL
 **/

namespace Kunena\Tests\Unit\BBCode;

use Kunena\Forum\Libraries\Attachment\KunenaAttachment;
use Kunena\Forum\Libraries\BBCode\KunenaBBCodeLibrary;
use Nbbc\BBCode;
use PHPUnit\Framework\TestCase;

class MinimalAttachmentEscapingTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        if (!\defined('_JEXEC')) {
            \define('_JEXEC', 1);
        }

        if (!\defined('KPATH_FRAMEWORK')) {
            \define('KPATH_FRAMEWORK', realpath(__DIR__ . '/../../../src/libraries/kunena'));
        }

        require_once __DIR__ . '/fixtures/quote_stubs.php';
        require_once KPATH_FRAMEWORK . '/src/Database/KunenaDatabaseObject.php';
        require_once KPATH_FRAMEWORK . '/src/Attachment/KunenaAttachment.php';
        require_once KPATH_FRAMEWORK . '/src/BBCode/KunenaBBCode.php';
    }

    private function render(string $filename, bool $protected, bool $readable = true): string
    {
        $attachment = $this->getMockBuilder(KunenaAttachment::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['exists', 'isAuthorised', 'getUrl'])
            ->getMock();
        $attachment->method('exists')->willReturn(true);
        $attachment->method('isAuthorised')->willReturn($readable);
        $attachment->expects($readable ? $this->once() : $this->never())->method('getUrl')->willReturn('/download');
        $attachment->id            = 1;
        $attachment->filename      = $protected ? 'stored.pdf' : $filename;
        $attachment->filename_real = $filename;
        $attachment->protected     = (int) $protected;
        $attachment->filetype      = 'application/pdf';
        $attachment->size          = 1024;

        $library = (new \ReflectionClass(KunenaBBCodeLibrary::class))->newInstanceWithoutConstructor();
        $library->config = (object) ['showFileForGuest' => 1];
        $bbcode = (object) ['parent' => (object) ['attachments' => [$attachment], 'forceMinimal' => true]];

        return $library->DoAttachment($bbcode, BBCode::BBCODE_OUTPUT, 'attachment', '', [], '');
    }

    public function testUnprotectedFilenameIsRenderedAsText(): void
    {
        $html = $this->render('x"><img src=x onerror=alert(1)>.pdf', false);

        $this->assertStringContainsString('>x&quot;&gt;&lt;img src=x onerror=alert(1)&gt;.pdf</a>', $html);
        $this->assertStringNotContainsString('<img', $html);
    }

    public function testProtectedFilenameIsEscapedAndUsesTheOriginalName(): void
    {
        $html = $this->render('résumé & <report>.pdf', true);

        $this->assertStringContainsString('>résumé &amp; &lt;report&gt;.pdf</a>', $html);
        $this->assertStringNotContainsString('stored.pdf', $html);
    }

    public function testUnreadableAttachmentRemainsHidden(): void
    {
        $this->assertSame('', $this->render('secret.pdf', false, false));
    }
}
