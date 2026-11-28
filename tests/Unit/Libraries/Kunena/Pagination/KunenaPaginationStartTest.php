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

namespace Kunena\Tests\Unit\Libraries\Kunena\Pagination;

use Kunena\Forum\Libraries\Pagination\KunenaPagination;
use Kunena\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * A start which is not the start of a page (an old link after a change of the number of items of a page: start=25 with 20 a page)
 * is the start of its page: the lists showed items 26 to 45 while the next page began at 41, and the number of the current page was
 * not the one of its link.
 */
class KunenaPaginationStartTest extends UnitTestCase
{
    public static function starts(): array
    {
        return [
            'in the second page'         => [100, 25, 20, 20, 2],
            'with 15 a page'             => [100, 20, 15, 15, 2],
            'the start of a page'        => [100, 40, 20, 40, 3],
            'the first page'             => [100, 0, 20, 0, 1],
            'past the end'               => [100, 500, 20, 80, 5],
        ];
    }

    #[DataProvider('starts')]
    public function testTheStartIsTheStartOfItsPage(int $total, int $start, int $limit, int $expected, int $page): void
    {
        $pagination = new KunenaPagination($total, $start, $limit);

        $this->assertSame($expected, $pagination->limitstart);
        $this->assertEquals($page, $pagination->pagesCurrent);
    }
}
