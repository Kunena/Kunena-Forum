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

namespace Kunena\Tests\Unit\Libraries\Kunena\Tools;

use Kunena\Forum\Libraries\Tools\KunenaSecurityAudit as A;
use Kunena\Forum\Libraries\Tools\KunenaUpdateCheck as U;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The tool "Security audit" warns about the settings which make the forum easy to abuse.
 */
class KunenaSecurityAuditTest extends UnitTestCase
{
    private const SAFE_CONFIG = [
        'fileTypes'   => 'txt,pdf,zip', 'imageTypes' => 'jpg,png', 'avatarTypes' => 'jpg,png', 'pubWrite' => 0, 'captcha' => 0,
        'imageUpload' => 'registered', 'fileUpload' => 'registered', 'showEmail' => 0, 'checkMimeTypes' => 1,
    ];

    private const SAFE_SITE = [
        'forbidden'      => ['php', 'html', 'svg'], 'protected' => ['media/kunena/attachments' => true], 'apache' => true, 'debug' => false,
        'errorReporting' => 'default', 'registration' => true, 'activation' => 1, 'https' => true, 'forceSsl' => 0,
        'integrity'      => ['lists' => 3, 'modified' => [], 'missing' => []],
    ];

    private static function statuses(array $config, array $env): array
    {
        $result = [];

        foreach (A::checks($config + self::SAFE_CONFIG, $env + self::SAFE_SITE) as [$key, $status]) {
            $result[$key] = $status;
        }

        return $result;
    }

    public function testASafeSiteHasNoProblem(): void
    {
        $this->assertSame([U::OK], array_values(array_unique(self::statuses([], []))));
    }

    public function testAFileTypeWhichRunsAsAScript(): void
    {
        $checks = A::checks(['fileTypes' => 'txt, .PHP ,zip', 'imageTypes' => 'jpg,svg'] + self::SAFE_CONFIG, self::SAFE_SITE);

        $this->assertSame(['types', U::WARNING, 'php, svg', ['php', 'svg'], 'kunena'], $checks[0]);
    }

    public function testAnUploadFolderWithoutItsHtaccess(): void
    {
        $this->assertSame(U::PROBLEM, self::statuses([], ['protected' => ['media/kunena/inbox' => false]])['folders']);
        // Not Apache: the .htaccess does nothing there, the server has to be set up.
        $this->assertSame(U::INFO, self::statuses([], ['protected' => ['media/kunena/inbox' => false], 'apache' => false])['folders']);
    }

    public function testGuestsWhoPostWithoutACaptcha(): void
    {
        $this->assertSame(U::PROBLEM, self::statuses(['pubWrite' => 1, 'captcha' => 0], [])['guests']);
        $this->assertSame(U::INFO, self::statuses(['pubWrite' => 1, 'captcha' => 1], [])['guests']);
        $this->assertSame(U::WARNING, self::statuses(['pubWrite' => 1, 'captcha' => 1, 'fileUpload' => 'everybody'], [])['uploads']);
        $this->assertSame(U::OK, self::statuses(['fileUpload' => 'everybody'], [])['uploads']);
    }

    public function testTheSettingsOfTheSite(): void
    {
        $status = self::statuses(['showEmail' => 1, 'checkMimeTypes' => 0], ['activation' => 0, 'errorReporting' => 'maximum', 'https' => false]);

        $this->assertSame(U::WARNING, $status['email']);
        $this->assertSame(U::WARNING, $status['mime']);
        $this->assertSame(U::WARNING, $status['registration']);
        $this->assertSame(U::WARNING, $status['debug']);
        $this->assertSame(U::WARNING, $status['https']);
        $this->assertSame(U::OK, self::statuses([], ['https' => false, 'forceSsl' => 2])['https']);
    }

    /**
     * The files of Kunena against the SHA-256 of the package: changed and missing files are listed, a copy of the code
     * without lists (a development site) is only an information.
     */
    public function testTheIntegrityOfTheFiles(): void
    {
        $integrity = ['lists' => 3, 'modified' => ['site/src/Controller.php'], 'missing' => ['library/src/Upload.php']];
        $checks    = A::checks(self::SAFE_CONFIG, ['integrity' => $integrity] + self::SAFE_SITE);
        $check     = array_values(array_filter($checks, static fn ($c) => $c[0] === 'integrity'))[0];

        $this->assertSame(U::WARNING, $check[1]);
        $this->assertSame('2', $check[2]);
        $this->assertSame(['modified:site/src/Controller.php', 'missing:library/src/Upload.php'], $check[3]);

        // Nothing changed: the algorithms which were checked are named.
        $clean = A::checks(self::SAFE_CONFIG, ['integrity' => ['lists' => 6, 'algorithms' => ['sha256', 'sha512'], 'modified' => [], 'missing' => []]] + self::SAFE_SITE);
        $clean = array_values(array_filter($clean, static fn ($c) => $c[0] === 'integrity'))[0];
        $this->assertSame([U::OK, 'SHA-256, SHA-512'], [$clean[1], $clean[2]]);

        $this->assertSame(U::INFO, self::statuses([], ['integrity' => ['lists' => 0, 'modified' => [], 'missing' => []]])['integrity']);
        $this->assertSame(U::INFO, self::statuses([], ['integrity' => null])['integrity']);
    }
}
