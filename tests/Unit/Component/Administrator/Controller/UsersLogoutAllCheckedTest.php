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
 * The Logout button of the users list takes the checked users: only the first one was logged out (array_shift()), the others kept
 * their sessions, and the message said that it was done. (The task needs a whole site: its code is read.)
 */
class UsersLogoutAllCheckedTest extends UnitTestCase
{
    public function testEveryCheckedUserIsLoggedOut(): void
    {
        $source = str_replace("\r\n", "\n", Files::read('src/admin/src/Controller/UsersController.php'));

        $this->assertSame(1, preg_match('/    public function logout\(\): void\n.*?\n    \}\n/s', $source, $m));
        $body = $m[0];

        $this->assertStringNotContainsString('array_shift($cid)', $body);
        $this->assertMatchesRegularExpression('/foreach \(array_unique\(\$cid\) as \$id\) \{.*?\$this->app->logout\(\(int\) \$id, /s', $body);

        // A refused or unknown user is skipped, the others are still logged out.
        $loop = substr($body, (int) strpos($body, 'foreach ('), (int) strpos($body, '$done++;') - (int) strpos($body, 'foreach ('));
        $this->assertStringNotContainsString('return;', $loop);
    }
}
