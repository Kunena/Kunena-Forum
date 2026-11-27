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

namespace Kunena\Tests\Unit\Component\Site\Layout\User;

use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The tab of the settings of a profile is shown to the user himself and to an administrator, who are the ones whose change is saved
 * (UserController::saveSettings()). A moderator who edited another member saw it, changed a setting, and got "saved" for a change
 * which was not kept. (The tabs need a whole site: the code is read.)
 */
class UserEditSettingsTabTest extends UnitTestCase
{
    public function testTheSettingsAreForTheUserAndTheAdministrators(): void
    {
        $layout = str_replace("\r\n", "\n", Files::read('src/site/src/Layout/User/UserEdit.php'));

        $this->assertMatchesRegularExpression(
            "/if \(\\\$this->profile->isMyself\(\) \|\| KunenaUserHelper::getMyself\(\)->isAdmin\(\)\) \{\n\s*\\\$tab\s*= new stdClass\(\);\n\s*\\\$tab->title\s*= Text::_\('COM_KUNENA_PROFILE_EDIT_SETTINGS'\);/",
            $layout
        );

        // The rule of the save, which the tab follows.
        $controller = Files::read('src/site/src/Controllers/UserController.php');

        $this->assertStringContainsString('if ((int) $this->user->userid !== (int) $me->userid && !$me->isAdmin()) {', $controller);
    }
}
