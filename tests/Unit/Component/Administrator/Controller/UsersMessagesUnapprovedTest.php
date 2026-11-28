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
 * "Trash user messages" and "Move user messages" of the users list take the messages of the user which wait for approval too. They
 * took only the published ones (the default mode of getLatestMessages()): the queued posts of a spammer stayed in the queue, and one
 * approval of it published them; a move left them in the old category, and split a topic of the user instead of moving it. (The
 * tasks need a whole site: their code is read.)
 */
class UsersMessagesUnapprovedTest extends UnitTestCase
{
    private static function method(string $file, string $signature): string
    {
        $source = str_replace("\r\n", "\n", Files::read($file));
        $start  = strpos($source, $signature);

        return $start === false ? '' : substr($source, $start, (int) strpos($source, "\n    }\n", $start) - $start);
    }

    public function testTheTasksTakeTheUnapprovedMessages(): void
    {
        $trash = self::method('src/admin/src/Controller/UsersController.php', '    public function trashusermessages(): void');
        $move  = self::method('src/admin/src/Controller/UserController.php', '    public function moveMessages(');

        foreach (['trash' => $trash, 'move' => $move] as $what => $body) {
            $this->assertNotSame('', $body, $what);
            $this->assertStringNotContainsString('getLatestMessages(', $body, $what);
            $this->assertMatchesRegularExpression("/KunenaUserCleanup::messages\(\(int\) \\\$id, \['recent', 'unapproved'\]\)|KunenaUserCleanup::messages\(\\\$id, \['recent', 'unapproved'\]\)/", $body, $what);
        }

        // The collection is the one of the clean-up of a user, which the tasks may call.
        $cleanup = self::method('src/libraries/kunena/src/Tools/KunenaUserCleanup.php', '    public static function messages(int $userid, array $modes');
        $this->assertStringContainsString('foreach ($modes as $mode)', $cleanup);
    }
}
