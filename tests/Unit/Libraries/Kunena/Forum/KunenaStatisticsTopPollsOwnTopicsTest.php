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

namespace Kunena\Tests\Unit\Libraries\Kunena\Forum;

use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The top polls of the statistics: in the categories where the users see their own topics only, the topics of the others are
 * left out before the list is cut, as the categories which the visitor cannot read. They were left out after it (by the right to
 * read of the topics), so with the most voted polls in such a category a guest got an empty list while he reads other polls.
 * (The list needs the categories, the access and the topics of a site: its code is read.)
 */
class KunenaStatisticsTopPollsOwnTopicsTest extends UnitTestCase
{
    public function testTheTopicsOfOthersAreLeftOutBeforeTheLimit(): void
    {
        $source = str_replace("\r\n", "\n", Files::read('src/libraries/kunena/src/Forum/KunenaStatistics.php'));

        $this->assertSame(1, preg_match('/public function loadTopPolls\(.*?\n    \}\n/s', $source, $m));

        $own   = strpos($m[0], "KunenaCategoryHelper::getOwnTopicsOnlyWhere(null, \$this->_db->quoteName('topic.category_id'), \$this->_db->quoteName('topic.first_post_userid'))");
        $limit = strpos($m[0], '$query->setLimit($limit);');

        $this->assertNotFalse($own);
        $this->assertLessThan($limit, $own);
    }
}
