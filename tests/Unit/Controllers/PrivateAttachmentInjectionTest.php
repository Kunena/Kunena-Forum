<?php

/**
 * Kunena Component
 *
 * Regression test: attachment ids sent with a private message must be checked before they are mapped, and before they
 * are made private. Otherwise any user can read, or lock, the attachments of everybody else.
 *
 * @package         Kunena.Tests
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 **/

namespace Kunena\Tests\Unit\Controllers;

use PHPUnit\Framework\TestCase;

class PrivateAttachmentInjectionTest extends TestCase
{
    private const CONTROLLER = __DIR__ . '/../../../src/site/src/Controllers/TopicController.php';
    private const PRIVATE    = __DIR__ . '/../../../src/libraries/kunena/src/KunenaPrivate/KunenaPrivateMessage.php';

    private function method(string $file, string $name): string
    {
        $source = file_get_contents($file);

        $found = preg_match(
            '/function ' . preg_quote($name, '/') . '\([^)]*\)[^{]*\{(.*?)(?=\n    (?:\/\*\*|public function|protected function|private function)|\n\}\s*$)/s',
            $source,
            $m
        );

        $this->assertSame(1, $found, "Method $name not found.");

        return $m[1];
    }

    public function testPostPrivateFiltersIdsBeforeMapping(): void
    {
        $body = $this->method(self::CONTROLLER, 'postPrivate');

        $this->assertStringContainsString('filterPrivateAttachmentIds(', $body);
        $this->assertLessThan(
            strpos($body, 'setMapped('),
            strpos($body, 'filterPrivateAttachmentIds('),
            'The ids must be filtered before they are mapped.'
        );

        // The raw ids of the request must never reach setMapped().
        $this->assertDoesNotMatchRegularExpression('/\$attachIds\s*=\s*explode\([^;]*;/', str_replace('filterPrivateAttachmentIds(explode(', '', $body));
    }

    public function testFilterOnlyKeepsOwnedUnattachedAttachments(): void
    {
        $body = $this->method(self::CONTROLLER, 'filterPrivateAttachmentIds');

        $this->assertStringContainsString("getById(\$ids, 'none')", $body, 'Missing ids must be looked up.');
        $this->assertStringContainsString('$attachment->exists()', $body);
        $this->assertStringContainsString('$attachment->mesid == $message->id', $body, 'Attachments of other messages must be dropped.');
        $this->assertStringContainsString('$this->me->userid', $body);
        $this->assertStringContainsString('$message->userid', $body);
        $this->assertStringContainsString('in_array((int) $attachment->userid, $owners, true)', $body, 'Strict owner comparison.');
    }

    public function testFilterRejectsGuests(): void
    {
        $this->assertStringContainsString('!$this->me->exists()', $this->method(self::CONTROLLER, 'filterPrivateAttachmentIds'));
    }

    public function testEmptyResultStopsEmptyPrivateMessage(): void
    {
        $body = $this->method(self::CONTROLLER, 'postPrivate');

        // After filtering, a message without text and without valid attachments must not be created.
        $this->assertGreaterThanOrEqual(
            2,
            preg_match_all('/if \(!trim\(\$body\) && !\$attachIds\)/', $body),
            'The empty check has to run again after the ids are filtered.'
        );
    }

    public function testSaveInternalChecksAttachmentsBeforeSavingTheMap(): void
    {
        $body = $this->method(self::PRIVATE, 'saveInternal');

        $filter = strpos($body, 'getById($this->_attachments->getMapped()');
        $reset  = strpos($body, '$this->_attachments->setMapped(');
        $save   = strpos($body, '$this->_attachments->setKey($this->id)->save()');
        $mark   = strpos($body, 'PROTECTION_PRIVATE');

        $this->assertNotFalse($filter);
        $this->assertNotFalse($reset, 'The mapped ids have to be replaced by the checked ones.');
        $this->assertLessThan($save, $filter, 'Check before the map is stored.');
        $this->assertLessThan($save, $reset, 'Replace the ids before the map is stored.');
        $this->assertLessThan($mark, $save);
    }

    public function testSaveInternalNeedsOwnerOrOwnPost(): void
    {
        $body = $this->method(self::PRIVATE, 'saveInternal');

        $this->assertStringContainsString('$attachment->exists()', $body);
        $this->assertStringContainsString('$attachment->userid == $this->author_id', $body);
        $this->assertStringContainsString('in_array((int) $attachment->mesid, $postIds, true)', $body);
    }

    public function testOnlyCheckedAttachmentsAreMadePrivate(): void
    {
        $body = $this->method(self::PRIVATE, 'saveInternal');

        // The list is built one attachment at a time, only from the ones which passed the check ...
        $this->assertStringContainsString('$attachments[$id] = $attachment;', $body);

        // ... and the loop which sets the protection walks that list, after the map was stored.
        $loop = strpos($body, 'foreach ($attachments as $attachment) {', strpos($body, 'setKey($this->id)->save()'));

        $this->assertNotFalse($loop, 'The protection must be set from the checked list.');
        $this->assertSame(1, substr_count($body, 'getById('), 'No second lookup of the raw ids.');
    }
}
