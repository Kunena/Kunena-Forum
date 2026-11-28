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

namespace Kunena\Tests\Unit\Component\Administrator\Controller;

use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * Removing a user in the backend deletes his Joomla account, and with it his data of the forum (the system plugin, on
 * onUserAfterDelete). The profile of the forum was deleted before: when the delete of the account then failed, the account stayed
 * without its profile, and his ban (a column of the profile) was lifted. (The task needs a whole site: its code is read.)
 */
class UsersRemoveAccountFirstTest extends UnitTestCase
{
    public function testTheProfileIsNotDeletedBeforeTheAccount(): void
    {
        $source = str_replace("\r\n", "\n", Files::read('src/admin/src/Controller/UsersController.php'));

        $this->assertSame(1, preg_match('/public function remove\(\): void\s*\{(.*?)\n    \}\n/s', $source, $m));
        $this->assertStringNotContainsString('$result = $user->delete();', $m[1]);
        $this->assertStringContainsString('$jresult = $instance->delete();', $m[1]);

        // The system plugin deletes the profile with the account.
        $this->assertStringContainsString("'#__kunena_users'            => ['userid'],", Files::read('src/plugins/plg_system_kunena/src/Extension/Kunena.php'));
    }
}
