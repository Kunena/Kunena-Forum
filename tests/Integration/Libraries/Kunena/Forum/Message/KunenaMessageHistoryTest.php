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

namespace Kunena\Tests\Integration\Libraries\Kunena\Forum\Message;

use Joomla\CMS\Factory;
use Joomla\Database\DatabaseInterface;
use Joomla\DI\Container;
use Kunena\Forum\Libraries\Forum\KunenaDiagnostics;
use Kunena\Forum\Libraries\Forum\Message\KunenaMessage;
use Kunena\Forum\Libraries\Forum\Message\KunenaMessageHistory;
use Kunena\Forum\Libraries\User\KunenaUser;
use Kunena\Tests\Integration\DBTestHelper;
use Kunena\Tests\Integration\DBTestInterface;
use Kunena\Tests\Integration\DBTestTrait;
use Kunena\Tests\Integration\IntegrationTestCase;
use ReflectionClass;

/**
 * The edit history on the real table: an edit keeps the version before it with who edited, when and why, an edit which changes
 * nothing keeps none, a message keeps at most MAX_VERSIONS versions, a page loads the numbers at once, and the diagnostics find the
 * history of messages which are gone.
 */
class KunenaMessageHistoryTest extends IntegrationTestCase implements DBTestInterface
{
    use DBTestTrait;

    private ?Container $savedContainer = null;

    /**
     * @return  string[]
     */
    public function getSchemasToLoad(): array
    {
        return ['kunena'];
    }

    protected function setUp(): void
    {
        parent::setUp();

        // The container of Joomla gives the database of the tests to the class; the message and the user need the framework.
        if (!\defined('JVERSION')) {
            $this->markTestSkipped('The framework of Joomla is not loaded: set KUNENA_TEST_JOOMLA to a Joomla installation.');
        }

        $driver    = $this->getDBDriver();
        $container = new Container();
        $container->share(DatabaseInterface::class, static fn () => $driver);
        $container->share('DatabaseDriver', static fn () => $driver);

        $this->savedContainer = Factory::$container;
        Factory::$container   = $container;

        KunenaMessageHistory::clearCache();
        DBTestHelper::markDirty('#__kunena_messages_history');
    }

    protected function tearDown(): void
    {
        if ($this->savedContainer !== null) {
            Factory::$container = $this->savedContainer;
        }

        KunenaMessageHistory::clearCache();

        parent::tearDown();
    }

    /**
     * The message 5 after an edit, and its editor.
     *
     * @return  array{0: KunenaMessage, 1: KunenaUser}
     */
    private function edited(string $subject, string $text, string $reason = '', int $time = 1700000000): array
    {
        $message = (new ReflectionClass(KunenaMessage::class))->newInstanceWithoutConstructor();
        $message->id              = 5;
        $message->subject         = $subject;
        $message->message         = $text;
        $message->modified_reason = $reason;
        $message->modified_time   = $time;

        $editor         = (new ReflectionClass(KunenaUser::class))->newInstanceWithoutConstructor();
        $editor->userid = 42;

        return [$message, $editor];
    }

    public function testAnEditKeepsTheVersionBeforeIt(): void
    {
        [$message, $editor] = $this->edited('Subject', 'new text', 'Typo');

        $this->assertTrue(KunenaMessageHistory::record($message, ['subject' => 'Subject', 'message' => 'old text'], $editor));

        $versions = KunenaMessageHistory::getVersions(5);

        $this->assertCount(1, $versions);
        $this->assertSame('old text', $versions[0]->message);
        $this->assertSame('Subject', $versions[0]->subject);
        $this->assertSame(42, (int) $versions[0]->edited_by);
        $this->assertSame(1700000000, (int) $versions[0]->edited_time);
        $this->assertSame('Typo', $versions[0]->reason);
    }

    /**
     * A message which its author makes anonymous: his earlier edits in its history do not name him any more ("Edited by" him told
     * who wrote it, although the IP address and the e-mail were removed for that). The edits of a moderator keep their editor.
     * (The save of a message needs a whole site: the call in it is read.)
     */
    public function testTheHistoryOfAMessageMadeAnonymousDoesNotNameItsAuthor(): void
    {
        foreach ([42, 7] as $by) {
            [$message, $editor] = $this->edited('Subject', 'text by ' . $by);
            $editor->userid     = $by;
            KunenaMessageHistory::record($message, ['subject' => 'Subject', 'message' => 'before ' . $by], $editor);
        }

        KunenaMessageHistory::anonymize(5, 42);

        $this->assertSame([0, 7], array_map(static fn ($version) => (int) $version->edited_by, KunenaMessageHistory::getVersions(5)));

        $source = str_replace("\r\n", "\n", (string) file_get_contents(\dirname(__DIR__, 6) . '/src/libraries/kunena/src/Forum/Message/KunenaMessage.php'));
        $this->assertStringContainsString('$this->_anonymousAuthor = (int) $this->userid;', $source);
        $this->assertMatchesRegularExpression('/if \(!\$isNew && \$this->_anonymousAuthor && !\(int\) \$this->userid\) \{\n\s*KunenaMessageHistory::anonymize\(\(int\) \$this->id, \$this->_anonymousAuthor\);/', $source);
    }

    public function testAnEditWhichChangesNothingKeepsNoVersion(): void
    {
        [$message, $editor] = $this->edited('Subject', 'same', 'Only a reason');

        $this->assertFalse(KunenaMessageHistory::record($message, ['subject' => 'Subject', 'message' => 'same'], $editor));
        $this->assertSame([], KunenaMessageHistory::getVersions(5));
    }

    public function testAMessageKeepsItsLastVersions(): void
    {
        for ($i = 1; $i <= KunenaMessageHistory::MAX_VERSIONS + 3; $i++) {
            [$message, $editor] = $this->edited('Subject', 'text ' . $i);
            KunenaMessageHistory::record($message, ['subject' => 'Subject', 'message' => 'text ' . ($i - 1)], $editor);
        }

        $versions = KunenaMessageHistory::getVersions(5);

        $this->assertCount(KunenaMessageHistory::MAX_VERSIONS, $versions);
        // The oldest go: the first kept version is the text before the fourth edit, the last one the text before the last edit.
        $this->assertSame('text 3', $versions[0]->message);
        $this->assertSame('text ' . (KunenaMessageHistory::MAX_VERSIONS + 2), end($versions)->message);
    }

    public function testAPageLoadsTheNumbersOfItsMessages(): void
    {
        [$message, $editor] = $this->edited('S', 'b');
        KunenaMessageHistory::record($message, ['subject' => 'S', 'message' => 'a'], $editor);
        [$message, $editor] = $this->edited('S', 'c');
        KunenaMessageHistory::record($message, ['subject' => 'S', 'message' => 'b'], $editor);
        KunenaMessageHistory::clearCache();

        KunenaMessageHistory::load([5, 6]);

        $this->assertSame(2, KunenaMessageHistory::getCount(5));
        $this->assertSame(0, KunenaMessageHistory::getCount(6));
    }

    public function testTheDiagnosticsFindAndDeleteTheHistoryOfMessagesWhichAreGone(): void
    {
        [$message, $editor] = $this->edited('S', 'new');
        KunenaMessageHistory::record($message, ['subject' => 'S', 'message' => 'old'], $editor);

        // The message 5 does not exist in the table of the messages.
        $this->assertContains('historyOrphaned', KunenaDiagnostics::getList());
        $this->assertSame(1, KunenaDiagnostics::count('historyOrphaned'));
        $this->assertTrue(KunenaDiagnostics::delete('historyOrphaned'));
        $this->assertSame([], KunenaMessageHistory::getVersions(5));
    }
}
