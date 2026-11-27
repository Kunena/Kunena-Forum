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
 * The list of the categories of the backend: the filter "polls allowed" has its own value. The search text and that filter were
 * read into each other's variable, so the filter got the text of the search: a search also filtered on the polls, with a text
 * which is no yes or no. (The model needs the application and the session: its code is read.)
 */
class CategoriesModelFilterPollsTest extends UnitTestCase
{
    public function testEachFilterIsReadIntoItsOwnState(): void
    {
        $source = Files::read('src/admin/src/Model/CategoriesModel.php');

        $this->assertSame(1, preg_match('/protected function populateState\(.*?\n    \}\n/s', $source, $m));

        // Every filter: the value of its own request variable goes into its own state.
        preg_match_all(
            '/\$(\w+)\s*= \$this->getUserStateFromRequest\(\$this->context \. \'\.filter\.(\w+)\', \'(\w+)\'\);\s*\$this->setState\(\'filter\.(\w+)\', \$(\w+)\);/',
            $m[0],
            $filters,
            PREG_SET_ORDER
        );

        $this->assertGreaterThan(6, \count($filters));

        foreach ($filters as [, $variable, $stateKey, $request, $state, $value]) {
            $this->assertSame($variable, $value, "The value which was read for $request is the one which is set.");
            $this->assertContains($state, [$stateKey, $stateKey . 's'], "The state of $request.");
        }

        $this->assertSame(1, substr_count($m[0], "'filter_search'"), 'The search is read once.');
        $this->assertSame(1, substr_count($m[0], "setState('filter.allowPolls', \$allowPolls)"));
    }
}
