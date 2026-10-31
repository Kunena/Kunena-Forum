<?php

/**
 * Kunena Component
 *
 * Regression test: isAuthorised() returns a boolean, it never throws. A call like
 *
 *     try { $topic->isAuthorised('sticky'); } catch (Exception $e) { ... return; }
 *
 * does therefore not check anything: the catch is never reached and the action is done for everybody. That was the
 * case at 46 places (moderation, editing, polls, announcements ...). Use tryAuthorise(), which throws, or test the
 * returned value.
 *
 * @package         Kunena.Tests
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 **/

namespace Kunena\Tests\Unit\Security;

use FilesystemIterator;
use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class DeadAuthorisationCheckTest extends TestCase
{
    private const SRC = __DIR__ . '/../../../src';

    /**
     * The places in the code where a call of isAuthorised() sits in a try block (a boolean can not be caught), or
     * where its result is thrown away.
     *
     * @return  string[]  Descriptions like "line 12: $topic->isAuthorised('x')".
     */
    public static function findDeadChecks(string $source): array
    {
        $found = [];

        // try { ... } catch, with one level of braces inside the block
        if (preg_match_all('/\btry\s*\{((?:[^{}]|\{[^{}]*\})*)\}\s*catch\b/s', $source, $blocks, PREG_OFFSET_CAPTURE)) {
            foreach ($blocks[1] as [$block, $offset]) {
                if (preg_match_all('/->isAuthorised\(/', $block, $calls, PREG_OFFSET_CAPTURE)) {
                    foreach ($calls[0] as [, $at]) {
                        $found[] = 'line ' . (substr_count($source, "\n", 0, $offset + $at) + 1) . ': isAuthorised() inside try/catch';
                    }
                }
            }
        }

        // a statement which throws the answer away
        if (preg_match_all('/^[ \t]*\$[^=;!&|?\n]*->isAuthorised\([^;\n]*\);[ \t]*$/m', $source, $bare, PREG_OFFSET_CAPTURE)) {
            foreach ($bare[0] as [, $at]) {
                $found[] = 'line ' . (substr_count($source, "\n", 0, $at) + 1) . ': result of isAuthorised() is ignored';
            }
        }

        return $found;
    }

    // The scanner itself has to keep working, otherwise a green result would mean nothing.

    public function testScannerFindsTheOldPattern(): void
    {
        $old = <<<'PHP'
<?php
class X {
    public function sticky()
    {
        try {
            $topic->isAuthorised('sticky');
        } catch (Exception $e) {
            $this->app->enqueueMessage($e->getMessage(), 'error');
            return;
        }
    }
}
PHP;

        $this->assertCount(2, self::findDeadChecks($old), 'try/catch and ignored result');
    }

    public function testScannerFindsTheDisabledThrowVariant(): void
    {
        $old = "<?php\ntry {\n    \$topic->isAuthorised('poll.create', null, false);\n} catch (Exception \$e) {\n}\n";

        $this->assertNotEmpty(self::findDeadChecks($old));
    }

    public function testScannerFindsItWithAnIfInsideTheBlock(): void
    {
        $old = "<?php\ntry {\n    if (\$x) {\n        \$topic->isAuthorised('a');\n    }\n} catch (Exception \$e) {\n}\n";

        $this->assertNotEmpty(self::findDeadChecks($old));
    }

    public function testScannerAcceptsCorrectUses(): void
    {
        $good = <<<'PHP'
<?php
class X {
    public function ok()
    {
        try {
            $topic->tryAuthorise('sticky');
        } catch (Exception $e) {
            return;
        }

        if ($topic->isAuthorised('edit')) {
            $allowed = $message->isAuthorised('read', null, false);
        }

        return $topic->isAuthorised('lock') && $this->me->exists();
    }
}
PHP;

        $this->assertSame([], self::findDeadChecks($good));
    }

    // The real source.

    public function testNoDeadAuthorisationChecksInTheSource(): void
    {
        $problems = [];

        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(self::SRC, FilesystemIterator::SKIP_DOTS));

        foreach ($files as $file) {
            $path = str_replace('\\', '/', $file->getPathname());

            if ($file->getExtension() !== 'php' || str_contains($path, '/External/')) {
                continue;
            }

            $source = file_get_contents($path);

            if (!str_contains($source, 'isAuthorised(')) {
                continue;
            }

            foreach (self::findDeadChecks($source) as $problem) {
                $problems[] = substr($path, strpos($path, '/src/') + 1) . ' ' . $problem;
            }
        }

        $this->assertSame(
            [],
            $problems,
            "isAuthorised() returns a boolean and never throws, so these checks do nothing. Use tryAuthorise():\n" . implode("\n", $problems)
        );
    }

    public function testTheControllersUseTheThrowingMethod(): void
    {
        // A few of the actions which were open to everybody: they have to ask tryAuthorise() now.
        $expect = [
            'site/src/Controllers/TopicController.php'      => ["tryAuthorise('sticky')", "tryAuthorise('lock')", "tryAuthorise('delete')", "tryAuthorise('move')", "tryAuthorise('edit')", "tryAuthorise('approve')", "tryAuthorise('permdelete')"],
            'site/src/Controllers/TopicsController.php'     => ["tryAuthorise('move')", "tryAuthorise('delete')", "tryAuthorise('approve')", "tryAuthorise('permdelete')"],
            'site/src/Controllers/AnnouncementController.php' => ["tryAuthorise('delete')", "tryAuthorise(\$id ? 'edit' : 'create')"],
            'site/src/Controllers/CategoryController.php'   => ["tryAuthorise('read')"],
            'admin/src/Controller/UserController.php'       => ["tryAuthorise('move')"],
        ];

        foreach ($expect as $file => $needles) {
            $source = file_get_contents(self::SRC . '/' . $file);

            foreach ($needles as $needle) {
                $this->assertStringContainsString($needle, $source, "$file should contain $needle");
            }
        }
    }

    public function testDeniedActionsStopInsteadOfContinuing(): void
    {
        $topic = file_get_contents(self::SRC . '/site/src/Controllers/TopicController.php');

        // Polls: a refused poll action is skipped.
        $this->assertSame(4, substr_count($topic, '$this->canUsePoll('), 'poll create (post), create, edit and delete in the edit action');
        $this->assertDoesNotMatchRegularExpression("/tryAuthorise\\('poll\\.[a-z]+'\\);\\s*\\} catch \\(Exception \\\$e\\) \\{\\s*\\\$this->app->enqueueMessage\\([^;]*;\\s*\\}/s", $topic, 'A poll check which only shows the message does not stop the poll action.');

        // Bulk move: no target, or a target which may not be read, stops the move.
        $topics = file_get_contents(self::SRC . '/site/src/Controllers/TopicsController.php');

        $this->assertMatchesRegularExpression("/tryAuthorise\\('read'\\);\\s*\\} catch \\(Exception \\\$e\\) \\{[^}]*return;/s", $topics);

        // Announcements and the admin move loop skip the item which is refused.
        $announcements = file_get_contents(self::SRC . '/site/src/Controllers/AnnouncementController.php');

        $this->assertGreaterThanOrEqual(3, preg_match_all("/tryAuthorise\\('(edit|delete)'\\);\\s*\\} catch \\(Exception \\\$e\\) \\{[^}]*continue;/s", $announcements));
        $this->assertMatchesRegularExpression("/tryAuthorise\\('move'\\);\\s*\\} catch \\(Exception \\\$e\\) \\{[^}]*continue;/s", file_get_contents(self::SRC . '/admin/src/Controller/UserController.php'));
    }
}
