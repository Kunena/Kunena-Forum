<?php

/**
 * Kunena Component
 *
 * Regression test for the search and the hidden parts of a message. [hide] is for members, [confidential] for the author and the
 * moderators, [private] for nobody. The search compared its words with the whole text, so a visitor could find out what a hidden part
 * says: a search for a word finds the message when the word is in the hidden part (and so does the number of results, and "-word"
 * leaving a message out). Those messages are not searched in their text by the ones who may not read it.
 *
 * @package         Kunena.Tests
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 **/

namespace Kunena\Tests\Unit\Forum;

use Kunena\Forum\Libraries\Forum\Message\KunenaSearchHiddenText as H;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class HiddenTextSearchTest extends TestCase
{
    private const MODEL = __DIR__ . '/../../../src/site/src/Model/SearchModel.php';

    public static function setUpBeforeClass(): void
    {
        if (!\defined('_JEXEC')) {
            \define('_JEXEC', 1);
        }

        require_once __DIR__ . '/../../../src/libraries/kunena/src/Forum/Message/KunenaSearchHiddenText.php';
    }

    public function testGuestDoesNotSearchHiddenOrConfidentialOrPrivateText(): void
    {
        $sql = H::searchableCondition(false, false, 0);

        $this->assertStringContainsString("t.message NOT LIKE '%[hide%'", $sql);
        $this->assertStringContainsString("t.message NOT LIKE '%[confidential%'", $sql);
        $this->assertStringContainsString("t.message NOT LIKE '%[private%'", $sql);
        $this->assertStringNotContainsString('m.userid', $sql, 'A guest is no author of everything that guests wrote.');
    }

    public function testMemberSearchesHideButNotTheConfidentialTextOfOthers(): void
    {
        $sql = H::searchableCondition(true, false, 42);

        $this->assertStringNotContainsString('[hide', $sql, 'A member may read [hide].');
        $this->assertStringContainsString("(t.message NOT LIKE '%[confidential%' OR m.userid = 42)", $sql, 'Their own messages are searched.');
        $this->assertStringContainsString("t.message NOT LIKE '%[private%'", $sql);
    }

    public function testGlobalModeratorSearchesEverythingBut_Private(): void
    {
        $sql = H::searchableCondition(true, true, 7);

        $this->assertSame("(t.message NOT LIKE '%[private%')", $sql);
    }

    public function testUserIdIsAnIntegerInTheQuery(): void
    {
        $this->assertStringContainsString('m.userid = 5)', H::searchableCondition(true, false, (int) '5; DROP TABLE x'));
        $this->assertStringNotContainsString(';', H::searchableCondition(true, false, (int) "1' OR '1'='1"));
    }

    public function testMemberWithoutAnIdIsTreatedLikeAGuestForConfidentialText(): void
    {
        $this->assertStringNotContainsString('m.userid', H::searchableCondition(true, false, 0));
    }

    public function testConditionIsOneBracketedTermAndBalanced(): void
    {
        foreach ([[false, false, 0], [true, false, 9], [true, true, 9]] as $args) {
            $sql = H::searchableCondition(...$args);

            $this->assertSame(substr_count($sql, '('), substr_count($sql, ')'));
            $this->assertStringStartsWith('(', $sql);
            $this->assertStringEndsWith(')', $sql);
        }
    }

    public function testWordIsFoundInTheTextOnlyWhereTheTextMayBeSearched(): void
    {
        $searchable = H::searchableCondition(false, false, 0);
        $sql        = H::wordCondition("'%secret%'", false, $searchable);

        $this->assertSame("((t.message LIKE '%secret%' AND {$searchable}) OR m.subject LIKE '%secret%')", $sql);
    }

    public function testWordToLeaveOutUsesTheSameRule(): void
    {
        $searchable = H::searchableCondition(false, false, 0);
        $sql        = H::wordCondition("'%secret%'", true, $searchable);

        $this->assertSame("(NOT (t.message LIKE '%secret%' AND {$searchable}) AND m.subject NOT LIKE '%secret%')", $sql);
        $this->assertStringNotContainsString("t.message NOT LIKE '%secret%'", $sql, 'A plain NOT LIKE would tell which messages hold the word in a hidden part.');
    }

    public static function operands(): array
    {
        return [["'%a%'"], ["'%a\\_b\\%%'"], ["'%x\\'y%'"]];
    }

    #[DataProvider('operands')]
    public function testOperandIsUsedAsGiven(string $like): void
    {
        $searchable = H::searchableCondition(true, false, 3);

        $this->assertSame(2, substr_count(H::wordCondition($like, false, $searchable), $like));
        $this->assertSame(2, substr_count(H::wordCondition($like, true, $searchable), $like));
    }

    public function testModelUsesTheRuleForEveryWordAndNotTheOldComparison(): void
    {
        $source = file_get_contents(self::MODEL);

        $this->assertStringContainsString('KunenaSearchHiddenText::searchableCondition($me->exists(), $me->isModerator(), (int) $me->userid)', $source);
        $this->assertStringContainsString('KunenaSearchHiddenText::wordCondition(', $source);
        $this->assertStringNotContainsString("(t.message {\$not} LIKE", $source, 'The text is no longer compared with the whole message.');
        $this->assertDoesNotMatchRegularExpression('/t\.message\s+\{\$not\}\s+LIKE/', $source);
    }

    public function testTheModelDoesNotUseAGlobalModeratorForTheCategoryModeratorCase(): void
    {
        // isModerator() without a category is the global moderator (and the administrator): a moderator of one category can not
        // read [confidential] in the others, so the text of those messages stays out of their search.
        $source = file_get_contents(self::MODEL);

        $this->assertStringNotContainsString('isModerator($category', substr($source, strpos($source, 'protected function buildWhere')));
    }
}
