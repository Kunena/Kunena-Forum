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
 * The message "Your search ... did not match any topics" held the words of the search as they came. Joomla prints a message as
 * html. Only the filter of the request (which removes tags) stood between the address of a search and markup on the page: the
 * words are encoded where the message is made.
 */
class SearchModelNoResultsMessageTest extends UnitTestCase
{
    public function testWordsOfTheSearchAreEncodedInTheMessage(): void
    {
        $source = Files::read('src/site/src/Model/SearchModel.php');

        $this->assertSame(1, preg_match("/Text::sprintf\\('COM_KUNENA_SEARCH_NORESULTS_FOUND',([^;]*)\\);/s", $source, $m));

        $this->assertStringContainsString("htmlspecialchars((string) \$text, ENT_QUOTES, 'UTF-8')", $m[1]);
        $this->assertDoesNotMatchRegularExpression('/\'<strong>\' \. \$text \./', $m[1], 'The words are not put into the message as they came.');
        $this->assertSame(1, substr_count($source, 'COM_KUNENA_SEARCH_NORESULTS_FOUND'), 'The message is made in one place.');
    }
}
