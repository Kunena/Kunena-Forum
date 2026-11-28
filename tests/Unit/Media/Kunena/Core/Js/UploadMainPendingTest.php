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

namespace Kunena\Tests\Unit\Media\Kunena\Core\Js;

use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The form of a post is sent only when no upload is running any more: the first finished upload of several enabled the button, and
 * the files still on their way were not in the post. A refusal without a message enables it too (it stayed disabled for good).
 * (The script runs in a browser: its code is read.)
 */
class UploadMainPendingTest extends UnitTestCase
{
    public function testTheFormWaitsForEveryUpload(): void
    {
        $script = str_replace("\r\n", "\n", Files::read('src/media/kunena/core/js/upload.main.js'));

        $this->assertStringContainsString('pending += files.length;', $script);
        $this->assertStringContainsString('const last = finished();', $script);
        $this->assertSame(2, substr_count($script, 'setSubmit(last);'));
        $this->assertStringContainsString('setSubmit(finished());', $script);

        // No upload enables the form on its own any more.
        $this->assertSame(1, substr_count($script, 'setSubmit(true);'), 'only the refusal of too many files, which uploads nothing');
    }
}
