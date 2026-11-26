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
 * The upgrade gives the messages of the trash which have no time of their delete the time of the upgrade. The column came with
 * Kunena 7.0 and is 0 for what was in the trash before; the task which empties the trash (enabled by default, every hour) takes
 * the messages whose time is older than its age, so it removed the whole trash of an upgraded forum for good in its next runs.
 */
class UpgradeTrashTimeTest extends UnitTestCase
{
    private const QUERY = 'UPDATE `#__kunena_messages` SET `deleted_time` = UNIX_TIMESTAMP() WHERE `hold` IN (2, 3) AND `deleted_time` = 0';

    public function testTheUpdateOfJoomlaStampsTheTrash(): void
    {
        $this->assertStringContainsString(self::QUERY . ';', Files::read('src/admin/sql/updates/mysql/8.0.0-DEV.sql'));
    }

    public function testTheUpgradeOfKunenaStampsTheTrash(): void
    {
        $xml = simplexml_load_string(Files::read('src/admin/install/kunena.install.upgrade.xml'));

        $queries = array_map('strval', $xml->xpath('//version[@version="8.0.0-DEV"]/query'));

        $this->assertContains(self::QUERY, $queries);
    }
}
