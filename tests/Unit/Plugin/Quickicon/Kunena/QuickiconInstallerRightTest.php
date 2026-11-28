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

namespace Kunena\Tests\Unit\Plugin\Quickicon\Kunena;

use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The quick icon of Kunena for a manager of the forum who may not manage the installer is the icon of the forum, without a state of
 * its updates. It was the red "unsupported" icon (the state of the updates was null, which means an unsupported version). (The icon
 * needs a whole backend: its code is read.)
 */
class QuickiconInstallerRightTest extends UnitTestCase
{
    public function testAManagerWithoutTheInstallerGetsTheIconOfTheForum(): void
    {
        $source = Files::read('src/plugins/plg_quickicon_kunena/src/Extension/Kunena.php');

        $check = strpos($source, "if (KunenaForum::installed() && !\$app->getIdentity()->authorise('core.manage', 'com_installer')) {");

        $this->assertNotFalse($check);
        $this->assertStringContainsString("\$updateInfo = (object) ['version' => 0, 'addons' => 0];", substr($source, $check, 200));
        $this->assertLessThan(strpos($source, '} elseif ($updateInfo === null) {'), $check);
    }
}
