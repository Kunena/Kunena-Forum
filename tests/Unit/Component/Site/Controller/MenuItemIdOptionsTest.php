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

namespace Kunena\Tests\Unit\Component\Site\Controller;

use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The menu item options of "SEF redirect" go to their own pages: the page of the help (misc) read customId, which no option has, so
 * "MISC ID" did nothing, and the form of a new topic took "SEARCH ID", so it was sent to the menu item of the search. (The pages
 * need a whole site: their code is read.)
 */
class MenuItemIdOptionsTest extends UnitTestCase
{
    public function testTheHelpPageReadsMiscId(): void
    {
        $source = Files::read('src/site/src/Controller/Application/Misc/Initial/MiscDisplay.php');

        $this->assertStringContainsString('$itemid = $this->config->miscId;', $source);
        $this->assertStringNotContainsString('customId', $source);
        $this->assertStringContainsString('name="miscId"', Files::read('src/admin/config.xml'));
    }

    public function testTheNewTopicFormDoesNotTakeTheSearchId(): void
    {
        $this->assertStringNotContainsString('searchId', Files::read('src/site/src/Controller/Topic/Form/Create/TopicFormCreateDisplay.php'));
    }
}
