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

namespace Kunena\Tests\Unit\Plugin\System\Kunena;

use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * "Check New User" (StopForumSpam) checks the registrations of the site only: a user created by an administrator (the backend, the
 * web services, the command line) was checked with the address of the administrator, so an administrator on a listed address could
 * create no user, and a user whom he chose to create was refused for his e-mail. (The event asks the service on the internet: its
 * code is read.)
 */
class StopForumSpamNewUserSiteOnlyTest extends UnitTestCase
{
    public function testOnlyTheRegistrationsOfTheSiteAreChecked(): void
    {
        $source = str_replace("\r\n", "\n", Files::read('src/plugins/plg_system_kunena/src/Extension/Kunena.php'));
        $start  = strpos($source, '    public function onUserBeforeSave(');
        $body   = substr($source, $start, (int) strpos($source, "\n    }\n", $start) - $start);
        $check  = strpos($body, "if (!\$this->getApplication()->isClient('site')) {\n            return;");

        $this->assertNotFalse($check);
        $this->assertLessThan(strpos($body, '$query[\'ip\']       = IpHelper::getIp();'), $check);
        $this->assertLessThan(strpos($body, '$http->get('), $check);
    }
}
