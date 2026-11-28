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
 * Ban and Block of the users list leave a ban or a block which is there already: the list gives these tasks to the users who are
 * not banned or blocked, so a user who is comes there only from a page which was not reloaded or a second click, and the task lifted
 * the ban or the block (with the message that the user was banned or blocked). Unban and Unblock lift them. (The tasks need a whole
 * site: their code is read.)
 */
class UsersBanTwiceTest extends UnitTestCase
{
    public function testBanAndBlockDoNotLiftWhatIsThere(): void
    {
        $source = str_replace("\r\n", "\n", Files::read('src/admin/src/Controller/UsersController.php'));

        foreach (['ban', 'block'] as $task) {
            $this->assertSame(1, preg_match('/    public function ' . $task . '\(\)(?:: void)?\n.*?\n    \}\n/s', $source, $m), $task);
            $this->assertStringNotContainsString('setExpiration(', $m[0], $task);
        }

        // The tasks which lift them still do.
        foreach (['unban', 'unblock'] as $task) {
            $this->assertSame(1, preg_match('/    public function ' . $task . '\(\)(?:: void)?\n.*?\n    \}\n/s', $source, $m), $task);
            $this->assertStringContainsString('setExpiration(', $m[0], $task);
        }
    }
}
