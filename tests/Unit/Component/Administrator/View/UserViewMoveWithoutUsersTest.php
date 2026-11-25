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

namespace Kunena\Tests\Unit\Component\Administrator\View;

use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * Users > Move messages opened by its address, without users ticked in the list: the page of one user was prepared and the
 * layout showed PHP warnings for the list of categories and the users it did not have. The request goes back to the list.
 */
class UserViewMoveWithoutUsersTest extends UnitTestCase
{
    public function testTheLayoutMoveWithoutUsersGoesBackToTheList(): void
    {
        $source = Files::read('src/admin/src/View/User/HtmlView.php');

        $withUsers = strpos($source, 'if ($userids && $this->getLayout() == \'move\') {');
        $without   = strpos($source, 'if ($this->getLayout() == \'move\') {');
        $user      = strpos($source, '$this->user         = $model->getUser();');

        $this->assertIsInt($withUsers);
        $this->assertIsInt($without, 'The layout without users is handled.');
        $this->assertIsInt($user);
        $this->assertGreaterThan($withUsers, $without);
        $this->assertLessThan($user, $without, 'Before the page of one user is prepared.');
        $this->assertStringContainsString('$app->redirect(\'index.php?option=com_kunena&view=users\');', substr($source, $without, $user - $without));
    }
}
