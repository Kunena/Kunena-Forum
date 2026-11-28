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

namespace Kunena\Tests\Unit\Component\Site\Template;

use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The user list offers the order by the e-mail addresses to the moderators only, as the model accepts it (UserModel: the column hides
 * the address of each member who hides it, and the order gave it away). Both templates showed the sort to every member, whose click
 * ordered the list by the id.
 */
class UserListEmailSortTest extends UnitTestCase
{
    public function testTheEmailSortIsForTheModerators(): void
    {
        $aurelia = str_replace("\r\n", "\n", Files::read('src/site/template/aurelia/layouts/user/list/default.php'));

        $this->assertMatchesRegularExpression("/if \(\\\$this->me->isModerator\(\)\) :\n\s*echo HTMLHelper::_\(\n\s*'kunenagrid.sort',\n\s*'COM_KUNENA_USRL_EMAIL',/", $aurelia);

        $agora = Files::read('src/site/template/agora/layouts/user/list/default.php');

        $this->assertStringContainsString("if (\$config->\$option && (\$sort[1] !== 'email' || \$this->me->isModerator())) {", $agora);

        // The rule of the model.
        $this->assertStringContainsString('if ($this->config->userlistEmail && $this->me->isModerator()) {', Files::read('src/site/src/Model/UserModel.php'));
    }
}
