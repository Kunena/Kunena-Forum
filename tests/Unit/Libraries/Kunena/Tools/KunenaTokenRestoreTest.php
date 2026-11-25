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

namespace Kunena\Tests\Unit\Libraries\Kunena\Tools;

use FilesystemIterator;
use Kunena\Forum\Libraries\Tools\KunenaTokenRestore as R;
use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * The tool "Restore the build tokens" puts back the tokens which an installed package replaced in a linked git clone, and
 * leaves a file with its tokens alone.
 */
class KunenaTokenRestoreTest extends UnitTestCase
{
    private string $dir = '';

    protected function tearDown(): void
    {
        if ($this->dir !== '' && is_dir($this->dir)) {
            $items = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this->dir, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST);

            foreach ($items as $item) {
                $item->isDir() ? @rmdir($item->getPathname()) : @unlink($item->getPathname());
            }

            @rmdir($this->dir);
        }

        parent::tearDown();
    }

    public function testTheCopyrightYearBecomesTheTokenAgain(): void
    {
        $this->assertSame(
            " * @copyright Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.\n (C) 2023 - @currentyear@ Kunena Team",
            R::restore(" * @copyright Copyright (C) 2008 - 2026 Kunena Team. All rights reserved.\n (C) 2023 - 2026 Kunena Team", 'php')
        );
    }

    public function testTheTagsOfAManifestGetTheirTokens(): void
    {
        $built = "<version>8.0.0-ALPHA1-DEV</version>\n<creationDate>2026-11-25</creationDate>\n<build>8.0.0</build>\n"
            . "<changelogurl>https://update.kunena.org/changelog/8.0.0.xml</changelogurl>\n<versionname>INTERNAL</versionname>\n"
            . "<copyright>(C) 2008 - 2026 Kunena Team. All rights reserved.</copyright>\n<file id=\"kunena\">lib_kunena_v8.0.0-ALPHA1-DEV.zip</file>";

        $this->assertSame(
            "<version>@kunenaversion@</version>\n<creationDate>@kunenaversiondate@</creationDate>\n<build>@kunenaversionbuild@</build>\n"
            . "<changelogurl>@kunenachangelog@</changelogurl>\n<versionname>@kunenaversionname@</versionname>\n"
            . "<copyright>(C) 2008 - @currentyear@ Kunena Team. All rights reserved.</copyright>\n<file id=\"kunena\">lib_kunena_v@kunenaversion@.zip</file>",
            R::restore($built, 'xml')
        );
    }

    /**
     * The checkout as it is in git has nothing to restore: no file of src/ is changed (the history of the releases in the
     * upgrade file, this class itself...).
     */
    public function testTheCleanCheckoutHasNothingToRestore(): void
    {
        $this->assertSame([], array_diff_key(R::find([Files::path('src')]), array_flip([str_replace('\\', '/', Files::path('src/pkg_kunena.xml'))])));
    }

    public function testTheTagsOfAManifestAreNotChangedInAnotherFile(): void
    {
        $this->assertSame('$a = "<version>1.0</version>";', R::restore('$a = "<version>1.0</version>";', 'php'));
    }

    public function testThePhpComparisonsGetTheirTokens(): void
    {
        $this->assertSame(
            "self::\$version_name = ('@kunenaversionname@' == '@' . 'kunenaversionname' . '@') ? 'Git Repository' : '@kunenaversionname@';\n"
            . "if (\$data->version == '@kunenaversion@') {\nif (\$data->creationdate == '@kunenaversiondate@') {",
            R::restore(
                "self::\$version_name = ('INTERNAL' == '@' . 'kunenaversionname' . '@') ? 'Git Repository' : 'INTERNAL';\n"
                . "if (\$data->version == '8.0.0-ALPHA1-DEV') {\nif (\$data->creationdate == '2026-11-25') {",
                'php'
            )
        );
    }

    public function testAFileWithItsTokensIsNotChanged(): void
    {
        foreach (['src/pkg_kunena.xml' => 'xml', 'src/libraries/kunena/src/Forum/KunenaForum.php' => 'php', 'src/libraries/kunena/src/Template/KunenaTemplateHelper.php' => 'php', 'src/admin/kunena.xml' => 'xml'] as $file => $extension) {
            $content = Files::read($file);

            // The package manifest of the checkout has the version itself: the only literal one, and it is not reached through a link.
            if ($file === 'src/pkg_kunena.xml') {
                $content = preg_replace('#<version>[^<]+</version>#', '<version>@kunenaversion@</version>', $content);
                $content = preg_replace('#<versionname>[^<]+</versionname>#', '<versionname>@kunenaversionname@</versionname>', $content);
            }

            $this->assertSame($content, R::restore($content, $extension), $file);
        }
    }

    public function testFindAndApplyWorkOnTheFilesOfTheFolders(): void
    {
        $this->dir = sys_get_temp_dir() . '/kunena-tokens-' . bin2hex(random_bytes(4));
        @mkdir($this->dir . '/src', 0777, true);
        @mkdir($this->dir . '/vendor', 0777, true);
        file_put_contents($this->dir . '/src/a.php', "<?php\n// Copyright (C) 2008 - 2026 Kunena Team\n");
        file_put_contents($this->dir . '/src/b.php', "<?php\n// Copyright (C) 2008 - @currentyear@ Kunena Team\n");
        file_put_contents($this->dir . '/src/c.png', 'Copyright (C) 2008 - 2026 Kunena Team');
        file_put_contents($this->dir . '/vendor/d.php', '// Copyright (C) 2008 - 2026 Kunena Team');

        $this->assertSame([$this->norm($this->dir . '/src/a.php') => 1], R::find([$this->dir]));
        $this->assertSame(['restored' => 1, 'failed' => 0], R::apply([$this->dir]));
        $this->assertStringContainsString('@currentyear@', file_get_contents($this->dir . '/src/a.php'));
        $this->assertSame([], R::find([$this->dir]));
    }

    private function norm(string $path): string
    {
        return str_replace('\\', '/', $path);
    }
}
