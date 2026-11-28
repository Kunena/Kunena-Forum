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

namespace Kunena\Tests\Unit\Plugin\Kunena\Joomla;

use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The access fields of the category form (plugin Kunena - Joomla):
 * - "Allowed to post a new topic" and "Allowed to reply" without a choice: a list without a choice is not sent, the setting was
 *   left out of the parameters, and the defaults (Registered, Manager, Super Users) came back: nobody could be refused. An empty
 *   value is sent before each list.
 * - The primary and the secondary group are one group (the columns are one number): the lists allowed a choice of several, of
 *   which only the last one was kept.
 * (The fields need a whole site: the code is read.)
 */
class KunenaAccessJoomlaFormTest extends UnitTestCase
{
    private function source(): string
    {
        return Files::read('src/plugins/plg_kunena_joomla/src/Helper/KunenaAccessJoomla.php');
    }

    public function testTheListsOfThePostAndTheReplyHaveAnEmptyValue(): void
    {
        $source = $this->source();

        foreach (['level', 'group'] as $type) {
            foreach (['access_post', 'access_reply'] as $field) {
                $name = "params-joomla-{$type}[{$field}][]";

                $this->assertStringContainsString(
                    "'input' => '<input type=\"hidden\" name=\"{$name}\" value=\"\">' . HTMLHelper::_(\n                        'access.usergroup',\n                        '{$name}',",
                    str_replace("\r\n", "\n", $source),
                    $name
                );
            }
        }
    }

    public function testThePrimaryAndTheSecondaryGroupAreOneGroup(): void
    {
        $source = $this->source();

        foreach (['pubAccess', 'adminAccess'] as $field) {
            $this->assertSame(1, preg_match("/HTMLHelper::_\('access\.usergroup', '{$field}', [^\n]*\)/", $source, $m), $field);
            $this->assertStringNotContainsString('multiple', $m[0], $field);
        }
    }
}
