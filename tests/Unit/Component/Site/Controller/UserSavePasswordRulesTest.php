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
 * The rules of a new password on the profile are the ones of Joomla, with its messages. The messages were keys of com_users which
 * Joomla 6 does not have (the user read "COM_USERS_MSG_NOT_ENOUGH_INTEGERS_N"), and the option "Minimum Lower Case" was not
 * checked. (The task needs a whole site: its code is read, and the language file of the Joomla of the tests.)
 */
class UserSavePasswordRulesTest extends UnitTestCase
{
    private function saveUser(): string
    {
        $source = str_replace("\r\n", "\n", Files::read('src/site/src/Controllers/UserController.php'));

        $this->assertSame(1, preg_match('/protected function saveUser\(\)[^{]*\{(.*?)\n    \}\n/s', $source, $m));

        return $m[1];
    }

    public function testEveryMessageOfThePasswordRulesIsAKeyOfJoomla(): void
    {
        $this->requireJoomla();

        $joomla = (string) file_get_contents(JPATH_ROOT . '/language/en-GB/joomla.ini');

        preg_match_all("/Text::(?:_|plural)\\('([A-Z_]+)'/", $this->saveUser(), $keys);

        // The keys of Joomla (the keys of Kunena are in its own files).
        $passwordKeys = array_filter($keys[1], static fn ($key) => str_contains($key, 'PASSWORD') && !str_starts_with($key, 'COM_KUNENA_'));

        $this->assertNotEmpty($passwordKeys);

        foreach ($passwordKeys as $key) {
            $this->assertMatchesRegularExpression('/^' . $key . '=/m', $joomla, $key . ' is a key of Joomla');
        }
    }

    public function testTheMinimumOfLowerCaseLettersIsChecked(): void
    {
        $saveUser = $this->saveUser();

        $this->assertStringContainsString("\$params->get('minimum_lowercase')", $saveUser);
        $this->assertStringContainsString("preg_match_all('/[a-z]/', \$value", $saveUser);
        $this->assertStringContainsString("Text::plural('JFIELD_PASSWORD_NOT_ENOUGH_LOWERCASE_LETTERS_N', \$minimumLowercase)", $saveUser);
    }
}
