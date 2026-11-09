<?php

/**
 * Kunena Component
 *
 * @package  Kunena.Tests
 * @license  https://www.gnu.org/copyleft/gpl.html GNU/GPL
 **/

namespace Kunena\Tests\Unit\Attachment;

use Kunena\Forum\Libraries\Attachment\KunenaAttachmentHelper;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ContentDispositionTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        if (!\defined('_JEXEC')) {
            \define('_JEXEC', 1);
        }

        require_once __DIR__ . '/../../../src/libraries/kunena/src/Attachment/KunenaAttachmentHelper.php';
    }

    public static function filenames(): array
    {
        return [
            'plain'       => ['report.pdf', 'filename="report.pdf"; filename*=UTF-8\'\'report.pdf'],
            'unicode'     => ['résumé.pdf', 'filename="r__sum__.pdf"; filename*=UTF-8\'\'r%C3%A9sum%C3%A9.pdf'],
            'special'     => ['x"; foo="bar%20.pdf', 'filename="x_; foo=_bar_20.pdf"; filename*=UTF-8\'\'x%22%3B%20foo%3D%22bar%2520.pdf'],
            'controls'    => ["a\r\nb\0.pdf", 'filename="ab.pdf"; filename*=UTF-8\'\'ab.pdf'],
            'paths'       => ['../a\\b.pdf', 'filename=".._a_b.pdf"; filename*=UTF-8\'\'.._a_b.pdf'],
            'empty'       => ["\r\n", 'filename="attachment"; filename*=UTF-8\'\'attachment'],
            'invalid utf8' => ["a\xff.pdf", 'filename="a_.pdf"'],
        ];
    }

    #[DataProvider('filenames')]
    public function testFilenameParameters(string $filename, string $expected): void
    {
        $this->assertSame($expected, KunenaAttachmentHelper::getContentDispositionFilename($filename));
    }

    public function testBothDownloadModesUseTheSafeParameters(): void
    {
        $source = file_get_contents(__DIR__ . '/../../../src/site/src/Controller/Application/Attachment/Initial/AttachmentDisplay.php');

        $this->assertSame(2, substr_count($source, 'KunenaAttachmentHelper::getContentDispositionFilename($attachment->getFilename(false))'));
        $this->assertStringContainsString("Content-Disposition: inline; ", $source);
        $this->assertStringContainsString("Content-Disposition: attachment; ", $source);
    }
}
