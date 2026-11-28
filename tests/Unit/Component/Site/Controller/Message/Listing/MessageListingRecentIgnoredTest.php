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

namespace Kunena\Tests\Unit\Component\Site\Controller\Message\Listing;

use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The recent posts of all the users leave out the posts of the users whom the visitor ignores: "Ignore user" says that the messages
 * and the topics of the user are hidden, the topic lists leave them out, and the recent posts showed them in full. (The list needs a
 * whole site: its code is read; KunenaMessageFinderThankeeTest runs the filter of the finder.)
 */
class MessageListingRecentIgnoredTest extends UnitTestCase
{
    public function testThePostsOfIgnoredUsersAreLeftOut(): void
    {
        $source = str_replace("\r\n", "\n", Files::read('src/site/src/Controller/Message/Listing/Recent/MessageListingRecentDisplay.php'));

        $this->assertMatchesRegularExpression(
            '/default:\n\s*\$finder\n\s*->filterByUser\(\$user, \'author\'\)\n\s*->filterByHold\(\[0\]\);\n(?:\s*\/\/[^\n]*\n)*\s*if \(!\$user\) \{\n\s*\$finder->filterOutAuthors\(KunenaUserIgnore::getIgnored\(/',
            $source
        );
        // Before the count of the pages.
        $this->assertLessThan(strpos($source, '$finder->count()'), strpos($source, '$finder->filterOutAuthors('));
    }
}
