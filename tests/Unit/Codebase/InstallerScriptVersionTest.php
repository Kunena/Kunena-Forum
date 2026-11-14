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
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * The installer script decides from the installed version which updates of the database run. A version which is not a version
 * (the placeholder of a git checkout, or nothing) is older than every version for version_compare(): the whole history of the
 * upgrade then ran on a current database and stopped at the first step which uses a column that is renamed since.
 */
class InstallerScriptVersionTest extends UnitTestCase
{
    /**
     * @return  array<string,array{0:string}>
     */
    public static function notAVersion(): array
    {
        return [
            'the placeholder of the source' => ['@kunenaversion@'],
            'nothing'                       => [''],
        ];
    }

    #[DataProvider('notAVersion')]
    public function testAnUnreadableVersionLooksOlderThanTheWholeHistoryOfTheUpgrade(string $installed): void
    {
        $this->assertTrue(version_compare($installed, '7.0.0', '<'));
        $this->assertTrue(version_compare('5.2.0-alpha1', strtolower($installed), '>'));
    }

    public function testTheInstallerScriptDoesNotTrustAnUnreadableVersion(): void
    {
        $script = Files::read('src/admin/script.php');

        $this->assertStringContainsString('$this->installedVersion = $this->readInstalledVersion();', $script);
        $this->assertStringContainsString("preg_match('/^\\d+\\.\\d+/'", $script);
        $this->assertStringContainsString("'#__kunena_version'", $script);

        // The placeholder of a development site is ignored by name, written in two parts so that the build keeps it.
        $this->assertStringContainsString("'@' . 'kunenaversion' . '@'", $script);

        // The version which is assumed in the end is newer than the released ones and older than the one in development.
        $this->assertStringContainsString("DEVELOPMENT_VERSION = '7.99.0';", $script);
        $this->assertTrue(version_compare('7.99.0', '7.1.0', '>'));
        $this->assertTrue(version_compare('8.0.0-dev', '7.99.0', '>'));
    }
}
