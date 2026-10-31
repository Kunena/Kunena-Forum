<?php

/**
 * Kunena Component
 *
 * Regression test:
 *
 *  - getusersmentionssearch answered every visitor, for any word (even none), with the real name and the user name of
 *    every matching account of the site, the blocked and the not activated ones too. It is a list of the members now: only
 *    for members, for a word of at least two characters, ten names at most, active accounts only, and only the name the
 *    forum shows.
 *  - categorytemplate and loadrate answered for any category and topic id, also the private ones.
 *
 * @package         Kunena.Tests
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 **/

namespace Kunena\Tests\Unit\User;

use Kunena\Forum\Libraries\User\KunenaMentionSearch;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class MentionSearchHardeningTest extends TestCase
{
    private const SRC = __DIR__ . '/../../../src';

    public static function setUpBeforeClass(): void
    {
        if (!\defined('_JEXEC')) {
            \define('_JEXEC', 1);
        }

        require_once self::SRC . '/libraries/kunena/src/User/KunenaMentionSearch.php';
    }

    // --- the word ---

    #[DataProvider('terms')]
    public function testNormaliseTerm(mixed $input, ?string $expected): void
    {
        $this->assertSame($expected, KunenaMentionSearch::normaliseTerm($input), var_export($input, true));
    }

    public static function terms(): array
    {
        return [
            'a normal word'              => ['jan', 'jan'],
            'exactly the minimum'        => ['ja', 'ja'],
            'one character'              => ['j', null],
            'nothing'                    => ['', null],
            'only spaces'                => ['     ', null],
            'one character with spaces'  => ['  j  ', null],
            'surrounding spaces'         => ['  jan  ', 'jan'],
            'control characters'         => ["ja\0n", 'jan'],
            'only control characters'    => ["\n\t\0", null],
            'a character and a control'  => ["j\n", null],
            'null'                       => [null, null],
            'an array'                   => [['jan'], null],
            'a number'                   => [123, null],
            'unicode, two characters'    => ['éé', 'éé'],
            'unicode, one character'     => ['é', null],
            'percent sign stays a text'  => ['100%', '100%'],
            'underscore stays a text'    => ['a_b', 'a_b'],
        ];
    }

    public function testALongWordIsCut(): void
    {
        $term = KunenaMentionSearch::normaliseTerm(str_repeat('é', 500));

        $this->assertSame(KunenaMentionSearch::MAX_LENGTH, mb_strlen($term, 'UTF-8'));
    }

    // --- the answer ---

    public function testOnlyTheNameThatTheForumShowsIsGiven(): void
    {
        $rows = [
            (object) ['name' => 'Jane Doe', 'username' => 'jane', 'email' => 'jane@example.com', 'id' => 5, 'password' => 'x'],
            ['name' => 'John Roe', 'username' => 'john'],
        ];

        $this->assertSame([['name' => 'jane'], ['name' => 'john']], KunenaMentionSearch::formatResults($rows, true), 'user names');
        $this->assertSame([['name' => 'Jane Doe'], ['name' => 'John Roe']], KunenaMentionSearch::formatResults($rows, false), 'names');

        $json = json_encode(KunenaMentionSearch::formatResults($rows, true));

        foreach (['Jane Doe', 'John Roe', 'example.com', 'password', 'email'] as $secret) {
            $this->assertStringNotContainsString($secret, $json, 'The real name must not be told when the forum shows user names.');
        }
    }

    public function testEachNameOnceAndEmptyNamesDropped(): void
    {
        $rows = [
            ['name' => 'A', 'username' => 'ann'],
            ['name' => 'B', 'username' => 'ann'],
            ['name' => 'C', 'username' => ''],
            ['name' => 'D', 'username' => '   '],
            ['name' => 'E'],
        ];

        $this->assertSame([['name' => 'ann']], KunenaMentionSearch::formatResults($rows, true));
    }

    public function testNotMoreThanTheLimit(): void
    {
        $rows = [];

        for ($i = 0; $i < 100; $i++) {
            $rows[] = ['name' => "N$i", 'username' => "user$i"];
        }

        $this->assertCount(KunenaMentionSearch::LIMIT, KunenaMentionSearch::formatResults($rows, true));
        $this->assertSame(10, KunenaMentionSearch::LIMIT);
        $this->assertSame([], KunenaMentionSearch::formatResults([], true));
    }

    public function testTheAnswerKeepsTheShapeOfTheScript(): void
    {
        // The script of the form reads the field "name" of every item (lookup and fillAttr) and treats everything but a 200 (a 403 included) as no result.
        $script = file_get_contents(self::SRC . '/site/template/aurelia/assets/js/search.js');

        $this->assertStringContainsString("lookup: 'name'", $script);
        $this->assertStringContainsString("fillAttr: 'name'", $script);
        $this->assertStringContainsString('xhr.status === 200', $script);
        $this->assertStringContainsString('cb([]);', $script, 'Any other status, 403 included, is no result.');

        foreach (KunenaMentionSearch::formatResults([['name' => 'x', 'username' => 'jane']], true) as $item) {
            $this->assertSame(['name'], array_keys($item));
        }
    }

    // --- the controller ---

    private function source(string $file): string
    {
        return file_get_contents(self::SRC . '/' . $file);
    }

    private function method(string $source, string $name): string
    {
        $this->assertSame(1, preg_match('/public function ' . preg_quote($name, '/') . '\(\)\s*\{(.*?)\n    \}\n/s', $source, $m), $name);

        return $m[1];
    }

    public function testGuestsGetA403AndNoQuery(): void
    {
        $body = $this->method($this->source('site/src/Controllers/UserController.php'), 'getusersmentionssearch');

        $this->assertMatchesRegularExpression('/if \(!\$this->me->exists\(\)\) \{[^}]*\$status = 403;/s', $body);
        $this->assertLessThan(strpos($body, 'createQuery()'), strpos($body, '$this->me->exists()'), 'No query before the login is checked.');
        $this->assertStringContainsString('http_response_code($status)', $body);
        $this->assertLessThan(strpos($body, 'echo json_encode'), strpos($body, 'http_response_code($status)'));
    }

    public function testTheQueryIsLimitedAndOnlyForActiveAccounts(): void
    {
        $body = $this->method($this->source('site/src/Controllers/UserController.php'), 'getusersmentionssearch');

        $this->assertStringContainsString('KunenaMentionSearch::normaliseTerm(', $body);
        $this->assertStringContainsString('} elseif ($term !== null) {', $body, 'A word that is too short does not search.');
        $this->assertStringContainsString("quoteName('block') . ' = 0'", $body, 'No blocked accounts.');
        $this->assertStringContainsString("quoteName('activation')", $body, 'No accounts which are not activated.');
        $this->assertStringContainsString('->setLimit(KunenaMentionSearch::LIMIT)', $body);
        $this->assertStringContainsString("escape(\$term, true) . '%'", $body, 'The word is the start of a name, with its wildcards escaped.');
        $this->assertDoesNotMatchRegularExpression("/'%'\s*\.\s*\\\$db->escape/", $body, 'No search inside the name.');
    }

    public function testTheSearchUsesTheNameThatIsShown(): void
    {
        $body = $this->method($this->source('site/src/Controllers/UserController.php'), 'getusersmentionssearch');

        $this->assertStringContainsString("\$useUsername = (bool) \$this->config->username;", $body);
        $this->assertStringContainsString("\$column      = \$useUsername ? 'username' : 'name';", $body);
        $this->assertStringContainsString('KunenaMentionSearch::formatResults((array) $db->loadObjectList(), $useUsername)', $body);
        $this->assertDoesNotMatchRegularExpression('/echo json_encode\(\$row\);.*loadObjectList/s', $body);
        $this->assertSame(1, substr_count($body, 'loadObjectList'), 'The rows are not sent as they come from the database.');
    }

    public function testCategoryTemplateIsOnlyForReadableCategories(): void
    {
        $body = $this->method($this->source('site/src/Controllers/TopicController.php'), 'categorytemplate');

        $read = strpos($body, "\$category->isAuthorised('read')");
        $send = strpos($body, '$category->topictemplate');

        $this->assertNotFalse($read, 'categorytemplate needs a read check.');
        $this->assertNotFalse($send);
        $this->assertLessThan($send, $read, 'Check before the template text is used.');
        $this->assertMatchesRegularExpression("/isAuthorised\\('read'\\)\\) \\{\\s*throw new RuntimeException\\(Text::_\\('Forbidden'\\), 403\\);/", $body);
        $this->assertStringContainsString("\$template = '';", $body, 'No category chosen yet is an empty template.');
        $this->assertSame(1, substr_count($body, '$category->topictemplate'), 'The text is only read behind the check.');
    }

    public function testLoadRateIsOnlyForReadableTopics(): void
    {
        $body = $this->method($this->source('site/src/Controllers/TopicController.php'), 'loadrate');

        $read = strpos($body, "\$topic->isAuthorised('read')");

        $this->assertNotFalse($read, 'loadrate needs a read check.');
        $this->assertLessThan(strpos($body, 'KunenaRateHelper::getSelected('), $read);
        $this->assertLessThan(strpos($body, 'KunenaRateHelper::getRate('), $read);
        $this->assertMatchesRegularExpression("/!\\\$topic->exists\\(\\) \\|\\| !\\\$topic->isAuthorised\\('read'\\)\\) \\{\\s*throw new RuntimeException\\(Text::_\\('Forbidden'\\), 403\\);/", $body);
    }
}
