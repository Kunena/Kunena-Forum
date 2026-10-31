<?php

/**
 * Kunena Component
 *
 *  - The view of a topic put a private message on the place of the last post of its list only (the line was outside the loop), and a
 *    post of the list that is not on the page was written to as if it was there (an error on a null object).
 *  - The scheduled clean-up removed every pending attachment at every run, also the file of a member who was still writing the
 *    message. It leaves the uploads of the last 24 hours alone now, and removes only what was never added to a message.
 *
 * @package         Kunena.Tests
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 **/

namespace Kunena\Tests\Unit\Topic;

use Kunena\Forum\Libraries\Attachment\KunenaPendingAttachment as Pending;
use Kunena\Forum\Libraries\KunenaPrivate\KunenaPrivatePartsMap;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

if (!\defined('_JEXEC')) {
    \define('_JEXEC', 1);
}

require_once __DIR__ . '/../../../src/libraries/kunena/src/KunenaPrivate/KunenaPrivatePartsMap.php';
require_once __DIR__ . '/../../../src/libraries/kunena/src/Attachment/KunenaPendingAttachment.php';

class PrivatePartsAndPendingUploadsTest extends TestCase
{
    private const SRC = __DIR__ . '/../../../src';

    private function messages(int ...$ids): array
    {
        $list = [];

        foreach ($ids as $id) {
            $list[$id] = (object) ['id' => $id];
        }

        return $list;
    }

    // --- private parts

    public function testPrivateMessageIsOnEveryPostOfItsListThatIsOnThePage(): void
    {
        $messages = $this->messages(10, 11, 12);
        $pm       = (object) ['id' => 7];

        $this->assertSame(3, KunenaPrivatePartsMap::attach($messages, $pm, [10, 11, 12]));

        foreach ([10, 11, 12] as $id) {
            $this->assertSame($pm, $messages[$id]->pm[7], "post $id");
        }
    }

    public function testPostsOfAnotherPageAreSkippedWithoutAnError(): void
    {
        $messages = $this->messages(10, 11);
        $pm       = (object) ['id' => 7];

        $this->assertSame(2, KunenaPrivatePartsMap::attach($messages, $pm, [3, 10, 99, 11, 100]));
        $this->assertSame([10, 11], array_keys($messages), 'No place was made for a post that is not on the page.');
    }

    public function testSeveralPrivateMessagesOnOnePost(): void
    {
        $messages = $this->messages(10);
        $a        = (object) ['id' => 1];
        $b        = (object) ['id' => 2];

        KunenaPrivatePartsMap::attach($messages, $a, [10]);
        KunenaPrivatePartsMap::attach($messages, $b, [10]);

        $this->assertSame([1, 2], array_keys($messages[10]->pm));
    }

    public static function noLists(): array
    {
        return [[null], [false], [''], ['10'], [5], [[]]];
    }

    #[DataProvider('noLists')]
    public function testSomethingThatIsNoListChangesNothing(mixed $posts): void
    {
        $messages = $this->messages(10);

        $this->assertSame(0, KunenaPrivatePartsMap::attach($messages, (object) ['id' => 1], $posts));
        $this->assertObjectNotHasProperty('pm', $messages[10]);
    }

    public function testOldValueIsReplacedWhenItIsNoList(): void
    {
        $messages        = $this->messages(10);
        $messages[10]->pm = 'text';

        KunenaPrivatePartsMap::attach($messages, (object) ['id' => 4], [10]);

        $this->assertIsArray($messages[10]->pm);
        $this->assertArrayHasKey(4, $messages[10]->pm);
    }

    public function testListMayAlsoBeAnObjectOfIds(): void
    {
        $messages = $this->messages(10, 11);

        $this->assertSame(2, KunenaPrivatePartsMap::attach($messages, (object) ['id' => 1], (object) ['a' => 10, 'b' => 11]));
    }

    public static function displays(): array
    {
        return [
            'topic view'     => ['site/src/Controller/Topic/Item/TopicItemDisplay.php'],
            'reply history'  => ['site/src/Controller/Topic/Form/History/TopicFormHistoryDisplay.php'],
        ];
    }

    #[DataProvider('displays')]
    public function testDisplaysUseTheHelper(string $file): void
    {
        $source = file_get_contents(self::SRC . '/' . $file);

        $this->assertStringContainsString("KunenaPrivatePartsMap::attach(", $source);
        $this->assertStringContainsString("get('receivers.posts')", $source);
        $this->assertDoesNotMatchRegularExpression('/->pm\[\$pm->id\]\s*=\s*\$pm;/', $source, 'The private message is placed by the helper.');
        $this->assertDoesNotMatchRegularExpression('/foreach\s*\(\$posts as \$post\)/', $source);
    }

    // --- pending uploads

    public static function uploads(): array
    {
        $now = 1_800_000_000;

        return [
            'a minute old'               => [$now - 60, $now, false],
            'ten hours old'              => [$now - 36000, $now, false],
            'just under a day'           => [$now - 86399, $now, false],
            'exactly a day'              => [$now - 86400, $now, true],
            'three days'                 => [$now - 259200, $now, true],
            'from the future'            => [$now + 5000, $now, false],
            'written right now'          => [$now, $now, false],
            'no file'                    => [null, $now, true],
            'zero time'                  => [0, $now, true],
            'negative time'              => [-5, $now, true],
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
        $source = file_get_contents(self::SRC . '/plugins/plg_task_kunena/src/Extension/Kunena.php');

        $this->assertSame(1, preg_match('/private function cleanPendingAttachments\([^)]*\)[^{]*\{(.*?)\r?\n    \}\r?\n/s', $source, $m));
        $body = $m[1];

        $this->assertStringContainsString("quoteName('status') . ' = 0'", $body);
        $this->assertStringContainsString("quoteName('mesid') . ' = 0'", $body, 'Only what was never added to a message.');
        $this->assertStringContainsString("order(\$db->quoteName('id') . ' ASC')", $body, 'The oldest first.');

        $ask = strpos($body, 'KunenaPendingAttachment::mayRemove(');
        $this->assertIsInt($ask);
        $this->assertLessThan(strpos($body, '$attachment->delete()'), $ask, 'The age is asked before the delete.');
        $this->assertStringContainsString('filemtime($path)', $body);
    }
}
