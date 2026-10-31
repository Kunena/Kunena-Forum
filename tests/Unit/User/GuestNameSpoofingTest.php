<?php

/**
 * Kunena Component
 *
 * Regression test for the name of a guest. Only the login name was refused, so a guest was able to sign as the name which a
 * member is shown with (the name of the account), in another case, with other spacing or with characters which can not be seen.
 *
 * @package         Kunena.Tests
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 **/

namespace Kunena\Tests\Unit\User;

use Kunena\Forum\Libraries\User\KunenaGuestName;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class GuestNameSpoofingTest extends TestCase
{
    private const MESSAGE = __DIR__ . '/../../../src/libraries/kunena/src/Forum/Message/KunenaMessage.php';

    public static function setUpBeforeClass(): void
    {
        if (!\defined('_JEXEC')) {
            \define('_JEXEC', 1);
        }

        require_once __DIR__ . '/../../../src/libraries/kunena/src/User/KunenaGuestName.php';
    }

    public static function cleaned(): array
    {
        return [
            'plain'                    => ['Visitor', 'Visitor'],
            'padding'                  => ["  Visitor \t", 'Visitor'],
            'inner spaces'             => ['Site    Admin', 'Site Admin'],
            'newline'                  => ["Site\nAdmin", 'Site Admin'],
            'no break space'           => ["Site\u{00A0}Admin", 'Site Admin'],
            'ideographic space'        => ["Site\u{3000}Admin", 'Site Admin'],
            'zero width space'         => ["Ad\u{200B}min", 'Admin'],
            'zero width joiner'        => ["Ad\u{200D}min", 'Admin'],
            'soft hyphen'              => ["Ad\u{00AD}min", 'Admin'],
            'byte order mark'          => ["\u{FEFF}Admin", 'Admin'],
            'right to left override'   => ["\u{202E}nimdA", 'nimdA'],
            'null byte'                => ["Ad\0min", 'Admin'],
            'control characters'       => ["Ad\x07min", 'Admin'],
            'full width letters'       => ['Ａｄｍｉｎ', 'Admin'],
            'invalid utf-8'            => ["Ad\xC3\x28min", ''],
            'only invisible'           => ["\u{200B}\u{200B}", ''],
            'empty'                    => ['', ''],
            'too long'                 => [str_repeat('a', 80), str_repeat('a', 35)],
        ];
    }

    #[DataProvider('cleaned')]
    public function testClean(string $typed, string $expected): void
    {
        if (!class_exists('Normalizer') && $typed === 'Ａｄｍｉｎ') {
            $this->markTestSkipped('The intl extension is not available.');
        }

        $this->assertSame($expected, KunenaGuestName::clean($typed));
    }

    public function testNameOfAnAccountGetsTheAnonymousName(): void
    {
        $accounts = ['site admin', 'jelle'];
        $taken    = fn (string $name): bool => \in_array(mb_strtolower($name), $accounts, true);

        foreach (['Site Admin', 'site admin', "  SITE   ADMIN ", "Site\u{200B} Admin", "Site\u{00A0}Admin", "Jelle\u{202E}"] as $typed) {
            $this->assertSame('Anonymous', KunenaGuestName::resolve($typed, $taken, 'Anonymous'), json_encode($typed));
        }
    }

    public function testFreeNameIsKeptCleaned(): void
    {
        $this->assertSame('Some Visitor', KunenaGuestName::resolve("  Some \u{200B}  Visitor ", fn () => false, 'Anonymous'));
    }

    public function testEmptyNameGetsTheAnonymousNameWithoutAskingTheDatabase(): void
    {
        $asked = 0;

        $this->assertSame('Anonymous', KunenaGuestName::resolve("\u{200B}", function () use (&$asked) {
            $asked++;

            return false;
        }, 'Anonymous'));
        $this->assertSame(0, $asked);
    }

    public function testTheCheckedNameIsTheCleanedOne(): void
    {
        $seen = null;
        KunenaGuestName::resolve("A\u{200B}d  min", function (string $name) use (&$seen) {
            $seen = $name;

            return false;
        }, 'Anonymous');

        $this->assertSame('Ad min', $seen, 'The lookup must see what will be stored.');
    }

    private function check(): string
    {
        $source = file_get_contents(self::MESSAGE);

        $this->assertSame(1, preg_match('/public function check\(\): bool\s*\{(.*?)\r?\n    \}\r?\n/s', $source, $m));

        return $m[1];
    }

    public function testMessageUsesThePolicyForGuests(): void
    {
        $body = $this->check();

        $this->assertStringContainsString('KunenaGuestName::resolve(', $body);
        $this->assertStringContainsString('$this->isAccountName(', $body);
        $this->assertDoesNotMatchRegularExpression('/\$nicktaken/', $body, 'Only the login name used to be asked.');
    }

    public function testAccountLookupAsksForTheLoginNameAndTheShownName(): void
    {
        $source = file_get_contents(self::MESSAGE);

        $this->assertSame(1, preg_match('/function isAccountName\(string \$name\): bool\s*\{(.*?)\r?\n    \}\r?\n/s', $source, $m));
        $this->assertStringContainsString("quoteName('name') . ' = :name OR '", $m[1]);
        $this->assertStringContainsString("quoteName('username') . ' = :username'", $m[1]);
        $this->assertStringContainsString("->bind(':name', \$name)", $m[1], 'The name is bound, not concatenated.');
    }

    public function testRegisteredAuthorsStillGetTheirOwnName(): void
    {
        $this->assertMatchesRegularExpression('/else\s*\{\s*\$this->name = \$author->getName\(\);/', $this->check());
    }
}
