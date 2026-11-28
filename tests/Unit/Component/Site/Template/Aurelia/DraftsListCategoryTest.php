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
 * The list of the drafts names the category of a draft only when the user may still read it (as the link of its topic, of the
 * topics which he may read): the name of a category which he lost access to was shown. (Agora uses this layout.)
 */
class DraftsListCategoryTest extends UnitTestCase
{
    public function testTheCategoryIsNamedOnlyWhenTheUserMayReadIt(): void
    {
        $layout = Files::read('src/site/template/aurelia/layouts/topic/drafts/default.php');

        $this->assertStringContainsString("\$category->exists() && \$category->isAuthorised('read') ? \$category->displayField('name') : ''", $layout);
        $this->assertFileDoesNotExist(Files::path('src/site/template/agora/layouts/topic/drafts/default.php'));
    }
}
