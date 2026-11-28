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
 * The button "Remove avatar" of the upload of an avatar is shown for an avatar which was saved only. The task removes the avatar of
 * the user, whatever file is named, so the button of a refused upload (a file too large) deleted his current avatar, which the list
 * no longer showed. (The script runs in a browser: its code is read.)
 */
class UploadAvatarRemoveTest extends UnitTestCase
{
    public function testARefusedUploadHasNoRemoveButton(): void
    {
        $script = str_replace("\r\n", "\n", Files::read('src/media/kunena/core/js/upload.avatar.js'));

        $this->assertMatchesRegularExpression("/if \(result\.success === true\) \{\n\s*context\.append\(removeButton\(userId\(\), file\.name\)\);\n\s*\}/", $script);
    }
}
