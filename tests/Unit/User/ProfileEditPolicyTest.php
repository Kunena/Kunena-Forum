<?php

/**
 * Kunena Component
 *
 * Regression test: a moderator of the forum could save the profile of any user, including the Joomla account (email,
 * user name, password) of a Super User, and so take the site over. The rule is now the one of banning: nobody but an
 * administrator touches an administrator or a moderator, and the login of an account is only changed by the user
 * himself or by somebody who may edit users in Joomla (a Super User is only changed by a Super User).
 *
 * @package         Kunena.Tests
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 **/

namespace Kunena\Tests\Unit\User;

use Kunena\Forum\Libraries\User\KunenaProfileEditPolicy;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ProfileEditPolicyTest extends TestCase
{
    private const CONTROLLER = __DIR__ . '/../../../src/site/src/Controllers/UserController.php';

    public static function setUpBeforeClass(): void
    {
        if (!\defined('_JEXEC')) {
            \define('_JEXEC', 1);
        }

        require_once __DIR__ . '/../../../src/libraries/kunena/src/User/KunenaProfileEditPolicy.php';
    }

    // --- The Kunena profile ---

    #[DataProvider('profileCases')]
    public function testKunenaProfile(string $who, bool $self, bool $actorMod, bool $actorAdmin, bool $targetAdmin, bool $targetMod, bool $expected): void
    {
        $this->assertSame($expected, KunenaProfileEditPolicy::canEditProfile($self, $actorMod, $actorAdmin, $targetAdmin, $targetMod), $who);
    }

    public static function profileCases(): array
    {
        //                                                    self   mod    admin  tAdmin tMod   allowed
        return [
            ['member edits himself',                          true,  false, false, false, false, true],
            ['member edits somebody else',                    false, false, false, false, false, false],
            ['member edits a moderator',                      false, false, false, false, true,  false],
            ['member edits an administrator',                 false, false, false, true,  false, false],
            ['moderator edits himself',                       true,  true,  false, false, true,  true],
            ['moderator edits a member',                      false, true,  false, false, false, true],
            ['moderator edits another moderator',             false, true,  false, false, true,  false],
            ['moderator edits an administrator',              false, true,  false, true,  false, false],
            ['moderator edits an administrator who moderates', false, true, false, true,  true,  false],
            ['administrator edits a member',                  false, true,  true,  false, false, true],
            ['administrator edits a moderator',               false, true,  true,  false, true,  true],
            ['administrator edits another administrator',     false, true,  true,  true,  false, true],
            ['administrator edits himself',                   true,  true,  true,  true,  true,  true],
            ['guest-like actor edits an administrator',       false, false, false, true,  false, false],
        ];
    }

    // --- The Joomla account ---

    #[DataProvider('accountCases')]
    public function testJoomlaAccount(string $who, bool $self, bool $mayEditUsers, bool $actorSuper, bool $targetSuper, bool $expected): void
    {
        $this->assertSame($expected, KunenaProfileEditPolicy::canEditAccount($self, $mayEditUsers, $actorSuper, $targetSuper), $who);
    }

    public static function accountCases(): array
    {
        //                                                         self   mayEdit aSuper tSuper allowed
        return [
            ['anybody changes his own account',                    true,  false,   false,  false,  true],
            ['a Super User changes his own account',               true,  true,    true,   true,   true],
            ['a moderator without the permission, a member',       false, false,   false,  false,  false],
            ['a moderator without the permission, a Super User',   false, false,   false,  true,   false],
            ['a user manager changes a member',                    false, true,    false,  false,  true],
            ['a user manager changes a Super User',                false, true,    false,  true,   false],
            ['a Super User changes a member',                      false, true,    true,   false,  true],
            ['a Super User changes another Super User',            false, true,    true,   true,   true],
            ['a Super User without the edit flag changes a user',  false, false,   true,   false,  false],
        ];
    }

    public function testTheTakeoverThatWasPossible(): void
    {
        // A moderator (no Joomla permission) sets the password and the email of a Super User.
        $actorIsAdmin = false;

        $this->assertFalse(KunenaProfileEditPolicy::canEditProfile(false, true, $actorIsAdmin, true, true), 'not even the profile');
        $this->assertFalse(KunenaProfileEditPolicy::canEditAccount(false, false, false, true), 'and not the login');
    }

    public function testModeratorsStillModerateProfilesOfMembers(): void
    {
        // The normal job of a moderator keeps working: signature, avatar and location of a member.
        $this->assertTrue(KunenaProfileEditPolicy::canEditProfile(false, true, false, false, false));

        // ... but the login of that member is not theirs.
        $this->assertFalse(KunenaProfileEditPolicy::canEditAccount(false, false, false, false));
    }

    // --- The controller ---

    private function save(): string
    {
        $source = file_get_contents(self::CONTROLLER);

        $this->assertSame(1, preg_match('/public function save\(\)\s*\{(.*?)\n    \}\n/s', $source, $m));

        return $m[1];
    }

    public function testSaveChecksThePolicyBeforeAnythingIsWritten(): void
    {
        $body = $this->save();

        $profile = strpos($body, 'KunenaProfileEditPolicy::canEditProfile(');
        $account = strpos($body, 'KunenaProfileEditPolicy::canEditAccount(');

        $this->assertNotFalse($profile, 'The profile check is missing.');
        $this->assertNotFalse($account, 'The account check is missing.');

        foreach (['$this->saveUser()', '$this->saveProfile()', '$this->saveSettings()', '$this->user->save()'] as $write) {
            $at = strpos($body, $write);

            $this->assertNotFalse($at, $write);
            $this->assertLessThan($at, $profile, "The profile check must come before $write");
        }

        $this->assertMatchesRegularExpression('/canEditProfile\(.*?\)\s*\)\s*\{\s*throw new KunenaExceptionAuthorise\([^;]*403\);/s', $body, 'A refusal has to stop the save.');
    }

    public function testTheJoomlaAccountIsOnlySavedWhenAllowed(): void
    {
        $body = $this->save();

        $this->assertSame(1, substr_count($body, '$this->saveUser()'), 'saveUser() is called once.');
        $this->assertMatchesRegularExpression('/if \(\$mayEditAccount\) \{\s*\$success = \$this->saveUser\(\);/s', $body);
        $this->assertStringContainsString("authorise('core.edit', 'com_users')", $body, 'The permission of Joomla to edit users.');
        $this->assertStringContainsString("authorise('core.admin')", $body, 'Super Users are recognised.');
    }

    public function testSaveUserIsNotCalledFromAnywhereElse(): void
    {
        $source = file_get_contents(self::CONTROLLER);

        $this->assertSame(1, substr_count($source, '$this->saveUser()'), 'Every call of saveUser() has to go through the check.');
    }

    public function testTheFieldsAreSavedForTheCheckedUser(): void
    {
        $source = file_get_contents(self::CONTROLLER);

        foreach (['saveProfile', 'saveSettings'] as $method) {
            $this->assertSame(1, preg_match('/function ' . $method . '\(\)\s*\{(.*?)\n    \}\n/s', $source, $m), $method);

            $this->assertStringContainsString('(int) $this->user->id', $m[1], "$method has to use the user which save() checked");
            $this->assertDoesNotMatchRegularExpression("/get\\('userid'/", $m[1], "$method must not read the userid of the request again");
            $this->assertDoesNotMatchRegularExpression("/getInt\\('userid'/", $m[1], "$method must not read the userid of the request again");
        }
    }
}
