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

use Kunena\Forum\Libraries\Tools\KunenaCategoryTransfer;
use Kunena\Forum\Libraries\Tools\KunenaSettingsSnapshot;
use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The Cert ID of eBay (its client secret, the password of the requests of Kunena) stays on its site, as the other keys: the
 * settings snapshot showed it and put it in the file, the history of the settings kept it in clear, the export of the categories
 * with the configuration put it in the file, and an import put the one of the other site on this one.
 */
class KunenaSettingsEbayCertIdTest extends UnitTestCase
{
    protected function setUp(): void
    {
        require_once Files::path('src/libraries/kunena/src/Tools/KunenaSettingsSnapshot.php');
        require_once Files::path('src/libraries/kunena/src/Tools/KunenaCategoryTransfer.php');
    }

    public function testTheSnapshotAndTheHistoryKeepItOnTheSite(): void
    {
        $this->assertTrue(KunenaSettingsSnapshot::isPrivate('ebayCertId'));
        $this->assertFalse(KunenaSettingsSnapshot::isPrivate('ebayAffiliateId'), 'not the id of the affiliate, which is no secret');
    }

    public function testTheExportOfTheCategoriesLeavesItOut(): void
    {
        $this->assertSame(['boardTitle' => 'Forum'], KunenaCategoryTransfer::filterConfig(['ebayCertId' => 'x', 'boardTitle' => 'Forum']));
        $this->assertSame([], KunenaCategoryTransfer::configChanges(['ebayCertId' => 'mine'], ['ebayCertId' => 'theirs']));
    }
}
