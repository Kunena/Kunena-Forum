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
 * The list of the categories of a moderator on the page of a user in the backend selects the categories of his rows only. A user
 * without a row had a 0 selected for "none", and 0 is the value of "Global moderator": a save of the user (his signature, with
 * "moderator" on, which it is for a member of a moderator group) made him a global moderator. (The list needs the HTML helpers of a
 * site: its code is read.)
 */
class UserModelModeratorListTest extends UnitTestCase
{
    public function testAUserWithoutARowHasNothingSelected(): void
    {
        $source = str_replace("\r\n", "\n", Files::read('src/admin/src/Model/UserModel.php'));

        $this->assertSame(1, preg_match('/public function getListModCats\(\)\s*\{(.*?)\n    \}\n/s', $source, $m));
        $this->assertStringNotContainsString('$modCatList[] = 0;', $m[1]);

        // The global moderator is the row of the category 0, which the query gives.
        $this->assertStringContainsString("HTMLHelper::_('select.option', 0, Text::_('COM_KUNENA_GLOBAL_MODERATOR'))", $m[1]);
    }
}
