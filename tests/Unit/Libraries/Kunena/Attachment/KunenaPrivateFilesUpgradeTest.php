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

namespace Kunena\Tests\Unit\Libraries\Kunena\Attachment;

use Kunena\Tests\Unit\UnitTestCase;

/**
 * The files of the private attachments of an upgraded site get a name which nobody can guess: the upgrade file of the installer
 * of Kunena runs the step, and the step has the name of function which the installer calls.
 */
class KunenaPrivateFilesUpgradeTest extends UnitTestCase
{
    private const string STEP = '8.0.0-2026-10-27_hide_private_attachments';

    private function root(): string
    {
        return \dirname(__DIR__, 5) . '/src/admin/install/';
    }

    public function testTheUpgradeFileRunsTheStepIn800(): void
    {
        $xml   = simplexml_load_file($this->root() . 'kunena.install.upgrade.xml');
        $steps = [];

        foreach ($xml->upgrade[0]->version as $version) {
            if ((string) $version['version'] === '8.0.0-DEV') {
                foreach ($version->phpfile as $file) {
                    $steps[] = (string) $file['name'];
                }
            }
        }

        $this->assertContains(self::STEP, $steps);
    }

    public function testTheStepHasTheFunctionWhichTheInstallerCalls(): void
    {
        // The installer calls 'kunena_' + the name of the file without dots and with underscores for the dashes.
        $function = 'kunena_' . strtr(self::STEP, ['.' => '', '-' => '_']);
        $code     = (string) file_get_contents($this->root() . 'sql/updates/php/' . self::STEP . '.php');

        $this->assertStringContainsString("function {$function}(", $code);
        $this->assertStringContainsString('KunenaAttachmentHelper::hidePrivateFiles()', $code);
    }
}
