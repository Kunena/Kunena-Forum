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

use Kunena\Forum\Libraries\Tools\KunenaUserExport as E;
use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The tool "User data export" turns the domains of the privacy plugin of Kunena into plain data, and names the files of
 * the ZIP without a folder of their own.
 */
class KunenaUserExportTest extends UnitTestCase
{
    public function testTheDomainsBecomePlainData(): void
    {
        $field = static fn (string $name, $value) => (object) ['name' => $name, 'value' => $value];
        $item  = new class ([$field('id', 5), $field('subject', 'Hello')]) {
            public function __construct(private array $fields)
            {
            }

            public function getFields(): array
            {
                return $this->fields;
            }
        };
        $domain = new class ($item) {
            public string $name = 'kunena_user_messages';

            public function __construct(private object $item)
            {
            }

            public function getItems(): array
            {
                return [$this->item];
            }
        };

        $this->assertSame(['kunena_user_messages' => [['id' => 5, 'subject' => 'Hello']]], E::domainsToArray([$domain]));
    }

    public function testTheNamesOfTheFilesOfTheZip(): void
    {
        $this->assertSame('attachments/7-photo.jpg', E::entryName(7, 'photo.jpg'));
        $this->assertSame('attachments/7-passwd', E::entryName(7, '../../etc/passwd'));
        $this->assertSame('attachments/7-a_b.txt', E::entryName(7, 'a:b.txt'));
        $this->assertSame('attachments/7-file', E::entryName(7, '..'));
    }

    /**
     * The data is the one of the privacy plugin: the plugin answers the export requests of Joomla.
     */
    public function testTheExportUsesThePrivacyPlugin(): void
    {
        $this->assertStringContainsString("bootPlugin('kunena', 'privacy')", Files::read('src/libraries/kunena/src/Tools/KunenaUserExport.php'));
        $this->assertStringContainsString('public function onPrivacyExportRequest', Files::read('src/plugins/plg_privacy_kunena/src/Extension/Kunena.php'));
    }
}
