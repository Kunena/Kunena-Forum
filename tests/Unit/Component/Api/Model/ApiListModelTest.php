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

namespace Kunena\Tests\Unit\Component\Api\Model;

use Kunena\Forum\Api\Model\ApiListModel;
use Kunena\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * The lists of the REST API: the number of items of a page is the one the links of the pages (Joomla's pagination, which reads
 * list.limit) count with.
 */
class ApiListModelTest extends UnitTestCase
{
    public static function limits(): array
    {
        // asked for, served
        return [
            'more than the maximum' => [500, 100],
            'none (zero)'           => [0, 20],
            'a normal page'         => [30, 30],
        ];
    }

    #[DataProvider('limits')]
    public function testThePaginationCountsWithTheLimitThatIsServed(int $asked, int $served): void
    {
        $this->requireJoomla();

        $model = new class () extends ApiListModel {
            public int $loadedLimit = -1;

            public function __construct()
            {
                // Without the database and the factory of the component.
                $this->__state_set = true;
            }

            protected function load(int $start, int $limit): array
            {
                $this->loadedLimit = $limit;

                return [array_fill(0, min($limit, 250), (object) []), 250];
            }
        };

        $model->setState('list.limit', $asked);
        $model->setState('list.start', 0);

        $this->assertCount(min($served, 250), $model->getItems());
        $this->assertSame($served, $model->loadedLimit);
        // With list.limit at 500, the links "next" and "last" pointed to the offset 500 while the page had 100 items: the items 100
        // to 499 were never given. With 0, Joomla's pagination took the whole list for one page and gave no "next" link.
        $this->assertSame($served, (int) $model->getState('list.limit'));
    }
}
