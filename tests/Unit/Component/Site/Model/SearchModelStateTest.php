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

namespace Kunena\Tests\Unit\Component\Site\Model;

use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The options of the search survive the way from the form to the page of the results (the form is posted, the results are an
 * address with the values which are not the defaults):
 * - the search box of a topic sends ids[]=<topic>: the value was made a number, a list is 1, and the topic 1 was searched;
 * - "Search in subcategories" unchecked is not sent: it was read as 1, and could not be turned off;
 * - "last year" was left out of the address as a default (365) while the page read the period of the settings.
 * (The state of the model needs a whole site: its code is read.)
 */
class SearchModelStateTest extends UnitTestCase
{
    private function source(): string
    {
        return str_replace("\r\n", "\n", Files::read('src/site/src/Model/SearchModel.php'));
    }

    public function testTheIdsOfTheSearchOfATopicAreAList(): void
    {
        $source = $this->source();

        $this->assertStringNotContainsString("explode(' ', (int) \$value)", $source);
        $this->assertStringContainsString("\$value = Factory::getApplication()->getInput()->get('ids', '0', 'raw');", $source);
    }

    public function testAnUncheckedBoxOfSubcategoriesIsNo(): void
    {
        $source = $this->source();

        $this->assertStringContainsString("getInt('childforums', \$posted ? 0 : 1)", $source);
        $this->assertStringContainsString("'childforums'  => 1,", $source);
    }

    public function testThePeriodOfTheAddressIsTheDefaultOfTheSettings(): void
    {
        $source = $this->source();

        $this->assertStringContainsString("'searchdate'   => (string) \$this->config->searchTime,", $source);
        $this->assertStringContainsString("getString('searchdate', \$this->config->searchTime)", $source);
    }
}
