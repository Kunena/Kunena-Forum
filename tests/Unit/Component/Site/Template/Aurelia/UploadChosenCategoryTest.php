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
 * The uploader sends the category chosen in the form of a new topic (the select "postcatid"): it sent the category in which the form
 * opened (the hidden field "kunena_upload", which a change of the category does not change), so the rights of the upload were those
 * of that category, and a file was refused in a category which allows it. (The script needs a browser: its code is read.)
 */
class UploadChosenCategoryTest extends UnitTestCase
{
    public function testTheUploadHasTheChosenCategory(): void
    {
        $script = Files::read('src/media/kunena/core/js/upload.main.js');

        $this->assertStringContainsString("catid: byId('postcatid')?.value || byId('kunena_upload')?.value || '',", $script);
        $this->assertStringContainsString("'postcatid'", Files::read('src/site/src/Controller/Topic/Form/Create/TopicFormCreateDisplay.php'));
    }
}
