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

namespace Kunena\Tests\Unit\Component\Site\Controller;

use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The name of a file uploaded with the editor is kept as the upload without JavaScript keeps it (getClientFilename(): no folder, no
 * control character, at most 190 characters, the length of its column). The name of the request was stored as it came: with a
 * strict database a longer name was an error, and the upload failed. (The upload needs a posted file: its code is read.)
 */
class TopicUploadClientNameTest extends UnitTestCase
{
    public function testTheNameOfTheEditorIsCleanedAsTheOtherUpload(): void
    {
        $source = Files::read('src/site/src/Controllers/TopicController.php');

        $this->assertStringContainsString("'filename_real' => KunenaAttachmentHelper::getClientFilename(\$response->filename),", $source);
        $this->assertStringNotContainsString("'filename_real' => \$response->filename,", $source);
    }
}
