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

namespace Kunena\Tests\Unit\Libraries\Kunena\Attachment;

use Kunena\Forum\Libraries\Attachment\KunenaPendingAttachment as Pending;
use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * The scheduled clean-up removed every pending attachment at every run, also the file of a member who was still writing the
 * message. It leaves the uploads of the last 24 hours alone now, and removes only what was never added to a message.
 */
class KunenaPendingAttachmentTest extends UnitTestCase
{
    public static function uploads(): array
    {
        $now = 1_800_000_000;

        return [
            'a minute old'      => [$now - 60, $now, false],
            'ten hours old'     => [$now - 36000, $now, false],
            'just under a day'  => [$now - 86399, $now, false],
            'exactly a day'     => [$now - 86400, $now, true],
            'three days'        => [$now - 259200, $now, true],
            'from the future'   => [$now + 5000, $now, false],
            'written right now' => [$now, $now, false],
            'no file'           => [null, $now, true],
            'zero time'         => [0, $now, true],
            'negative time'     => [-5, $now, true],
        ];
    }

    #[DataProvider('uploads')]
    public function testUploadIsRemovedOnlyWhenOld(?int $fileTime, int $now, bool $expected): void
    {
        $this->assertSame($expected, Pending::mayRemove($fileTime, $now));
    }

    public function testGracePeriodCanBeChangedAndIsNeverNegative(): void
    {
        $now = 1_800_000_000;

        $this->assertTrue(Pending::mayRemove($now - 7200, $now, 3600));
        $this->assertFalse(Pending::mayRemove($now - 1800, $now, 3600));
        $this->assertFalse(Pending::mayRemove($now + 10, $now, -100), 'A negative period does not make the future old.');
        $this->assertSame(86400, Pending::GRACE);
    }

    public function testTaskOnlyTakesWhatWasNeverAttachedAndAsksBeforeItDeletes(): void
    {
        $source = Files::read('src/plugins/plg_task_kunena/src/Extension/Kunena.php');

        $this->assertSame(1, preg_match('/private function cleanPendingAttachments\([^)]*\)[^{]*\{(.*?)\n    \}\n/s', $source, $m));

        $this->assertStringContainsString("quoteName('status') . ' = 0'", $m[1]);
        $this->assertStringContainsString("quoteName('mesid') . ' = 0'", $m[1], 'Only what was never added to a message.');
        $this->assertStringContainsString("order(\$db->quoteName('id') . ' ASC')", $m[1], 'The oldest first.');

        $ask = strpos($m[1], 'KunenaPendingAttachment::mayRemove(');

        $this->assertIsInt($ask);
        $this->assertLessThan(strpos($m[1], '$attachment->delete()'), $ask, 'The age is asked before the delete.');
        $this->assertStringContainsString('filemtime($path)', $m[1]);
    }

    public static function cancels(): array
    {
        return [
            'own pending upload'                => [0, 42, 42, true],
            'attachment of an own message'      => [5, 42, 42, false],
            'pending upload of another user'    => [0, 43, 42, false],
            'a guest and an upload of a guest'  => [0, 0, 0, false],
            'a guest and an upload of a member' => [0, 42, 0, false],
            'negative message'                  => [-1, 42, 42, false],
            'negative user'                     => [0, -1, -1, false],
        ];
    }

    #[DataProvider('cancels')]
    public function testCancelRemovesOnlyTheOwnPendingUploadsOfAMember(int $mesid, int $uploaderId, int $userId, bool $expected): void
    {
        $this->assertSame($expected, Pending::mayRemoveOnCancel($mesid, $uploaderId, $userId));
    }

    public function testCancelTaskChecksTheTokenAndAsksBeforeItDeletes(): void
    {
        $source = Files::read('src/site/src/Controllers/TopicController.php');

        $this->assertSame(1, preg_match('/public function removependingattachments\(\)[^{]*\{(.*?)
    \}
/s', $source, $m));

        $this->assertStringContainsString("Session::checkToken('request')", $m[1]);
        $this->assertStringContainsString('$this->me->exists() && !$this->me->isBanned()', $m[1], 'Nothing for a guest or a banned user.');
        $this->assertStringContainsString(', 0, 100)', $m[1], 'At most 100 ids.');

        $delete = strpos($m[1], '->delete()');

        $this->assertIsInt($delete);
        $this->assertLessThan($delete, strpos($m[1], 'Session::checkToken('), 'The token is checked before the delete.');
        $this->assertLessThan($delete, strpos($m[1], 'KunenaPendingAttachment::mayRemoveOnCancel('), 'The rule is asked before the delete.');
        $this->assertLessThan($delete, strpos($m[1], "isAuthorised('delete')"), 'The attachment is asked before the delete.');
    }
}
