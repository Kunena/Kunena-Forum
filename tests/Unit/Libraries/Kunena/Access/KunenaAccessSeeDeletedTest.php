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

namespace Kunena\Tests\Unit\Libraries\Kunena\Access;

use Kunena\Forum\Libraries\Access\KunenaAccess;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategory;
use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The lists of the deleted topics and posts (and their feed) are of the categories in which the user sees the deleted messages
 * ("Show deleted messages": the administrators only by default, or also the moderators). They took every category he moderates:
 * the count had the deleted topics which the list then left out (empty pages, and the size of the trash), and the feed showed them.
 */
class KunenaAccessSeeDeletedTest extends UnitTestCase
{
    public function testOnlyTheCategoriesWhereTheUserSeesTheDeletedOnesAreKept(): void
    {
        // The user sees the deleted messages of the category 2, not of the category 1 (which he moderates: 1 is in its holds).
        $access = new class () extends KunenaAccess {
            public function __construct()
            {
            }

            public function getAllowedHold($user, int $catid, $string = true)
            {
                return $catid === 2 ? [0, 1, 2, 3] : [0, 1];
            }
        };

        $categories = [];

        foreach ([1, 2] as $id) {
            $categories[$id] = static::bare(KunenaCategory::class, ['id' => $id]);
        }

        $this->assertSame([2], array_keys($access->filterSeeDeleted($categories, 7)));
    }

    /**
     * The lists and the feed use it (they need a whole site: their code is read).
     */
    public function testTheDeletedListsAndTheFeedKeepOnlyThoseCategories(): void
    {
        foreach (['Topic/Listing/Recent/TopicListingRecentDisplay.php', 'Message/Listing/Recent/MessageListingRecentDisplay.php'] as $file) {
            $source = str_replace("\r\n", "\n", Files::read('src/site/src/Controller/' . $file));

            $this->assertMatchesRegularExpression(
                "/if \(\\\$this->state->get\('list.mode'\) === 'deleted'\) \{\n\s*\\\$categories = KunenaAccess::getInstance\(\)->filterSeeDeleted\(\\\$categories, \\\$this->me\);\n\s*\}\n\n\s*\\\$finder->filterByCategories\(\\\$categories\);/",
                $source,
                $file
            );
        }

        $this->assertStringContainsString(
            "KunenaAccess::getInstance()->filterSeeDeleted(KunenaCategoryHelper::getCategories(false, false, 'topic.undelete'), KunenaUserHelper::getMyself());",
            Files::read('src/site/src/Model/TopicsModel.php')
        );
    }
}
