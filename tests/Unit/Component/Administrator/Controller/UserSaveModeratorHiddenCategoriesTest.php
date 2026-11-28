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

namespace Kunena\Tests\Unit\Component\Administrator\Controller;

use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The save of a user in the backend keeps his moderator rights in the categories which its list does not show. It set the rights of
 * all the categories which the administrator administers to the choice of the list, and the list had neither the unpublished
 * categories nor the sections: any save of the user (his signature) took his right in a hidden staff category away. The list has the
 * unpublished categories now, and the sections (which it cannot have) are left as they are. (The save needs a whole site: its code
 * is read.)
 */
class UserSaveModeratorHiddenCategoriesTest extends UnitTestCase
{
    public function testTheRightsOutsideTheListAreKept(): void
    {
        $model = str_replace("\r\n", "\n", Files::read('src/admin/src/Model/UserModel.php'));

        $this->assertMatchesRegularExpression("/\\\$params = \[\n\s*'sections'\s*=> false,\n\s*'action'\s*=> 'read',\n\s*'unpublished' => 1,\n\s*\];/", $model);

        $controller = str_replace("\r\n", "\n", Files::read('src/admin/src/Controller/UserController.php'));

        $this->assertSame(1, preg_match('/protected function setModerate\(.*?\n    \}\n/s', $controller, $m));
        $this->assertMatchesRegularExpression("/if \(\\\$category->isSection\(\)\) \{\n\s*continue;\n\s*\}\n\n\s*\\\$category->setModerator\(/", $m[0]);
    }

    public function testApplyKeepsThemAsSaveDoes(): void
    {
        // Apply (user.applychanges) had a copy of the old loop, which took the rights in the sections away; only Save was fixed.
        $controller = str_replace("\r\n", "\n", Files::read('src/admin/src/Controller/UserController.php'));

        $this->assertSame(1, preg_match('/protected function saveInternal\(.*?\n    \}\n/s', $controller, $m));
        $this->assertStringNotContainsString('->setModerator(', $m[0]);
        $this->assertSame(1, substr_count($m[0], '$this->setModerate($user, $modCatids);'));
    }
}
