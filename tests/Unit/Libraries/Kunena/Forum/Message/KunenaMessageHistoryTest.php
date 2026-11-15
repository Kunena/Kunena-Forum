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

namespace Kunena\Tests\Unit\Libraries\Kunena\Forum\Message;

use Kunena\Forum\Libraries\Forum\Message\KunenaMessage;
use Kunena\Forum\Libraries\Forum\Message\KunenaMessageHistory;
use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * The edit history of the messages: who sees it, which edit keeps a version, the change word by word, the table in every way a site
 * gets its tables, and the clean-ups which remove the history with its messages and users.
 */
class KunenaMessageHistoryTest extends UnitTestCase
{
    public static function whoSees(): array
    {
        // setting, moderator, may read, sees
        return [
            'off, a moderator'                  => [KunenaMessageHistory::OFF, true, true, false],
            'off, a reader'                     => [KunenaMessageHistory::OFF, false, true, false],
            'moderators, a moderator'           => [KunenaMessageHistory::MODERATORS, true, true, true],
            'moderators, a reader'              => [KunenaMessageHistory::MODERATORS, false, true, false],
            'everybody, a reader'               => [KunenaMessageHistory::EVERYBODY, false, true, true],
            'everybody, who may not read'       => [KunenaMessageHistory::EVERYBODY, false, false, false],
            'everybody, a moderator'            => [KunenaMessageHistory::EVERYBODY, true, false, true],
            'a value which is not a setting'    => [7, true, true, false],
        ];
    }

    #[DataProvider('whoSees')]
    public function testWhoSeesTheHistory(int $setting, bool $moderator, bool $canRead, bool $sees): void
    {
        $this->assertSame($sees, KunenaMessageHistory::canSee($setting, $moderator, $canRead));
    }

    private function message(string $subject, string $text): KunenaMessage
    {
        // The class of the message needs the framework of Joomla.
        $this->requireJoomla();

        return self::bare(KunenaMessage::class, ['id' => 5, 'subject' => $subject, 'message' => $text]);
    }

    public function testAnEditOfTheTextOrOfTheSubjectKeepsAVersion(): void
    {
        $this->assertTrue(KunenaMessageHistory::changed(['subject' => 'A', 'message' => 'old'], $this->message('A', 'new')));
        $this->assertTrue(KunenaMessageHistory::changed(['subject' => 'A', 'message' => 'same'], $this->message('B', 'same')));
    }

    public function testAnEditWhichChangesNeitherKeepsNone(): void
    {
        // Only the reason (or nothing) changed.
        $this->assertFalse(KunenaMessageHistory::changed(['subject' => 'A', 'message' => 'same'], $this->message('A', 'same')));
    }

    public function testTheChangedWordsAreMarked(): void
    {
        $this->assertSame(
            'The quick <del>brown</del><ins>red</ins> fox.',
            KunenaMessageHistory::diff('The quick brown fox.', 'The quick red fox.')
        );
    }

    public function testAnAddedWordIsMarked(): void
    {
        $this->assertSame('one <ins>two </ins>three', KunenaMessageHistory::diff('one three', 'one two three'));
    }

    public function testARemovedWordIsMarked(): void
    {
        $this->assertSame('one <del>two </del>three', KunenaMessageHistory::diff('one two three', 'one three'));
    }

    public function testTheSameTextHasNoMarks(): void
    {
        $this->assertSame('same text', KunenaMessageHistory::diff('same text', 'same text'));
    }

    public function testTheLinesAndTheSpacesAreKept(): void
    {
        $this->assertSame("a\n\n  b <ins>c</ins>", strip_tags(KunenaMessageHistory::diff("a\n\n  b ", "a\n\n  b c"), '<ins><del>'));
    }

    public function testTheTextsAreEscaped(): void
    {
        $diff = KunenaMessageHistory::diff('<b>x</b> & "y"', '<script>alert(1)</script> & "y"');

        $this->assertStringNotContainsString('<script>', $diff);
        $this->assertStringNotContainsString('<b>', $diff);
        $this->assertStringContainsString('&lt;script&gt;', $diff);
        $this->assertStringContainsString('&amp; &quot;y&quot;', $diff);
    }

    public function testFromNothingEverythingIsAdded(): void
    {
        $this->assertSame('<ins>new text</ins>', KunenaMessageHistory::diff('', 'new text'));
        $this->assertSame('<del>old text</del>', KunenaMessageHistory::diff('old text', ''));
    }

    public function testALongTextWithOneChangeIsComparedQuickly(): void
    {
        $text  = str_repeat('word ', 20000);
        $start = microtime(true);
        $diff  = KunenaMessageHistory::diff($text . 'old', $text . 'new');

        $this->assertStringEndsWith('<del>old</del><ins>new</ins>', $diff);
        $this->assertLessThan(2, microtime(true) - $start);
    }

    public function testTooBigAChangeIsShownAsRemovedAndAdded(): void
    {
        $old  = implode(' ', range(1, 1200));
        $new  = implode(' ', array_reverse(range(1, 1200)));
        $diff = KunenaMessageHistory::diff($old, $new);

        $this->assertSame('<del>' . $old . '</del><ins>' . $new . '</ins>', $diff);
    }

    public function testTheSettingKeepsTheHistoryForTheModeratorsByDefault(): void
    {
        $xml    = simplexml_load_file(Files::path('src/admin/config.xml'));
        $fields = $xml->xpath('//field[@name="editHistory"]');

        $this->assertCount(1, $fields);
        $this->assertSame((string) KunenaMessageHistory::MODERATORS, (string) $fields[0]['default']);
        $this->assertSame(['0', '1', '2'], array_map(static fn ($option): string => (string) $option['value'], $fields[0]->xpath('option')));
    }

    public function testAnEditKeepsTheVersionBeforeItWhenTheMessageIsSaved(): void
    {
        $source = Files::read('src/libraries/kunena/src/Forum/Message/KunenaMessage.php');

        $this->assertSame(1, preg_match('/public function edit\(\$fields = \[\], \$user = null\): void\s*\{(.*?)\n    \}\n/s', $source, $m));
        $this->assertLessThan(strpos($m[1], '$this->bind('), strpos($m[1], '$this->_previousVersion = '), 'The old version is not taken before the new fields.');

        $this->assertSame(1, preg_match('/public function save\(\): bool\s*\{(.*?)\n    \}\n/s', $source, $m));
        $this->assertStringContainsString('KunenaMessageHistory::record(', $m[1]);
        $this->assertLessThan(strpos($m[1], 'KunenaMessageHistory::record('), strpos($m[1], 'parent::save()'), 'The version is kept only once the message is saved.');
    }

    public function testANewInstallCreatesTheTable(): void
    {
        $this->assertMatchesRegularExpression(
            '/CREATE TABLE IF NOT EXISTS `#__kunena_messages_history`[^;]*`message_id`\s+int\(11\)\s+NOT NULL,[^;]*`message`\s+mediumtext\s+NOT NULL,[^;]*KEY `message_id` \(`message_id`\)/i',
            Files::read('src/admin/sql/install/mysql/create_tables.utf8.sql')
        );

        $xml = simplexml_load_file(Files::path('src/admin/sql/install/schema/install.xml'));

        $this->assertCount(7, $xml->xpath('//table[@name="kunena_messages_history"]/field'));
        $this->assertCount(1, $xml->xpath('//table[@name="kunena_messages_history"]/key[@name="message_id"]'));
    }

    public function testTheUpdateAndTheUninstallKnowTheTable(): void
    {
        $this->assertStringContainsString(
            'CREATE TABLE IF NOT EXISTS `#__kunena_messages_history`',
            Files::read('src/admin/sql/updates/mysql/8.0.0-DEV.sql')
        );
        $this->assertStringContainsString(
            'DROP TABLE IF EXISTS `#__kunena_messages_history`;',
            Files::read('src/admin/sql/uninstall/mysql/uninstall.mysql.utf8.sql')
        );
    }

    public static function cleanUps(): array
    {
        return [
            'a deleted message'             => ['src/libraries/kunena/src/Forum/Message/KunenaMessage.php'],
            'a deleted topic'               => ['src/libraries/kunena/src/Forum/Topic/KunenaTopic.php'],
            'the topics deleted at once'    => ['src/libraries/kunena/src/Forum/Topic/KunenaTopicHelper.php'],
            'a purged category'             => ['src/libraries/kunena/src/Forum/Category/KunenaCategory.php'],
            'a deleted Joomla user'         => ['src/plugins/plg_system_kunena/src/Extension/Kunena.php'],
            'the privacy export and remove' => ['src/plugins/plg_privacy_kunena/src/Extension/Kunena.php'],
            'the diagnostics'               => ['src/libraries/kunena/src/Forum/KunenaDiagnostics.php'],
        ];
    }

    #[DataProvider('cleanUps')]
    public function testTheHistoryIsRemovedWith(string $file): void
    {
        $this->assertStringContainsString('#__kunena_messages_history', Files::read($file));
    }
}
