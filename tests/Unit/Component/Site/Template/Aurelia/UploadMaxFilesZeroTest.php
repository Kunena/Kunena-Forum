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

namespace Kunena\Tests\Unit\Component\Site\Template\Aurelia;

use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * "Limit Attachments" of 0 is no limit in the uploader of the editor, as on the server (the save of the message and the upload
 * count the files only for a limit above 0): the uploader said "too many files" for every file, and nothing could be attached.
 * (The script needs a browser: its code is read.)
 */
class UploadMaxFilesZeroTest extends UnitTestCase
{
    public function testALimitOfZeroIsNoLimit(): void
    {
        $script = Files::read('src/media/kunena/core/js/upload.main.js');

        $this->assertStringContainsString("const maxFiles = Number(Joomla.getOptions('com_kunena.kunena_upload_files_maxfiles')) || 0;", $script);
        $this->assertStringContainsString('if (maxFiles > 0 && fileCountTotal > maxFiles) {', $script);
        $this->assertStringNotContainsString("if (fileCountTotal > Joomla.getOptions('com_kunena.kunena_upload_files_maxfiles')) {", $script);
    }
}
