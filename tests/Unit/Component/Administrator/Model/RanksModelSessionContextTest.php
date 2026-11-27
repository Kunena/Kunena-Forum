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

namespace Kunena\Tests\Unit\Component\Administrator\Model;

use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The list of the ranks of the backend: the filters are kept in the session under the context of the list. The dot before "filter"
 * in the key was missing (com_kunena.admin.ranksfilter.search): the search and the other filters were forgotten on every new visit.
 */
class RanksModelSessionContextTest extends UnitTestCase
{
    public function testEachFilterUsesTheContextOfTheList(): void
    {
        $source = Files::read('src/admin/src/Model/RanksModel.php');

        $this->assertSame(1, preg_match('/protected function populateState\(.*?\n    \}\n/s', $source, $m));

        preg_match_all(
            '/getUserStateFromRequest\(\$this->context \. \'([^\']+)\'/',
            $m[0],
            $keys,
            PREG_SET_ORDER
        );

        $this->assertCount(4, $keys);

        foreach ($keys as [, $suffix]) {
            $this->assertStringStartsWith('.filter.', $suffix, 'The session key of a filter.');
        }
    }
}