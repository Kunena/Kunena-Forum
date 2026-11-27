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

use Joomla\Input\Input;
use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The new password of the profile is stored as it was typed and checked. The rules were checked on the raw password, but the one
 * given to the account came from getArray(), which filters each value as a text: "Secret<b>Word42!" was stored as
 * "SecretWord42!", and the user was locked out with the password he had just chosen. (The task needs a whole site: the filter is
 * shown with the input of Joomla, the controller is read.)
 */
class UserSavePasswordRawTest extends UnitTestCase
{
    public function testTheCheckedPasswordIsTheOneWhichIsStored(): void
    {
        $this->requireJoomla();

        // What getArray() makes of a password with a tag or an entity.
        $post = (new Input(['password' => 'Secret<b>Word42!', 'password2' => 'a&amp;b']))->getArray();

        $this->assertSame('SecretWord42!', $post['password']);
        $this->assertSame('a&b', $post['password2']);

        $source = str_replace("\r\n", "\n", Files::read('src/site/src/Controllers/UserController.php'));

        $this->assertSame(1, preg_match('/protected function saveUser\(\)[^{]*\{(.*?)\n    \}\n/s', $source, $m));

        $raw  = strpos($m[1], "\$post['password']  = \$post_password;");
        $bind = strpos($m[1], '$user->bind($post);');

        $this->assertNotFalse($raw, 'The raw password replaces the filtered one.');
        $this->assertStringContainsString("\$post['password2'] = \$post_password2;", $m[1]);
        $this->assertLessThan($bind, $raw);
    }
}
