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

namespace Kunena\Tests\Unit\Libraries\Kunena\Forum\Category;

use Kunena\Forum\Libraries\Forum\Category\KunenaCategory;
use Kunena\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * The alias of a category which starts with its id (the setting Category id in the alias).
 */
class KunenaCategoryAliasTest extends UnitTestCase
{
    private function category(int $id): KunenaCategory
    {
        // The category is made without its constructor: that one needs Joomla and the database.
        return self::bare(KunenaCategory::class, ['id' => $id]);
    }

    public static function aliases(): array
    {
        return [
            'a name'                    => [12, 'general', '12-general'],
            'a name with dashes'        => [3, 'off-topic-talk', '3-off-topic-talk'],
            'the id is there already'   => [12, '12-general', '12-general'],
            'an empty alias'            => [12, '', '12'],
            'the alias is the id'       => [12, '12', '12'],
            'another id in front'       => [12, '7-general', '12-7-general'],
            'a number in the name'      => [5, '2024-news', '5-2024-news'],
        ];
    }

    #[DataProvider('aliases')]
    public function testTheAliasStartsWithTheId(int $id, string $alias, string $expected): void
    {
        $this->assertSame($expected, self::invoke($this->category($id), 'withId', $alias));
    }
}
