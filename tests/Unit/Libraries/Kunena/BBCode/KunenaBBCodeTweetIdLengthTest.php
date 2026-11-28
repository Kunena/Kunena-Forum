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
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * The ids of X have 19 digits since 2022 (1850000000000000000): the [tweet] tag and the task which loads a tweet refused every id
 * longer than 18 digits, so no tweet of the last years was shown. An id is now any integer of PHP (the cache and renderTweet() take an
 * int), not more: a longer one would become PHP_INT_MAX. (The tag and the task need a whole site: the check is read and run.)
 */
class KunenaBBCodeTweetIdLengthTest extends UnitTestCase
{
    private const FILES = [
        'tag'  => ['src/libraries/kunena/src/BBCode/KunenaBBCode.php', '$tweetid'],
        'task' => ['src/site/src/Controllers/MiscController.php', '$id'],
    ];

    public static function ids(): array
    {
        return [
            'an old id, 18 digits'                => ['585000000000000000', true],
            'an id of 2024, 19 digits'            => ['1850000000000000000', true],
            'the largest integer'                 => [(string) PHP_INT_MAX, true],
            'one more than the largest integer'   => ['9223372036854775808', false],
            '20 digits'                           => ['18500000000000000000', false],
            'letters'                             => ['12ab', false],
            'empty'                               => ['', false],
        ];
    }

    #[DataProvider('ids')]
    public function testTheIdsOfTheTagAndTheTask(string $id, bool $accepted): void
    {
        foreach (self::FILES as $what => [$file, $variable]) {
            $source = Files::read($file);
            $this->assertSame(1, preg_match('/if \(!ctype_digit\(' . preg_quote($variable, '/') . '\)(.*?)(?: \|\| !hash_equals|\) \{)/', $source, $matches), $what);

            // The rest of the condition after ctype_digit(), run on the id.
            $rest  = str_replace($variable, '$value', $matches[1]);
            $check = eval('return static function (string $value): bool { return !ctype_digit($value)' . $rest . '; };');

            $this->assertSame(!$accepted, $check($id), $what);
        }
    }
}
