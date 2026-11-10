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

namespace Kunena\Tests\Unit\Libraries\Kunena\User;

use Kunena\Forum\Libraries\User\KunenaAccountActionPolicy as P;
use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * The tasks of the administrator which change an account of the site (ban, block, log out). Having the right to manage Kunena is
 * not the right to manage the users of the site: a forum manager was able to block or log out a Super User.
 */
class KunenaAccountActionPolicyTest extends UnitTestCase
{
    private function refusal(string $action, int $actor = 10, int $target = 20, bool $targetSuper = false, bool $targetForumAdmin = false, bool $actorSuper = false, array $rights = ['core.edit.state', 'core.delete']): ?string
    {
        return P::refusal($action, $actor, $target, $targetSuper, $targetForumAdmin, $actorSuper, static fn (string $right): bool => \in_array($right, $rights, true));
    }

    public function testManagerWithTheRightsMayManageAnOrdinaryAccount(): void
    {
        foreach ([P::BAN, P::UNBAN, P::BLOCK, P::UNBLOCK, P::LOGOUT, P::REMOVE] as $action) {
            $this->assertNull($this->refusal($action), $action);
        }
    }

    public function testRightsOnTheUsersOfTheSiteAreNeeded(): void
    {
        foreach ([P::BAN, P::UNBAN, P::BLOCK, P::UNBLOCK, P::LOGOUT, P::REMOVE] as $action) {
            $this->assertSame(P::PERMISSION, $this->refusal($action, rights: []), $action);
        }

        $this->assertNull($this->refusal(P::BLOCK, rights: ['core.edit.state']));
        $this->assertSame(P::PERMISSION, $this->refusal(P::REMOVE, rights: ['core.edit.state']), 'Deleting needs core.delete.');
        $this->assertSame(P::PERMISSION, $this->refusal(P::BLOCK, rights: ['core.delete']), 'Blocking needs core.edit.state.');
    }

    public function testNobodyBlocksOrRemovesThemselves(): void
    {
        foreach ([P::BAN, P::BLOCK, P::REMOVE] as $action) {
            $this->assertSame(P::SELF, $this->refusal($action, 10, 10), $action);
        }

        $this->assertNull($this->refusal(P::LOGOUT, 10, 10), 'Ending the own session is harmless.');
        $this->assertNull($this->refusal(P::UNBLOCK, 10, 10));
    }

    public function testOnlyASuperUserTouchesTheAccountOfASuperUser(): void
    {
        foreach ([P::UNBAN, P::UNBLOCK, P::LOGOUT, P::REMOVE, P::BAN, P::BLOCK] as $action) {
            $this->assertSame(P::ADMIN, $this->refusal($action, targetSuper: true), "$action by a manager");
        }

        $this->assertNull($this->refusal(P::LOGOUT, targetSuper: true, actorSuper: true), 'A Super User may end the session of another one.');
        $this->assertNull($this->refusal(P::REMOVE, targetSuper: true, actorSuper: true, rights: []), 'A Super User needs no extra right.');
    }

    public function testAdministratorsOfTheForumAreNotBannedOrBlocked(): void
    {
        $this->assertSame(P::ADMIN, $this->refusal(P::BAN, targetForumAdmin: true));
        $this->assertSame(P::ADMIN, $this->refusal(P::BLOCK, targetForumAdmin: true, actorSuper: true), 'canBan() refuses them for everybody.');
        $this->assertNull($this->refusal(P::UNBAN, targetForumAdmin: true), 'Lifting a ban is always fine.');
        $this->assertNull($this->refusal(P::UNBLOCK, targetForumAdmin: true));
    }

    public function testUnknownTaskIsNeverAllowed(): void
    {
        $this->assertSame(P::PERMISSION, $this->refusal('promote'));
        $this->assertSame(P::PERMISSION, $this->refusal(''));
    }

    public static function tasks(): array
    {
        return [['logout', 'LOGOUT', '$this->app->logout('], ['ban', 'BAN', '$ban->save()'], ['unban', 'UNBAN', '$ban->save()'], ['block', 'BLOCK', '$ban->save()'], ['unblock', 'UNBLOCK', '$ban->save()']];
    }

    #[DataProvider('tasks')]
    public function testTheControllerAsksBeforeItActs(string $method, string $constant, string $action): void
    {
        $source = Files::read('src/admin/src/Controller/UsersController.php');

        $this->assertSame(1, preg_match('/public function ' . $method . '\(\)[^{]*\{(.*?)\n    \}\n/s', $source, $m), $method);

        $check = strpos($m[1], "accountActionAllowed(KunenaAccountActionPolicy::$constant");

        $this->assertIsInt($check, "$method asks the policy.");
        $this->assertLessThan(strpos($m[1], $action), $check, "$method asks before it acts.");
        $this->assertLessThan($check, strpos($m[1], 'Session::checkToken('), 'The token is still checked first.');
    }

    public function testControllerUsesTheRightsOfTheUsersComponent(): void
    {
        $source = Files::read('src/admin/src/Controller/UsersController.php');

        $this->assertStringContainsString("\$me->authorise(\$right, 'com_users')", $source);
        $this->assertStringContainsString("\$target->authorise('core.admin')", $source);
    }
}
