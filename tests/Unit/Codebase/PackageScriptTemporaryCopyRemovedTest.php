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

namespace Kunena\Tests\Unit\Codebase;

use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The installer script of the package keeps custom.scss of the templates in the folder for temporary files during an update and
 * puts it back afterwards. The copy stayed in that folder: a later update put it back over the template after the administrator
 * had deleted his custom.scss (no new copy was made then, and the old one was still there).
 */
class PackageScriptTemporaryCopyRemovedTest extends UnitTestCase
{
    public function testTheTemporaryCopyIsDeletedAfterItIsPutBack(): void
    {
        $source = Files::read('src/script.php');

        $back   = strpos($source, 'File::copy($saved, JPATH_SITE . "/components/com_kunena/template/{$template}/assets/scss/custom.scss");');
        $delete = strpos($source, 'File::delete($saved);');

        $this->assertIsInt($back);
        $this->assertIsInt($delete, 'The copy is deleted.');
        $this->assertGreaterThan($back, $delete, 'After it was put back.');
        $this->assertLessThan(600, $delete - $back, 'In the same block: only a copy which was put back.');
    }
}
