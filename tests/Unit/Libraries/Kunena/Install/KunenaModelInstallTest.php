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

namespace Kunena\Tests\Unit\Libraries\Kunena\Install;

use Joomla\Database\DatabaseDriver;
use Kunena\Forum\Libraries\Install\KunenaModelInstall;
use Kunena\Tests\Unit\UnitTestCase;
use RuntimeException;
use SimpleXMLElement;

/**
 * A query of the upgrade file which fails is reported, unless it is marked silenterror: before, it was only reported when the
 * failed query had changed rows, which a failed query never has.
 */
class KunenaModelInstallTest extends UnitTestCase
{
    private function model(): KunenaModelInstall
    {
        $this->requireJoomla();

        $db = $this->createStub(DatabaseDriver::class);
        $db->method('execute')->willThrowException(new RuntimeException('Unknown column'));
        $db->method('getAffectedRows')->willReturn(0);

        return self::bare(KunenaModelInstall::class, ['db' => $db]);
    }

    public function testAFailingQueryIsReported(): void
    {
        $query  = 'ALTER TABLE `#__kunena_messages` ADD `scheduled_time` int(11) NOT NULL default \'0\'';
        $result = $this->model()->processUpgradeXMLNode(new SimpleXMLElement('<query>' . $query . '</query>'));

        $this->assertIsArray($result);
        $this->assertFalse($result['success']);
        $this->assertSame('SQL Query: ' . $query, $result['action']);
    }

    public function testAFailingSilentQueryIsNotReported(): void
    {
        $action = new SimpleXMLElement('<query mode="silenterror">ALTER TABLE `#__kunena_messages` ADD `scheduled_time` int(11)</query>');

        $this->assertNull($this->model()->processUpgradeXMLNode($action));
    }
}
