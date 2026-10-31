<?php

/**
 * Kunena Component
 *
 * Regression test for the search "messages of this user". It compared the typed name with m.name, the name which is typed at the
 * time of the post: the messages of a guest who signed with the name of a member were found as messages of that member, and a
 * member who was renamed was not found by the new name.
 *
 * @package         Kunena.Tests
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 **/

namespace Kunena\Tests\Unit\Forum;

use Kunena\Forum\Libraries\Forum\Message\KunenaSearchUserFilter;
use PHPUnit\Framework\TestCase;

class SearchByUserFilterTest extends TestCase
{
    private const MODEL = __DIR__ . '/../../../src/site/src/Model/SearchModel.php';

    public static function setUpBeforeClass(): void
    {
        if (!\defined('_JEXEC')) {
            \define('_JEXEC', 1);
        }

        require_once __DIR__ . '/../../../src/libraries/kunena/src/Forum/Message/KunenaSearchUserFilter.php';
    }

    public function testMessagesOfAnAccountAreFoundByTheUserIdNotByTheTypedName(): void
    {
        $sql = KunenaSearchUserFilter::condition("'%jelle%'");

        $this->assertStringContainsString("m.userid IN (SELECT su.id FROM #__users AS su WHERE su.name LIKE '%jelle%' OR su.username LIKE '%jelle%')", $sql);
    }

    public function testTypedNameIsOnlyUsedForGuests(): void
    {
        $sql = KunenaSearchUserFilter::condition("'%jelle%'");

        $this->assertStringContainsString("(m.userid = 0 AND m.name LIKE '%jelle%'", $sql);
        $this->assertSame(1, substr_count($sql, 'm.name LIKE'), 'm.name is compared once, inside the guest branch.');
    }

    public function testGuestSignedWithTheNameOfAnAccountIsNotAMessageOfThatAccount(): void
    {
        $sql = KunenaSearchUserFilter::condition("'jelle'");

        $this->assertStringContainsString('NOT EXISTS (SELECT 1 FROM #__users AS gu WHERE gu.name = m.name OR gu.username = m.name)', $sql);
    }

    public function testConditionIsOneBracketedTermAndBalanced(): void
    {
        $sql = KunenaSearchUserFilter::condition("'%a%'");

        $this->assertSame(substr_count($sql, '('), substr_count($sql, ')'));
        $this->assertStringStartsWith('(m.userid IN', $sql);
        $this->assertStringEndsWith('))', $sql);
    }

    public function testPrefixOfTheTablesIsKept(): void
    {
        $this->assertStringContainsString('FROM jos_users AS su', KunenaSearchUserFilter::condition("'x'", 'jos_'));
    }

    public function testOperandIsUsedAsGiven(): void
    {
        // The model has escaped it for the database; the filter must neither change nor add to it.
        $sql = KunenaSearchUserFilter::condition("'a\\_b%'");

        $this->assertSame(3, substr_count($sql, "'a\\_b%'"));
    }

    public function testModelUsesTheFilterAndNoLongerComparesTheNameDirectly(): void
    {
        $source = file_get_contents(self::MODEL);

        $this->assertStringContainsString('KunenaSearchUserFilter::condition($like)', $source);
        $this->assertDoesNotMatchRegularExpression('/\$querystrings\[\]\s*=\s*"m\.name LIKE/', $source);
        $this->assertStringContainsString('$db->escape($username, true)', $source, 'The name is still escaped, wildcards included.');
    }
}
