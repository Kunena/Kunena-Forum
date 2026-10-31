<?php

/**
 * Kunena Component
 *
 *  - The client IP is stored with every message. Behind a load balancer the framework hands over the header X-Forwarded-For as it is,
 *    also when it is no address, and the page of a user in the administrator put the stored values into a query without quotes
 *    (second-order SQL injection). Only an address is stored now, and the list is made of quoted, valid addresses.
 *  - The tasks ban, unban, block, unblock, remove and logout of the administrator work on the account of the site. They did not apply
 *    the rules of KunenaBan::canBan() and did not ask for the rights on com_users: a forum manager could block or log out a Super User.
 *  - The system report is pasted into support forums. The secrets were hidden by a list of names, and the eBay Cert ID was not in it.
 *  - The search of the smilies had a stray "OR '<search>'" in its condition.
 *
 * @package         Kunena.Tests
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 **/

namespace Kunena\Tests\Unit\Security;

use Kunena\Forum\Libraries\Config\KunenaConfigReport;
use Kunena\Forum\Libraries\User\KunenaAccountActionPolicy as P;
use Kunena\Forum\Libraries\User\KunenaIpAddress;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

if (!\defined('_JEXEC')) {
    \define('_JEXEC', 1);
}

require_once __DIR__ . '/../../../src/libraries/kunena/src/User/KunenaIpAddress.php';
require_once __DIR__ . '/../../../src/libraries/kunena/src/User/KunenaAccountActionPolicy.php';
require_once __DIR__ . '/../../../src/libraries/kunena/src/Config/KunenaConfigReport.php';

class AdminHardeningTest extends TestCase
{
    private const SRC = __DIR__ . '/../../../src';

    private function source(string $file): string
    {
        return file_get_contents(self::SRC . '/' . $file);
    }

    // --- the stored IP address

    public static function addresses(): array
    {
        return [
            'ipv4'                   => ['203.0.113.9', '', '203.0.113.9'],
            'ipv4 padded'            => [' 203.0.113.9 ', '', '203.0.113.9'],
            'ipv6'                   => ['2001:db8::1', '', '2001:db8::1'],
            'ipv6, long form'        => ['2001:0db8:0000:0000:0000:0000:0000:0001', '', '2001:db8::1'],
            'sql in a header'        => ["x'OR'1'='1", '198.51.100.7', '198.51.100.7'],
            'sql, no connection'     => ["x'OR'1'='1", '', ''],
            'a script'               => ['<script>alert(1)</script>', 'not an address', ''],
            'a list'                 => ['1.2.3.4, 5.6.7.8', '198.51.100.7', '198.51.100.7'],
            'a name'                 => ['localhost', '', ''],
            'empty'                  => ['', '', ''],
            'a number'               => ['2130706433', '', ''],
            'octal'                  => ['0177.0.0.1', '', ''],
            'null byte'              => ["1.2.3.4\0'", '', ''],
            'quote after address'    => ["1.2.3.4'", '', ''],
        ];
    }

    #[DataProvider('addresses')]
    public function testOnlyAnAddressIsStored(string $detected, string $connection, string $expected): void
    {
        $this->assertSame($expected, KunenaIpAddress::clean($detected, $connection));
    }

    public function testNothingButTextIsHandled(): void
    {
        $this->assertSame('', KunenaIpAddress::clean(null, null));
        $this->assertSame('', KunenaIpAddress::clean(['1.2.3.4'], []));
        $this->assertSame('198.51.100.7', KunenaIpAddress::clean(false, '198.51.100.7'));
    }

    public function testUserHelperUsesIt(): void
    {
        $this->assertStringContainsString("KunenaIpAddress::clean(IpHelper::getIp(), \$_SERVER['REMOTE_ADDR'] ?? '')", $this->source('libraries/kunena/src/User/KunenaUserHelper.php'));
    }

    public function testIpListOfAUserIsQuotedAndValid(): void
    {
        $source = $this->source('admin/src/Model/UserModel.php');

        $this->assertStringContainsString('$db->quote($ip)', $source);
        $this->assertStringContainsString('FILTER_VALIDATE_IP', $source);
        $this->assertStringNotContainsString("implode(\"','\"", $source, 'The list is not glued with quotes by hand.');
        $this->assertStringNotContainsString("\$ipList = \"'{\$ipList}'\";", $source);
    }

    // --- the tasks on the accounts

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
        return [['logout', 'LOGOUT'], ['remove', 'REMOVE'], ['ban', 'BAN'], ['unban', 'UNBAN'], ['block', 'BLOCK'], ['unblock', 'UNBLOCK']];
    }

    #[DataProvider('tasks')]
    public function testTheControllerAsksBeforeItActs(string $method, string $const): void
    {
        $source = $this->source('admin/src/Controller/UsersController.php');

        $this->assertSame(1, preg_match('/public function ' . $method . '\(\)[^{]*\{(.*?)\r?\n    \}\r?\n/s', $source, $m), $method);
        $body = $m[1];

        $check = strpos($body, "accountActionAllowed(KunenaAccountActionPolicy::$const");
        $this->assertIsInt($check, "$method has the check");

        foreach (['->logout(', '->save()', '->delete()', '->ban('] as $act) {
            $at = strpos($body, $act);

            if ($at !== false) {
                $this->assertLessThan($at, $check, "$method: the check comes before $act");
            }
        }
    }

    public function testControllerUsesTheRightsOfTheUsersComponent(): void
    {
        $source = $this->source('admin/src/Controller/UsersController.php');

        $this->assertStringContainsString("authorise(\$right, 'com_users')", $source);
        $this->assertStringContainsString("authorise('core.admin')", $source);
    }

    // --- the report

    public static function hidden(): array
    {
        return [
            'ebay cert id'           => ['ebayCertId'],
            'ebay api key'           => ['ebayApiKey'],
            'x secret'               => ['XConsumerSecret'],
            'x key'                  => ['XConsumerKey'],
            'bluesky password'       => ['blueskyappPasswordOfApp'],
            'maps key'               => ['googleMapApiKey'],
            'spam key'               => ['stopForumSpamKey'],
            'future token'           => ['mastodonAccessToken'],
            'future secret'          => ['newServiceClientSecret'],
            'future password'        => ['smtpPassword'],
            'future pass'            => ['somethingPasswd'],
            'future cert'            => ['otherCertId'],
            'board title'            => ['boardTitle'],
            'email'                  => ['email'],
            'offline message'        => ['offlineMessage'],
            'visible address'        => ['emailVisibleAddress'],
            'affiliate'              => ['ebayAffiliateId'],
            'bluesky handle'         => ['blueskyappHandleOfApp'],
            'id'                     => ['id'],
        ];
    }

    #[DataProvider('hidden')]
    public function testSecretsStayOutOfTheReport(string $name): void
    {
        $this->assertTrue(KunenaConfigReport::isHidden($name), $name);
    }

    public static function shown(): array
    {
        return [['template'], ['pubProfile'], ['privateMessage'], ['floodProtection'], ['avatarSize'], ['boardOffline'], ['showHistory'], ['imageTypes']];
    }

    #[DataProvider('shown')]
    public function testOrdinarySettingsAreInTheReport(string $name): void
    {
        $this->assertFalse(KunenaConfigReport::isHidden($name), $name);
    }

    public function testEveryRealSettingThatLooksLikeASecretIsHidden(): void
    {
        $source = $this->source('libraries/kunena/src/Config/KunenaConfig.php');

        preg_match_all('/@property\s+\w+\s+\$(\w+)/', $source, $m);
        $names = $m[1];

        $this->assertNotEmpty($names);

        foreach ($names as $name) {
            if (preg_match('/key|secret|pass|token|cert/i', $name) && !str_contains(strtolower($name), 'rssauthor') && !str_contains(strtolower($name), 'privatemessage')) {
                $this->assertTrue(KunenaConfigReport::isHidden($name), "$name is a secret but is in the report");
            }
        }
    }

    public function testToolsModelUsesThePatternAndNotAList(): void
    {
        $source = $this->source('admin/src/Model/ToolsModel.php');

        $this->assertStringContainsString('KunenaConfigReport::isHidden((string) $key)', $source);
        $this->assertStringNotContainsString("\$key != 'ebayApiKey'", $source);
    }

    // --- the smilies

    public function testSmiliesSearchHasNoStrayCondition(): void
    {
        $source = $this->source('admin/src/Model/SmiliesModel.php');

        $this->assertStringNotContainsString("OR ' . \$search . ' OR", $source);
        $this->assertStringContainsString("a.location LIKE ' . \$search . ' OR a.emoticonbar LIKE '", $source);
    }
}
