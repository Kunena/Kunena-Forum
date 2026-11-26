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
 * The upgrade to Kunena 8.0 removes the private posts whose post of the forum is gone (a post deleted for good left its private
 * post, with its text, behind), with their recipients and links: the same queries in the update of Joomla and in the upgrade of
 * Kunena. The queries themselves are run by the integration test of the diagnostics.
 */
class UpgradePrivateOrphansTest extends UnitTestCase
{
    public function testTheUpgradeOfKunenaHasTheQueriesOfTheUpdateOfJoomla(): void
    {
        $sql = array_values(array_filter(
            array_map(static fn (string $line): string => rtrim(trim($line), ';'), preg_split('/\R/', Files::read('src/admin/sql/updates/mysql/8.0.0-DEV.sql'))),
            static fn (string $line): bool => str_starts_with($line, 'DELETE a FROM `#__kunena_private')
        ));

        $xml     = simplexml_load_string(Files::read('src/admin/install/kunena.install.upgrade.xml'));
        $upgrade = array_values(array_filter(
            array_map('strval', $xml->xpath('//version[@version="8.0.0-DEV"]/query')),
            static fn (string $query): bool => str_starts_with($query, 'DELETE a FROM `#__kunena_private')
        ));

        $this->assertCount(4, $sql);
        $this->assertSame($sql, $upgrade);
    }
}
