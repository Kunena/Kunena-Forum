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

namespace Kunena\Tests\Integration\Libraries\Kunena\Forum;

use Kunena\Forum\Libraries\Forum\Category\KunenaCategory;
use Kunena\Tests\Integration\DBTestInterface;
use Kunena\Tests\Integration\DBTestTrait;
use Kunena\Tests\Integration\IntegrationTestCase;

/**
 * The topics which the prune of a category (purge, trash) takes: the old topics of the category. The methods had a time and did
 * not use it: only the condition of the caller (Tools > Prune put the date into the extra condition) kept the new topics.
 */
class KunenaCategoryPruneTest extends IntegrationTestCase implements DBTestInterface
{
    use DBTestTrait;

    private const NOW = 1800000000;

    private const DAY = 86400;

    private function topic(int $id, int $category, int $age, int $hold = 0): void
    {
        $this->insertRow('#__kunena_topics', ['id' => $id, 'category_id' => $category, 'subject' => "Topic $id", 'hold' => $hold, 'last_post_time' => self::NOW - $age]);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->topic(1, 2, 100 * self::DAY);
        $this->topic(2, 2, self::DAY);
        $this->topic(3, 3, 100 * self::DAY);
        $this->topic(4, 2, 200 * self::DAY, 2);
    }

    public function testTheOldTopicsOfTheCategoryOnly(): void
    {
        $ids = KunenaCategory::getOldTopicIds($this->getDBDriver(), 2, self::NOW - 30 * self::DAY);

        // The oldest first.
        $this->assertSame([4, 1], $ids);
    }

    public function testTheTrashLeavesTheDeletedTopics(): void
    {
        $ids = KunenaCategory::getOldTopicIds($this->getDBDriver(), 2, self::NOW - 30 * self::DAY, '', 1000, true);

        $this->assertSame([1], $ids);
    }
}
