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

use Kunena\Forum\Libraries\User\KunenaUserWarnings;
use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * The warnings with points: the points of a request, when a warning expires, the active points, the automatic ban, who may warn, the
 * tasks which give and remove a warning, the options, the table and the clean-ups.
 */
class KunenaUserWarningsTest extends UnitTestCase
{
    public static function points(): array
    {
        return [
            'one'          => [1, 1],
            'five'         => [5, 5],
            'as text'      => ['3', 3],
            'zero'         => [0, null],
            'six'          => [6, null],
            'negative'     => [-2, null],
            'a fraction'   => [2.5, null],
            'text'         => ['many', null],
            'nothing'      => [null, null],
        ];
    }

    #[DataProvider('points')]
    public function testThePointsOfARequest(mixed $value, ?int $points): void
    {
        $this->assertSame($points, KunenaUserWarnings::cleanPoints($value));
    }

    public function testAWarningExpiresAfterTheDays(): void
    {
        $this->assertSame(1000 + 30 * 86400, KunenaUserWarnings::expires(1000, 30));
        $this->assertSame(0, KunenaUserWarnings::expires(1000, 0), 'With 0 days a warning never expires.');
        $this->assertSame(0, KunenaUserWarnings::expires(1000, -5));
    }

    public function testOnlyTheWarningsWhichDidNotExpireCount(): void
    {
        $warnings = [
            (object) ['points' => 3, 'expires_time' => 0],
            (object) ['points' => 2, 'expires_time' => 2000],
            (object) ['points' => 4, 'expires_time' => 999],
            (object) ['points' => 1, 'expires_time' => 1000],
        ];

        // At 1000: the one which never expires and the one of 2000; the ones of 999 and 1000 are over.
        $this->assertSame(5, KunenaUserWarnings::activePoints($warnings, 1000));
        $this->assertSame(0, KunenaUserWarnings::activePoints([], 1000));
    }

    public static function bans(): array
    {
        // points, threshold, banned, reaches
        return [
            'reaches the threshold'      => [10, 10, false, true],
            'over the threshold'         => [12, 10, false, true],
            'under the threshold'        => [9, 10, false, false],
            'no automatic ban'           => [50, 0, false, false],
            'already banned'             => [10, 10, true, false],
        ];
    }

    #[DataProvider('bans')]
    public function testTheAutomaticBan(int $points, int $threshold, bool $banned, bool $reaches): void
    {
        $this->assertSame($reaches, KunenaUserWarnings::reachesBan($points, $threshold, $banned));
    }

    public static function whoMayWarn(): array
    {
        // enabled, moderator of the category, me, target, target exists, target admin, target moderator, me admin, may warn
        return [
            'a moderator warns a user'             => [true, true, 1, 2, true, false, false, false, true],
            'the setting is off'                   => [false, true, 1, 2, true, false, false, false, false],
            'not a moderator of the category'      => [true, false, 1, 2, true, false, false, false, false],
            'himself'                              => [true, true, 1, 1, true, false, false, false, false],
            'a guest message (no user)'            => [true, true, 1, 0, false, false, false, false, false],
            'a user who does not exist'            => [true, true, 1, 2, false, false, false, false, false],
            'an administrator'                     => [true, true, 1, 2, true, true, false, true, false],
            'a moderator, by a moderator'          => [true, true, 1, 2, true, false, true, false, false],
            'a moderator, by an administrator'     => [true, true, 1, 2, true, false, true, true, true],
            'a guest warns'                        => [true, true, 0, 2, true, false, false, false, false],
        ];
    }

    #[DataProvider('whoMayWarn')]
    public function testWhoMayWarn(bool $enabled, bool $moderator, int $me, int $target, bool $exists, bool $admin, bool $mod, bool $meAdmin, bool $may): void
    {
        $this->assertSame($may, KunenaUserWarnings::mayWarn($enabled, $moderator, $me, $target, $exists, $admin, $mod, $meAdmin));
    }

    public function testTheTaskChecksBeforeItWarnsAndBansAfter(): void
    {
        $source = Files::read('src/site/src/Controllers/UserController.php');

        $this->assertSame(1, preg_match('/public function warn\(\)\s*\{(.*?)\n    \}\n/s', $source, $m));

        $add = strpos($m[1], 'KunenaUserWarnings::add(');

        $this->assertIsInt($add);
        $this->assertLessThan($add, strpos($m[1], "Session::checkToken('post')"));
        $this->assertLessThan($add, strpos($m[1], 'KunenaUserWarnings::mayWarn('));
        $this->assertLessThan($add, strpos($m[1], 'KunenaUserWarnings::cleanPoints('));
        $this->assertGreaterThan($add, strpos($m[1], 'KunenaUserWarnings::banIfReached('));

        $this->assertSame(1, preg_match('/public function unwarn\(\)\s*\{(.*?)\n    \}\n/s', $source, $m));
        $this->assertLessThan(strpos($m[1], 'KunenaUserWarnings::remove('), strpos($m[1], 'KunenaUserWarnings::mayWarn('));
    }

    public function testTheOptionsAndTheirDefaults(): void
    {
        $xml      = simplexml_load_file(Files::path('src/admin/config.xml'));
        $defaults = [];

        foreach (['warnings', 'warningExpireDays', 'warningBanPoints', 'warningBanDays'] as $name) {
            $fields = $xml->xpath('//field[@name="' . $name . '"]');
            $this->assertCount(1, $fields, $name);
            $defaults[$name] = (string) $fields[0]['default'];
        }

        $this->assertSame(['warnings' => '1', 'warningExpireDays' => '30', 'warningBanPoints' => '10', 'warningBanDays' => '7'], $defaults);
    }

    public function testANewInstallCreatesTheTable(): void
    {
        $this->assertMatchesRegularExpression(
            '/CREATE TABLE IF NOT EXISTS `#__kunena_warnings`[^;]*`points`\s+tinyint\(4\)[^;]*`expires_time`\s+int\(11\)[^;]*KEY `user_id` \(`user_id`\)/i',
            Files::read('src/admin/sql/install/mysql/create_tables.utf8.sql')
        );

        $xml = simplexml_load_file(Files::path('src/admin/sql/install/schema/install.xml'));

        $this->assertCount(8, $xml->xpath('//table[@name="kunena_warnings"]/field'));
        $this->assertStringContainsString('CREATE TABLE IF NOT EXISTS `#__kunena_warnings`', Files::read('src/admin/sql/updates/mysql/8.0.0-DEV.sql'));
        $this->assertStringContainsString('DROP TABLE IF EXISTS `#__kunena_warnings`;', Files::read('src/admin/sql/uninstall/mysql/uninstall.mysql.utf8.sql'));
    }

    public static function cleanUps(): array
    {
        return [
            'a deleted Joomla user'         => ['src/plugins/plg_system_kunena/src/Extension/Kunena.php'],
            'the privacy export and remove' => ['src/plugins/plg_privacy_kunena/src/Extension/Kunena.php'],
        ];
    }

    #[DataProvider('cleanUps')]
    public function testTheWarningsAreRemovedWith(string $file): void
    {
        $this->assertStringContainsString('#__kunena_warnings', Files::read($file));
    }
}
