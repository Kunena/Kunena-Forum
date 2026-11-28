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

namespace Kunena\Tests\Unit\Component\Site\Layout;

use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * A result of the search is its message parsed with the message as its parent and 500 as its length: 500 was given as the parent,
 * so a result had the whole text, and its tags had no message ([confidential] knew no author and no category, [attachment] no
 * file). (The results need a whole site: their code is read.)
 */
class SearchResultsParentTest extends UnitTestCase
{
    public function testTheMessageIsTheParentAndTheLengthIsTheLength(): void
    {
        $this->assertStringContainsString(
            'KunenaParser::parseBBCode($this->message->message, $this->message, 500);',
            Files::read('src/site/src/Layout/Search/SearchResults.php')
        );
        $this->assertStringContainsString(
            'public static function parseBBCode($txt, $parent = null, $len = 0,',
            Files::read('src/libraries/kunena/src/Html/KunenaParser.php')
        );
    }
}
