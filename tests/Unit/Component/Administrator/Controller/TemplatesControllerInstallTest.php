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

namespace Kunena\Tests\Unit\Component\Administrator\Controller;

use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The install of a template:
 * - the files of the package went to the folder kinstall, the same for every install, which stayed after a failed one (no
 *   template in the package, an archive which could not be read): the next package with its template at the root moved the
 *   files of the old one into the folder of the templates, which the web server shows;
 * - a locked template (Aurelia, Agora) was compared with the case of the folder of the package: on Windows and macOS "Agora" is
 *   the folder agora, which was deleted and replaced.
 * (The task needs the backend: its code is read.)
 */
class TemplatesControllerInstallTest extends UnitTestCase
{
    private function install(): string
    {
        $source = str_replace("\r\n", "\n", Files::read('src/admin/src/Controller/TemplatesController.php'));

        $this->assertSame(1, preg_match('/public function install\(\): bool\s*\{(.*?)\n    \}\n/s', $source, $m));

        return $m[1];
    }

    public function testThePackageHasAFolderOfItsOwnWhichIsAlwaysRemoved(): void
    {
        $install = $this->install();

        $this->assertStringContainsString("\$tmpKunena = KunenaPath::tmpdir() . '/kinstall-' . bin2hex(random_bytes(8)) . '/';", $install);
        $this->assertStringNotContainsString("'/kinstall/'", $install);

        // Removed after the last branch, before the redirect.
        $last = strrpos($install, "if (is_dir(\$tmpKunena)) {\n            Folder::delete(\$tmpKunena);\n        }");
        $this->assertNotFalse($last);
        $this->assertLessThan(strrpos($install, '$this->setRedirect('), $last);
    }

    public function testALockedTemplateIsFoundWhateverItsCase(): void
    {
        $this->assertStringContainsString(
            "\\in_array(strtolower((string) \$template->directory), array_map('strtolower', \$this->locked), true)",
            $this->install()
        );
    }
}
