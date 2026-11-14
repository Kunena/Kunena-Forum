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

use Com_KunenaInstallerScript;
use Error;
use Exception;
use Kunena\Forum\Libraries\Install\KunenaModelInstall;
use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use SimpleXMLElement;
use Throwable;

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

    /**
     * @return  array<string,array{0:string,1:string,2:bool}>
     */
    public static function developmentBlocks(): array
    {
        return [
            'an earlier pre-release of the version in development'   => ['8.0.0-ALPHA1-DEV', '8.0.0-DEV', true],
            'a pre-release with a beta'                              => ['8.0.0-BETA2', '8.0.0-DEV', true],
            'the released version does not run it again'             => ['8.0.0', '8.0.0-DEV', false],
            'a later version does not run it again'                  => ['8.1.0', '8.0.0-DEV', false],
            'a release candidate of the version before'              => ['7.1.0-RC1', '7.0.0-DEV', false],
            'the block of a version which is not in development'     => ['8.0.0-ALPHA1-DEV', '5.2.0-ALPHA1', false],
            'the placeholder block of a git checkout is not matched' => ['7.99.0', '@kunenaversion@', false],
        ];
    }

    #[DataProvider('developmentBlocks')]
    public function testTheBlockOfAVersionInDevelopmentRunsAgainUntilThatVersionIsReleased(string $installed, string $block, bool $runs): void
    {
        require_once Files::path('src/admin/script.php');

        $script = self::bare(Com_KunenaInstallerScript::class, ['installedVersion' => $installed]);

        $this->assertSame($runs, self::invoke($script, 'isOpenDevelopmentBlock', $block));
    }

    /**
     * @return  array<string,array{0:string,1:string,2:bool}>
     */
    public static function upgradeBlocks(): array
    {
        return [
            'a release candidate of 7.1'                 => ['7.1.0-RC1', '8.0.0-DEV', true],
            'a development site of 7.1'                  => ['7.1.0-DEV', '8.0.0-DEV', true],
            'a development site of 7.2'                  => ['7.2.0-DEV', '8.0.0-DEV', true],
            'the version assumed for a development site' => ['7.99.0', '8.0.0-DEV', true],
            'an earlier pre-release of 8.0'              => ['8.0.0-ALPHA1-DEV', '8.0.0-DEV', true],
            'the released version 8.0'                   => ['8.0.0', '8.0.0-DEV', false],
            'a later version'                            => ['8.1.0', '8.0.0-DEV', false],
            'the placeholder block of a git checkout'    => ['8.1.0', '@kunenaversion@', true],
        ];
    }

    #[DataProvider('upgradeBlocks')]
    public function testTheBlocksOfTheUpgradeRunForTheInstalledVersion(string $installed, string $block, bool $runs): void
    {
        require_once Files::path('src/admin/script.php');

        $script = self::bare(Com_KunenaInstallerScript::class, ['installedVersion' => $installed]);

        $this->assertSame($runs, self::invoke($script, 'runsUpgradeBlock', $block));
    }

    /**
     * @return  array<string,array{0:Throwable}>
     */
    public static function failures(): array
    {
        return [
            'an exception' => [new Exception('The folder of the attachments cannot be read')],
            'a PHP error'  => [new Error('Call to a member function on null')],
        ];
    }

    /**
     * A step which throws (the private attachments of 8.0.0-DEV) does not skip the steps after it, such as the new columns.
     */
    #[DataProvider('failures')]
    public function testAFailingStepDoesNotSkipTheStepsAfterIt(Throwable $failure): void
    {
        $this->requireJoomla();
        require_once Files::path('src/admin/script.php');

        $xml     = simplexml_load_file(Files::path('src/admin/install/kunena.install.upgrade.xml'));
        $actions = $xml->xpath('//version[@version="8.0.0-DEV"]')[0];
        $run     = [];

        $model = $this->createMock(KunenaModelInstall::class);
        $model->expects($this->exactly(\count($actions->children())))->method('processUpgradeXMLNode')->willReturnCallback(
            static function (SimpleXMLElement $action) use (&$run, $failure): ?array {
                if ($action->getName() === 'phpfile' && (string) $action['name'] === '8.0.0-2026-10-27_hide_private_attachments') {
                    throw $failure;
                }

                $run[] = (string) $action;

                return null;
            }
        );

        $script   = self::bare(Com_KunenaInstallerScript::class, ['installedVersion' => '7.99.0']);
        $failures = self::invoke($script, 'runUpgradeActions', $actions, $model);

        $this->assertCount(1, $failures);
        $this->assertSame('8.0.0-2026-10-27_hide_private_attachments.php', $failures[0]['step']);
        $this->assertSame($failure->getMessage(), $failures[0]['reason']);

        $this->assertCount(\count($actions->children()) - 1, $run);
        $this->assertContains("ALTER TABLE `#__kunena_messages` ADD `scheduled_time` int(11) NOT NULL default '0' AFTER `modified_reason`", $run);
        $this->assertContains("ALTER TABLE `#__kunena_messages` ADD `scheduled_review` tinyint(4) NOT NULL default '0' AFTER `scheduled_time`", $run);
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
