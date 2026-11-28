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
 * The options of a poll whose text is empty are left out before the poll is saved: an edit which emptied every option kept the poll
 * without an option (all its options and votes were deleted), and a new topic made a poll of empty options without an option. Now
 * such a poll is removed in the edit (with the right to delete it), and not made in a new topic. (The tasks need a whole site: their
 * code is read.)
 */
class TopicPollEmptyOptionsTest extends UnitTestCase
{
    public function testTheEmptyOptionsAreLeftOut(): void
    {
        $source = Files::read('src/site/src/Controllers/TopicController.php');

        $this->assertSame(2, substr_count($source, "\$poll_options = array_filter((array) \$fields['poll_options'], static fn (\$option) => trim((string) \$option) !== '');"));
        $this->assertStringNotContainsString("\$poll_options = \$fields['poll_options'];", $source);
    }
}
