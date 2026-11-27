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

namespace Kunena\Tests\Unit\Plugin\Kunena\Comprofiler;

use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The plugin of Community Builder refuses a CB older than the version it needs. The comparison was the other way round: a newer CB
 * was refused (a warning, and the integration did nothing) and an older one was taken. (The plugin needs Community Builder: its code
 * is read.)
 */
class ComprofilerVersionTest extends UnitTestCase
{
    public function testAnOlderCommunityBuilderIsRefused(): void
    {
        $source = Files::read('src/plugins/plg_kunena_comprofiler/src/Extension/Comprofiler.php');

        $this->assertStringContainsString("if (version_compare(CBLib::version(), \$this->minCBVersion, '<')) {", $source);
        $this->assertStringNotContainsString("version_compare(\$this->minCBVersion, CBLib::version(), '<')", $source);
    }
}
