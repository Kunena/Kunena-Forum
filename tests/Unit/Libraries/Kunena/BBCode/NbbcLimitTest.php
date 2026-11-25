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

namespace Kunena\Tests\Unit\Libraries\Kunena\BBCode;

use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;
use Nbbc\BBCode;
use Nbbc\BBCodeLexer;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * The limit of the length of the parser Nbbc, which makes the excerpts of the posts (the tooltips of the links, the description
 * of a topic): a text which starts with a tag or a space was never cut (the guess of its length counted only the text before the
 * first tag), and a text without a space where it could be cut became "..." only (a post in Chinese, a long address).
 */
class NbbcLimitTest extends UnitTestCase
{
    protected function setUp(): void
    {
        if (!\defined('_JEXEC')) {
            \define('_JEXEC', 1);
        }

        spl_autoload_register(static function ($class) {
            if (strpos($class, 'Nbbc\\') === 0) {
                $file = Files::path('src/libraries/kunena/External/Nbbc/src/' . substr($class, 5) . '.php');

                if (is_file($file)) {
                    require_once $file;
                }
            }
        });
    }

    private static function plain(string $text, int $limit): string
    {
        $parser = new BBCode();
        $parser->SetPlainMode(true);
        $parser->SetDetectURLs(false);
        $parser->SetEnableSmileys(false);
        $parser->SetLimit($limit);

        return (string) $parser->Parse($text);
    }

    public static function guesses(): array
    {
        return [
            'text only'              => ['one two three', 13],
            'a tag first'            => ['[b]bold[/b] one two three', 18],
            'a space first'          => ["\none two three", 14],
            'tags are not counted'   => ['a [i]b[/i] c', 5],
        ];
    }

    #[DataProvider('guesses')]
    public function testTheGuessCountsTheWholeText(string $text, int $length): void
    {
        $this->assertSame($length, (new BBCodeLexer($text))->guessTextLength());
    }

    public static function longTexts(): array
    {
        $words = 'one two three four five six seven eight nine ten eleven twelve';

        return [
            'text only'     => [$words],
            'a tag first'   => ['[b]bold[/b] ' . $words],
            'a space first' => [' ' . $words],
            'a line first'  => ["\n" . $words],
        ];
    }

    #[DataProvider('longTexts')]
    public function testALongTextIsCut(string $text): void
    {
        // The plain mode keeps <b> (stripBBCode() takes the tags out afterwards).
        $excerpt = strip_tags(self::plain($text, 20));

        $this->assertStringEndsWith('...', $excerpt);
        $this->assertLessThanOrEqual(23, \strlen($excerpt), $excerpt);
    }

    public function testATextWithoutASpaceIsCutAtTheLimit(): void
    {
        $text    = str_repeat('这是一个很长的中文消息没有空格', 10);
        $excerpt = self::plain($text, 200);

        $this->assertSame(mb_strcut($text, 0, 200, 'UTF-8') . '...', $excerpt);
        $this->assertTrue(mb_check_encoding($excerpt, 'UTF-8'));
        $this->assertSame(198, \strlen($excerpt) - 3, 'The cut is on a whole character: 66 characters of 3 bytes.');
    }

    public function testALongWordIsCutAtTheLimit(): void
    {
        $this->assertSame('Supercalifragilistic...', self::plain('Supercalifragilisticexpialidocious and more words after it', 20));
    }

    public function testATextIsStillCutAtASpace(): void
    {
        $this->assertSame('one two three four...', self::plain('one two three four five six seven eight nine ten eleven twelve', 20));
    }

    /**
     * A [code] block longer than the limit: the search for its [/code] stopped at the limit and left $end_tag_params
     * undefined (a warning on the page of the topic, where the description is an excerpt of the post).
     */
    public function testACodeBlockLongerThanTheLimitIsCutWithoutAWarning(): void
    {
        $parser = new BBCode();
        $parser->SetLimit(20);
        $html = (string) $parser->Parse('[code]' . str_repeat('echo "x"; ', 20) . '[/code] after');

        $this->assertStringContainsString('echo', $html);
        $this->assertStringNotContainsString('after', $html);
    }
}
