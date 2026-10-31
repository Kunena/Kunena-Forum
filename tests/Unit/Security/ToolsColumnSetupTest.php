<?php

/**
 * Kunena Component
 *
 * Regression test for the tool which sets up the column "socials". Its ALTER TABLE was written with a double quote at the start
 * and single quotes inside ("ALTER TABLE ' . $db->quoteName(...) . '"), so it was text, not a statement: it failed every time.
 * The error was printed, and the tool went on: it imported the links into a column which did not exist, and then DROPPED the old
 * columns, which hold the only copy of the links. The tool for the column "language" said that the column was created when it
 * was not.
 *
 * @package         Kunena.Tests
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 **/

namespace Kunena\Tests\Unit\Security;

use PHPUnit\Framework\TestCase;

class ToolsColumnSetupTest extends TestCase
{
    private const SRC  = __DIR__ . '/../../../src';
    private const FILE = self::SRC . '/admin/src/Controller/ToolsController.php';

    private function body(string $method): string
    {
        $source = file_get_contents(self::FILE);

        $this->assertSame(1, preg_match('/public function ' . preg_quote($method, '/') . '\([^)]*\)[^{]*\{(.*?)\r?\n    \}\r?\n/s', $source, $m), $method);

        return $m[1];
    }

    public function testAlterOfSocialsIsAStatement(): void
    {
        $body = $this->body('setupsocials');

        $this->assertStringContainsString(
            "\$query = 'ALTER TABLE ' . \$db->quoteName('#__kunena_users') . ' ADD `socials` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL AFTER `ip`';",
            $body
        );
        $this->assertDoesNotMatchRegularExpression('/"ALTER TABLE \'/', $body, 'A double quoted string with single quote concatenation inside.');
    }

    public function testFailedAlterStopsBeforeAnythingIsMovedOrDropped(): void
    {
        $body = $this->body('setupsocials');

        $this->assertSame(1, preg_match('/\$db->execute\(\);\s*\}\s*catch \(Exception \$e\) \{(.*?)\r?\n            \}/s', $body, $m), 'The execution of the ALTER.');
        $this->assertStringContainsString("enqueueMessage(\$e->getMessage(), 'error')", $m[1]);
        $this->assertStringContainsString('return;', $m[1], 'Stop.');
        $this->assertDoesNotMatchRegularExpression('/\becho\b/', $m[1], 'The error is a message, not output in the middle of a redirect.');

        $alter = strpos($body, "ADD `socials`");
        $catch = strpos($body, 'catch (Exception $e)', $alter);
        $drop  = strpos($body, ' DROP ');

        $this->assertLessThan($drop, $catch, 'The old columns are dropped after the new one exists.');
        $this->assertLessThan(strpos($body, '$listOldSocialsColumns = ['), $catch, 'The import is after the guard.');
    }

    public function testDroppedColumnIsQuoted(): void
    {
        $body = $this->body('setupsocials');

        $this->assertStringContainsString("' DROP ' . \$db->quoteName(\$column)", $body);
        $this->assertDoesNotMatchRegularExpression("/' DROP ' \\. \\\$column\\b/", $body);
    }

    public function testLanguageToolDoesNotClaimSuccessAfterAFailure(): void
    {
        $body = $this->body('setupcolumnlanguage');

        $this->assertSame(1, preg_match('/\$db->execute\(\);\s*\}\s*catch \(Exception \$e\) \{(.*?)\r?\n            \}/s', $body, $m));
        $this->assertStringContainsString("enqueueMessage(\$e->getMessage(), 'error')", $m[1]);
        $this->assertStringContainsString('return;', $m[1]);
        $this->assertLessThan(strpos($body, 'COLUMN_LANGUAGE_HAS_BEEN_CREATED'), strpos($body, 'return;'));
    }

    public function testNoSqlInTheAdministratorIsAMixOfQuotes(): void
    {
        $found = [];
        $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(self::SRC, \FilesystemIterator::SKIP_DOTS));

        foreach ($files as $file) {
            $path = str_replace('\\', '/', $file->getPathname());

            if ($file->getExtension() !== 'php' || str_contains($path, '/External/')) {
                continue;
            }

            // A string which starts with a double quote and goes on as if it ended with a single one.
            if (preg_match('/"(?:ALTER|SELECT|UPDATE|DELETE|INSERT|CREATE|DROP)\b[^"\n;]*\' \. \$/i', file_get_contents($path))) {
                $found[] = substr($path, strpos($path, '/src/') + 1);
            }
        }

        $this->assertSame([], $found, 'A statement that is text.');
    }
}
