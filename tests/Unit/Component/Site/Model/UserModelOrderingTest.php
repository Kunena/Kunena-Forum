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

namespace Kunena\Tests\Unit\Component\Site\Model;

use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The list of the users is ordered by a column which it shows. "filter_order=username" ordered it by the usernames (the names to
 * log in) when the forum shows the real names, and "filter_order=email" by the e-mail addresses which the members hide: the
 * order gave them away. (The state of the model needs a whole site: its code is read.)
 */
class UserModelOrderingTest extends UnitTestCase
{
    public function testOnlyTheShownNameAndForAModeratorTheEmailOrderTheList(): void
    {
        $source = str_replace("\r\n", "\n", Files::read('src/site/src/Model/UserModel.php'));

        $this->assertStringContainsString(
            "\$fields = ['id', \$this->config->username ? 'username' : 'name', 'registerDate', 'lastvisitDate', 'posts', 'karma', 'uhits'];",
            $source
        );
        $this->assertStringContainsString('if ($this->config->userlistEmail && $this->me->isModerator()) {', $source);
        $this->assertStringNotContainsString("\$fields = ['id', 'username', 'name',", $source);
    }
}
