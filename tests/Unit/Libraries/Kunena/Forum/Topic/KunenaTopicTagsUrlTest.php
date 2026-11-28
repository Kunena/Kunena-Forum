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

namespace Kunena\Tests\Unit\Libraries\Kunena\Forum\Topic;

use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The link of a tag lists all the topics of the tag (sel=-1). Without a time the list took the default time of the lists (the
 * setting "list time", 720 hours), which it applies before the tag: a topic of the tag whose last post was two months ago was not in
 * the list of its tag, for every guest and every member with the default time. (The address goes through the router of a site: its
 * code is read.)
 */
class KunenaTopicTagsUrlTest extends UnitTestCase
{
    public function testTheLinkOfATagHasAllTheTimes(): void
    {
        $source = Files::read('src/libraries/kunena/src/Forum/Topic/KunenaTopicTags.php');

        $this->assertStringContainsString("'index.php?option=com_kunena&view=topics&layout=default&ftag=' . \$tagId . '&sel=-1'", $source);
    }
}
