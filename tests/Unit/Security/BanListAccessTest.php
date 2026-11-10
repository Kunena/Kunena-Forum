<?php

/**
 * Kunena Component
 *
 * The list of the banned users had no check of access ("TODO: add authorisation"). The tab of the profile is only shown to
 * moderators, but the list has its own address (view=user&layout=banmanager), and every visitor could open it.
 *
 * @package         Kunena.Tests
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 **/

namespace Kunena\Tests\Unit\Security;

use PHPUnit\Framework\TestCase;

class BanListAccessTest extends TestCase
{
    private const SRC = __DIR__ . '/../../../src/site/src';

    /**
     * The body of before() of a display controller.
     */
    private function before(string $file): string
    {
        $source = file_get_contents(self::SRC . '/' . $file);

        $this->assertSame(1, preg_match('/protected function before\(\)[^{]*\{(.*?)\r?\n    \}\r?\n/s', $source, $m), $file);

        return $m[1];
    }

    public function testListOfBannedUsersIsForModeratorsBeforeAnythingIsRead(): void
    {
        $file = 'Controller/User/Ban/Manager/UserBanManagerDisplay.php';
        $body = $this->before($file);

        $check = strpos($body, '!$this->me->isModerator()');
        $throw = strpos($body, 'throw new KunenaExceptionAuthorise(');
        $read  = strpos($body, 'KunenaBan::getBannedUsers(');

        $this->assertIsInt($check, 'Only a moderator sees the list.');
        $this->assertIsInt($throw);
        $this->assertIsInt($read);
        $this->assertLessThan($throw, $check);
        $this->assertLessThan($read, $throw, 'No ban is read before the check.');
        $this->assertStringContainsString('!$this->me->exists() ||', $body, 'A guest is no moderator.');
        $this->assertStringNotContainsString('TODO: add authorisation', $body);
        $this->assertStringContainsString(
            'use Kunena\Forum\Libraries\Exception\KunenaExceptionAuthorise;',
            file_get_contents(self::SRC . '/' . $file)
        );
    }
}
