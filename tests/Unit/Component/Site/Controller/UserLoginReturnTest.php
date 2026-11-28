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

namespace Kunena\Tests\Unit\Component\Site\Controller;

use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The login of the forum goes to the (internal) return address after a good login: the login of Joomla gives true when the user is
 * logged in (the one of Community Builder gives no error), and the address was followed only when the result was empty, so after a
 * failed login of Joomla and never after a good one. (The task needs a whole site: its code is read.)
 */
class UserLoginReturnTest extends UnitTestCase
{
    public function testTheReturnAddressFollowsAGoodLogin(): void
    {
        $controller = Files::read('src/site/src/Controllers/UserController.php');

        $this->assertStringContainsString('$loggedIn = $error === true || $error === null;', $controller);
        $this->assertStringContainsString('if ($loggedIn && $return && KunenaRoute::isInternal($return)) {', $controller);

        // The two logins, as the controller reads them.
        $this->assertStringContainsString('return $logged;', Files::read('src/plugins/plg_kunena_joomla/src/Helper/KunenaLoginJoomla.php'));
        $this->assertStringContainsString('return $resultError ? $resultError : null;', Files::read('src/plugins/plg_kunena_comprofiler/src/Helper/KunenaLoginComprofiler.php'));
    }
}
