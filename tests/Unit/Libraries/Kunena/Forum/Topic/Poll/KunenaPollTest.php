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

namespace Kunena\Tests\Unit\Libraries\Kunena\Forum\Topic\Poll;

use Kunena\Forum\Libraries\Forum\Topic\Poll\KunenaPoll;
use Kunena\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * The new options of a poll are created in the order they were entered (issue #1041).
 */
class KunenaPollTest extends UnitTestCase
{
    /**
     * The submitted options keyed by the given prefix and 1 to $count, with the texts 'Option 1' to 'Option <count>'.
     */
    private static function numbered(string $prefix, int $count): array
    {
        $options = [];

        for ($i = 1; $i <= $count; $i++) {
            $options[$prefix === '' ? $i : $prefix . $i] = 'Option ' . $i;
        }

        return $options;
    }

    public static function polls(): array
    {
        $texts = static fn (int $count): array => array_values(self::numbered('', $count));

        return [
            'twelve new options of a new poll' => [
                self::numbered('new', 12),
                [],
                [
                    'Option 1', 'Option 2', 'Option 3', 'Option 4', 'Option 5', 'Option 6',
                    'Option 7', 'Option 8', 'Option 9', 'Option 10', 'Option 11', 'Option 12',
                ],
            ],
            'ten new options of an existing poll' => [
                [57 => 'A', 58 => 'B'] + self::numbered('new', 10),
                [57 => (object) ['text' => 'A'], 58 => (object) ['text' => 'B']],
                $texts(10),
            ],
            'integer keys of SCEditor on a new poll' => [self::numbered('', 11), [], $texts(11)],
            'an empty option is left out'            => [
                ['new1' => 'Option 1', 'new2' => '', 'new3' => 'Option 3'],
                [],
                ['Option 1', 'Option 3'],
            ],
        ];
    }

    #[DataProvider('polls')]
    public function testGetOptionsToCreate(array $newOptions, array $existing, array $expected): void
    {
        $poll = self::bare(KunenaPoll::class);

        $this->assertSame($expected, self::invoke($poll, 'getOptionsToCreate', $newOptions, $existing));
    }

    public function testNew2ComesBeforeNew10(): void
    {
        $created = self::invoke(self::bare(KunenaPoll::class), 'getOptionsToCreate', self::numbered('new', 12), []);

        $this->assertLessThan(array_search('Option 10', $created, true), array_search('Option 2', $created, true));
    }
}
