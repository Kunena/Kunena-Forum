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

namespace Kunena\Tests\Unit\Component\Site\Controller\Application\Attachment\Initial;

use Kunena\Forum\Libraries\Attachment\KunenaAttachment;
use Kunena\Forum\Site\Controller\Application\Attachment\Initial\AttachmentDisplay;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The headers of an attachment sent by the forum (issue #7850): a PDF shown in a message is sent as a PDF, inline and without
 * the sandbox which stops the viewer of Chromium; pictures and downloads keep their headers.
 */
class AttachmentDisplayTest extends UnitTestCase
{
    private const string SANDBOX = "Content-Security-Policy: default-src 'none'; img-src 'self' data:; style-src 'unsafe-inline'; sandbox";

    protected function setUp(): void
    {
        $this->requireJoomla();
    }

    private function headers(string $filetype, bool $download = false, bool $guestMayRead = false): array
    {
        $attachment = $this->bare(KunenaAttachment::class, [
            'protected'     => 0,
            'filename'      => 'report.pdf',
            'filename_real' => 'report.pdf',
            'filetype'      => $filetype,
        ]);

        return $this->invoke(AttachmentDisplay::class, 'fileHeaders', $attachment, $download, $guestMayRead);
    }

    /**
     * The values of the headers with the given name, in the order they are sent.
     */
    private function values(array $headers, string $name): array
    {
        $values = [];

        foreach ($headers as $line) {
            [$key, $value] = explode(':', $line, 2);

            if (strcasecmp($key, $name) === 0) {
                $values[] = trim($value);
            }
        }

        return $values;
    }

    public function testAnInlinePdfIsSentAsAPdf(): void
    {
        foreach (['application/pdf', 'application/pdf; text/html'] as $filetype) {
            $headers = $this->headers($filetype);

            $this->assertSame(['application/pdf'], $this->values($headers, 'Content-Type'));
            $this->assertSame(['inline; filename="report.pdf"; filename*=UTF-8\'\'report.pdf'], $this->values($headers, 'Content-Disposition'));
            $this->assertSame(["frame-ancestors 'self'"], $this->values($headers, 'Content-Security-Policy'));
            $this->assertSame([], $this->values($headers, 'Content-Description'));

            foreach ($headers as $line) {
                $this->assertStringNotContainsString('sandbox', $line);
            }
        }
    }

    public function testAnInlinePdfIsCachedWhenGuestsMayReadIt(): void
    {
        $headers = $this->headers('application/pdf', false, true);

        $this->assertSame(['public, max-age=3600'], $this->values($headers, 'Cache-Control'));
        $this->assertNotSame(['0'], $this->values($headers, 'Expires'));
        $this->assertCount(1, $this->values($headers, 'Expires'));
    }

    public function testAnInlinePdfIsRevalidatedWhenGuestsMayNotReadIt(): void
    {
        $headers = $this->headers('application/pdf', false, false);

        $this->assertSame(['must-revalidate, post-check=0, pre-check=0'], $this->values($headers, 'Cache-Control'));
        $this->assertSame(['0'], $this->values($headers, 'Expires'));
    }

    public function testAPdfAskedAsADownloadIsADownload(): void
    {
        $headers = $this->headers('application/pdf', true, true);

        $this->assertContains(self::SANDBOX, $headers);
        $this->assertContains('Content-Description: File Transfer', $headers);
        $this->assertSame(['application/force-download', 'application/octet-stream', 'application/download'], $this->values($headers, 'Content-Type'));
        $this->assertStringStartsWith('attachment; ', $this->values($headers, 'Content-Disposition')[0]);
        $this->assertSame(['must-revalidate, post-check=0, pre-check=0'], $this->values($headers, 'Cache-Control'));
    }

    public function testAnInlinePictureKeepsItsHeaders(): void
    {
        $headers = $this->headers('image/png', false, true);

        $this->assertContains(self::SANDBOX, $headers);
        $this->assertSame(['image/png'], $this->values($headers, 'Content-Type'));
        $this->assertStringStartsWith('inline; ', $this->values($headers, 'Content-Disposition')[0]);
        $this->assertSame(['public, max-age=3600'], $this->values($headers, 'Cache-Control'));
        $this->assertSame([], $this->values($headers, 'Content-Description'));
    }

    public function testAnotherFileIsADownload(): void
    {
        $headers = $this->headers('application/zip', false, true);

        $this->assertContains(self::SANDBOX, $headers);
        $this->assertContains('Content-Description: File Transfer', $headers);
        $this->assertSame(['application/force-download', 'application/octet-stream', 'application/download'], $this->values($headers, 'Content-Type'));
        $this->assertStringStartsWith('attachment; ', $this->values($headers, 'Content-Disposition')[0]);
        $this->assertSame(['0'], $this->values($headers, 'Expires'));
    }
}
