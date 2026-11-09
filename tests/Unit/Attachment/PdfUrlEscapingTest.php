<?php

/**
 * Kunena Component
 *
 * @package  Kunena.Tests
 * @license  https://www.gnu.org/copyleft/gpl.html GNU/GPL
 **/

namespace Kunena\Tests\Unit\Attachment;

use Kunena\Forum\Libraries\Attachment\KunenaAttachment;
use Kunena\Tests\Unit\BBCode\Fixtures\GenericConfig;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use PHPUnit\Framework\TestCase;

class PdfUrlEscapingTest extends TestCase
{
    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testProtectedInlinePdfUrlHonoursEscapeArgument(): void
    {
        \define('_JEXEC', 1);
        require_once __DIR__ . '/fixtures/url_stubs.php';
        require_once __DIR__ . '/../BBCode/fixtures/generic_config.php';
        class_alias(GenericConfig::class, 'Kunena\Forum\Libraries\Config\KunenaConfig');
        require_once __DIR__ . '/../../../src/libraries/kunena/src/Database/KunenaDatabaseObject.php';
        require_once __DIR__ . '/../../../src/libraries/kunena/src/Attachment/KunenaAttachment.php';

        GenericConfig::$values['attachmentProtection'] = 1;
        $attachment = (new \ReflectionClass(KunenaAttachment::class))->newInstanceWithoutConstructor();
        $attachment->id = 1;
        $attachment->folder = 'media';
        $attachment->filename = 'x"><img src=x>.pdf';
        $attachment->filetype = 'application/pdf';

        $this->assertSame('https://forum.example/media/x&quot;&gt;&lt;img src=x&gt;.pdf', $attachment->getUrl());
        $this->assertSame('https://forum.example/media/x"><img src=x>.pdf', $attachment->getUrl(false, true, false));
        $this->assertStringContainsString('&amp;download=1', $attachment->getUrl(false, false));
    }
}
