<?php

/**
 * Kunena Component
 *
 * Regression test for tryAuthorise() used the wrong way round, and for the checks which were fixed with it.
 *
 * tryAuthorise($action) throws when the user is refused and returns null when allowed. With the third argument false it
 * does not throw but returns the exception when the user is refused (and null when allowed). So
 *
 *     if (!$category->tryAuthorise('admin'))              // runs for the users who ARE allowed
 *     if ($category->tryAuthorise('admin', null, false))  // runs for the users who are NOT allowed
 *
 * are both wrong. Test a condition with isAuthorised() (a boolean), and use tryAuthorise() as a statement in a try.
 *
 * @package         Kunena.Tests
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 **/

namespace Kunena\Tests\Unit\Security;

use FilesystemIterator;
use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class InvertedAuthorisationCheckTest extends TestCase
{
    private const SRC = __DIR__ . '/../../../src';

    /**
     * @return  string[]
     */
    public static function findInverted(string $source): array
    {
        $found = [];

        // !$x->tryAuthorise('action')  : negation of the form which throws
        if (preg_match_all('/!\s*\$[\w>\-\[\]\'"]*(?:\([^()]*\))?[\w>\-]*->tryAuthorise\((?![^;]*,\s*(?:false|0)\s*\))[^;]*\)/', $source, $m, PREG_OFFSET_CAPTURE)) {
            foreach ($m[0] as [, $at]) {
                $found[] = 'line ' . (substr_count($source, "\n", 0, $at) + 1) . ': negation of tryAuthorise(), which throws';
            }
        }

        // if ($x->tryAuthorise('action', null, false))  : the exception is truthy when the user is refused
        if (preg_match_all('/\b(?:if|elseif|while)\s*\(\s*(?<![!])\$[\w>\-\[\]\'"]*(?:\([^()]*\))?[\w>\-]*->tryAuthorise\([^;{]*,\s*(?:false|0)\s*\)|&&\s*\$[\w>\-\[\]\'"]*(?:\([^()]*\))?[\w>\-]*->tryAuthorise\([^;{]*,\s*(?:false|0)\s*\)/', $source, $m, PREG_OFFSET_CAPTURE)) {
            foreach ($m[0] as [, $at]) {
                $found[] = 'line ' . (substr_count($source, "\n", 0, $at) + 1) . ': tryAuthorise(..., false) used as a condition, it is true for a refused user';
            }
        }

        return $found;
    }

    // The scanner has to see the old mistakes, and leave the correct code alone.

    public function testFindsTheNegatedThrowingForm(): void
    {
        $old = "<?php\nif (!\$category->getParent()->tryAuthorise('admin')) {\n    return;\n}\n";

        $this->assertNotEmpty(self::findInverted($old));
    }

    public function testFindsTheNonThrowingFormAsCondition(): void
    {
        $old = "<?php\nif (\$category->tryAuthorise('admin', null, false) && \$category->removeModerator(\$user)) {\n}\n";

        $this->assertNotEmpty(self::findInverted($old));
    }

    public function testFindsItAfterAnAnd(): void
    {
        $old = "<?php\nif (\$a && \$category->tryAuthorise('admin', null, false)) {\n}\n";

        $this->assertNotEmpty(self::findInverted($old));
    }

    public function testLeavesCorrectCodeAlone(): void
    {
        $good = <<<'PHP'
<?php
class X {
    public function isAuthorised($action = 'read', $user = null): bool
    {
        return !$this->tryAuthorise($action, $user, false);
    }

    public function ok()
    {
        try {
            $category->tryAuthorise('admin');
        } catch (Exception $e) {
            return;
        }

        $denied = $category->tryAuthorise('admin', null, false);

        if ($denied) {
            return;
        }

        if ($category->isAuthorised('admin') && $category->removeModerator($user)) {
        }

        if (!$category->getParent()->isAuthorised('admin')) {
        }
    }
}
PHP;

        $this->assertSame([], self::findInverted($good));
    }

    // The real source.

    public function testNoInvertedChecksInTheSource(): void
    {
        $problems = [];

        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(self::SRC, FilesystemIterator::SKIP_DOTS));

        foreach ($files as $file) {
            $path = str_replace('\\', '/', $file->getPathname());

            if ($file->getExtension() !== 'php' || str_contains($path, '/External/')) {
                continue;
            }

            $source = file_get_contents($path);

            if (!str_contains($source, 'tryAuthorise(')) {
                continue;
            }

            foreach (self::findInverted($source) as $problem) {
                $problems[] = substr($path, strpos($path, '/src/') + 1) . ' ' . $problem;
            }
        }

        $this->assertSame([], $problems, "Authorisation checks which work the wrong way round:\n" . implode("\n", $problems));
    }

    // The checks which were fixed.

    public function testModeratorsAreOnlyRemovedByACategoryAdmin(): void
    {
        $source = file_get_contents(self::SRC . '/admin/src/Controller/CategoryController.php');

        $this->assertStringContainsString("\$category->isAuthorised('admin') && \$category->removeModerator(\$user)", $source);
        $this->assertStringNotContainsString("tryAuthorise('admin', null, false) && \$category->removeModerator", $source);
    }

    public function testOrderingChecksAskIsAuthorised(): void
    {
        $source = file_get_contents(self::SRC . '/admin/src/Controller/CategoriesController.php');

        // Ordering (saveorder), moving up/down and the check of the parent in a third action all ask isAuthorised().
        $this->assertSame(3, substr_count($source, "!\$category->getParent()->isAuthorised('admin')"));
        $this->assertStringNotContainsString("!\$category->getParent()->tryAuthorise('admin')", $source, 'tryAuthorise() throws, a negation of it is wrong');
    }

    private function message(string $method): string
    {
        $source = file_get_contents(self::SRC . '/libraries/kunena/src/Forum/Message/KunenaMessage.php');

        $this->assertSame(1, preg_match('/function ' . $method . '\([^)]*\)[^{]*\{(.*?)\n    \}\n/s', $source, $m), $method);

        return $m[1];
    }

    public function testAddAttachmentsChecksTheOwner(): void
    {
        $body = $this->message('addAttachments');

        // The 'none' mode skips the authorisation of the attachment, so the owner has to be checked here.
        $this->assertStringContainsString('$attachment->userid == $me->userid', $body);
        $this->assertStringContainsString('$me->isModerator($this->getCategory())', $body);
        $this->assertStringContainsString('$me->exists()', $body, 'A guest (user id 0) owns nothing.');
        $this->assertStringContainsString('$attachment->mesid == $this->id', $body, 'The attachments of this message stay.');
        $this->assertDoesNotMatchRegularExpression('/_attachments_add\s*\+=\s*\$this->getAttachments/', $body, 'The ids must not be taken over without a check.');
    }

    public function testAnAttachmentOfSomebodyElseIsNotAddedWithoutTheOwnerCheck(): void
    {
        $body = $this->message('addAttachments');

        $this->assertLessThan(
            strpos($body, '$this->_attachments_add[$id] = $attachment;'),
            strpos($body, 'if ($ofThisMessage || $mayAdd)'),
            'The assignment must sit behind the condition.'
        );
    }

    public function testRightToAttachIsEnforced(): void
    {
        $body = $this->message('updateAttachments');

        // The result of the check used to be thrown away.
        $this->assertDoesNotMatchRegularExpression("/\\\$attachment->tryAuthorise\\('create(image|file)', null, false\\);/", $body);

        $this->assertStringContainsString("'topic.post.attachment.createimage'", $body);
        $this->assertStringContainsString("'topic.post.attachment.createfile'", $body);
        $this->assertMatchesRegularExpression('/if \(\$denied\) \{.*?continue;/s', $body, 'A refused attachment must be skipped.');
    }

    public function testRefusedAttachmentIsNotChanged(): void
    {
        $body = $this->message('updateAttachments');

        $check  = strpos($body, 'if ($denied)');
        $assign = strpos($body, '$attachment->mesid = $this->id;');

        $this->assertNotFalse($check);
        $this->assertNotFalse($assign);
        $this->assertLessThan($assign, $check, 'The message id is set after the check, so a refused attachment stays free.');
    }

    public function testAttachmentsOfTheMessageAreNotCheckedAgain(): void
    {
        $body = $this->message('updateAttachments');

        $this->assertStringContainsString('$newToMessage = $attachment->mesid != $this->id;', $body);
        $this->assertMatchesRegularExpression('/if \(\$newToMessage\) \{.*?if \(\$denied\)/s', $body, 'Only new attachments need the right to attach.');
    }
}
