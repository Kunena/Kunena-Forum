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
 * The ban box of a message does not change a ban which is active: it is not shown for a banned user, but sent from a page opened
 * before the ban it made the lifetime block of an administrator a ban of a few days, unblocked the Joomla account and erased both
 * reasons. (The task needs a whole site: its code is read.)
 */
class UserQuickBanBannedTest extends UnitTestCase
{
    public function testAQuickBanOfABannedUserIsRefusedBeforeTheBanIsChanged(): void
    {
        $source = str_replace("\r\n", "\n", Files::read('src/site/src/Controllers/UserController.php'));

        $refuse = strpos($source, "if (\$ban->id && \$ban->isEnabled() && \$this->app->getInput()->getInt('quick', 0)) {");
        $change = strpos($source, '$ban->setExpiration($expiration, $comment);');

        $this->assertNotFalse($refuse);
        $this->assertLessThan($change, $refuse);
        $this->assertStringContainsString("Text::_('COM_KUNENA_USER_BAN_ALREADY')", substr($source, $refuse, 300));
        $this->assertStringContainsString('COM_KUNENA_USER_BAN_ALREADY = ', Files::read('src/site/language/en-GB/com_kunena.ini'));
    }
}
