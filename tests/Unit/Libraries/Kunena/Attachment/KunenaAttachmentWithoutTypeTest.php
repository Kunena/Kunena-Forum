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

use ErrorException;
use Kunena\Forum\Libraries\Attachment\KunenaAttachment;
use Kunena\Tests\Unit\UnitTestCase;
use ReflectionClass;

/**
 * An attachment without a file type (an upload whose type was not found, an old row) is no image, no video, no sound and no PDF.
 * The checks gave null to stripos(), which PHP 8.4 reports as deprecated (the server log of the test site had it for the new
 * topic form) and a later PHP refuses.
 */
class KunenaAttachmentWithoutTypeTest extends UnitTestCase
{
    public function testAnAttachmentWithoutATypeIsNoneOfTheKinds(): void
    {
        $attachment           = (new ReflectionClass(KunenaAttachment::class))->newInstanceWithoutConstructor();
        $attachment->filetype = null;

        set_error_handler(static function (int $level, string $message): bool {
            throw new ErrorException($message, 0, $level);
        }, E_DEPRECATED);

        try {
            $kinds = [$attachment->isImage(), $attachment->isAudio(), $attachment->isVideo(), $attachment->isPdf()];
        } finally {
            restore_error_handler();
        }

        $this->assertSame([false, false, false, false], $kinds);
    }
}
