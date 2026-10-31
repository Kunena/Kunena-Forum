<?php

/**
 * Kunena Component
 *
 * Regression test: when a user may use none of the requested categories, the message and topic helpers must return
 * nothing. They used to drop the category restriction, which returned the messages and topics of every category
 * (unapproved, deleted and private ones too).
 *
 * @package         Kunena.Tests
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 **/

namespace Kunena\Tests\Unit\Forum;

use PHPUnit\Framework\TestCase;

class EmptyCategoryFallbackTest extends TestCase
{
    private const MESSAGES = __DIR__ . '/../../../src/libraries/kunena/src/Forum/Message/KunenaMessageHelper.php';
    private const TOPICS   = __DIR__ . '/../../../src/libraries/kunena/src/Forum/Topic/KunenaTopicHelper.php';

    private function method(string $file, string $name): string
    {
        $source = file_get_contents($file);

        $found = preg_match(
            '/function ' . preg_quote($name, '/') . '\([^)]*\)[^{]*\{(.*?)(?=\n    (?:\/\*\*|public static function|public function|protected static function))/s',
            $source,
            $m
        );

        $this->assertSame(1, $found, "Method $name not found.");

        return $m[1];
    }

    public function testMessagesNeverFallBackToNoCategoryFilter(): void
    {
        $body = $this->method(self::MESSAGES, 'getLatestMessages');

        $this->assertStringNotContainsString("\$catlist = ''", $body, 'An empty list must not turn into "no filter".');
    }

    public function testMessagesReturnNothingWithoutCategories(): void
    {
        $body = $this->method(self::MESSAGES, 'getLatestMessages');

        $this->assertMatchesRegularExpression('/if \(!\$catlist\) \{\s*return \[0, \[\]\];\s*\}/', $body);
    }

    public function testMessagesAlwaysRestrictToTheCategories(): void
    {
        $body = $this->method(self::MESSAGES, 'getLatestMessages');

        $return = strpos($body, 'if (!$catlist)');
        $filter = strpos($body, "'m.catid') . ' IN ('");

        $this->assertNotFalse($filter, 'The category restriction must be unconditional.');
        $this->assertLessThan($filter, $return, 'Return before the restriction can be skipped.');

        // The restriction may not sit inside an else/if branch any more.
        $this->assertDoesNotMatchRegularExpression('/if \(\\\\count\(\$catlist\) > 0\)/', $body);
    }

    public function testTopicsNeverFallBackToNoCategoryFilter(): void
    {
        $body = $this->method(self::TOPICS, 'getLatestTopics');

        $this->assertStringNotContainsString("\$catlist = '';", $body);
        $this->assertDoesNotMatchRegularExpression('/if \(\\\\count\(\$catlist\) > 0\)/', $body);
    }

    public function testTopicsReturnNothingWithoutCategories(): void
    {
        $body = $this->method(self::TOPICS, 'getLatestTopics');

        $this->assertMatchesRegularExpression('/if \(!\$catlist\) \{.*?return \[0, \[\]\];\s*\}/s', $body);
        $this->assertLessThan(
            strpos($body, "'tt.category_id') . ' IN ('"),
            strpos($body, 'if (!$catlist)'),
            'Check the list before the restriction is built.'
        );
    }

    public function testTopicsExcludeModeStillLimitsToReadableCategories(): void
    {
        $body = $this->method(self::TOPICS, 'getLatestTopics');

        // A NOT IN on its own would show the categories which the user may not read.
        $this->assertStringNotContainsString("' NOT IN ('", $body);
        $this->assertStringContainsString('KunenaCategoryHelper::getCategories(false)', $body);
        $this->assertStringContainsString('array_diff_key($readable, $catlist)', $body);
    }

    public function testCategoryIdsAreCastToIntegers(): void
    {
        $this->assertStringContainsString("array_map('intval', array_keys(\$catlist))", $this->method(self::MESSAGES, 'getLatestMessages'));
        $this->assertStringContainsString("array_map('intval', array_keys(\$catlist))", $this->method(self::TOPICS, 'getLatestTopics'));
    }
}
