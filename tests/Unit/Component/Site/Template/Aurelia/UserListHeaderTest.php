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

namespace Kunena\Tests\Unit\Component\Site\Template\Aurelia;

use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The header of the user list of Aurelia: the column of the real name (the list without usernames) showed a "-->" left of an
 * old comment, and the cell of the e-mail was not closed. Every header cell is closed, and no comment end is left.
 */
class UserListHeaderTest extends UnitTestCase
{
    public function testTheHeaderCellsAreClosedAndHaveNoCommentEnd(): void
    {
        $layout = Files::read('src/site/template/aurelia/layouts/user/list/default.php');

        $this->assertSame(1, preg_match('/<thead>(.*?)<\/thead>/s', $layout, $m));
        $this->assertStringNotContainsString('-->', $m[1]);
        $this->assertSame(substr_count($m[1], '<th '), substr_count($m[1], '</th>'));
    }
}
