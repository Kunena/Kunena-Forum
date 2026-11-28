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

use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The channels "CHILDREN" of a category are its children without a check in the list of all the channels ('none'), as the other
 * channels are: the action was given as the key of the option ([$action => 'none']), so the default check (what the visitor may read)
 * left out the children he may not read from the list which is kept for every action. (The channels need the categories of a site:
 * the code is read.)
 */
class KunenaCategoryChannelsChildrenTest extends UnitTestCase
{
    public function testTheChildrenAreTakenWithoutACheck(): void
    {
        $source = Files::read('src/libraries/kunena/src/Forum/Category/KunenaCategory.php');

        $this->assertStringContainsString("KunenaCategoryHelper::getChildren(\$this->id, 1, ['action' => 'none'])", $source);
        $this->assertStringNotContainsString("[\$action => 'none']", $source);
    }
}
