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

namespace Kunena\Tests\Integration\Libraries\Kunena\Tools;

use Kunena\Forum\Libraries\Tools\KunenaUserMerge;
use Kunena\Tests\Integration\DBTestInterface;
use Kunena\Tests\Integration\DBTestTrait;
use Kunena\Tests\Integration\IntegrationTestCase;
use ReflectionMethod;

/**
 * The tool "Merge users" keeps the subscriptions, the favorites and the roles of the merged account where the other account has a
 * row of its own (a topic in which it posted, a category which it marked read): the row of the merged account was dropped, and its
 * subscription with it.
 */
class KunenaUserMergeSubscriptionsTest extends IntegrationTestCase implements DBTestInterface
{
    use DBTestTrait;

    private const FROM = 101;

    private const INTO = 102;

    private function move(string $table, string $other): void
    {
        (new ReflectionMethod(KunenaUserMerge::class, 'moveKeyed'))
            ->invoke(null, $this->getDBDriver(), $table, 'user_id', $other, self::FROM, self::INTO);
    }

    public function testTheSubscriptionAndTheFavoriteOfATopicAreKept(): void
    {
        $this->insertRows('#__kunena_user_topics', [
            ['user_id' => self::FROM, 'topic_id' => 7, 'category_id' => 2, 'subscribed' => 1, 'favorite' => 1],
            ['user_id' => self::INTO, 'topic_id' => 7, 'category_id' => 2, 'posts' => 1],
        ]);

        $this->move('#__kunena_user_topics', 'topic_id');

        $this->assertSame(
            [['user_id' => self::INTO, 'posts' => 1, 'favorite' => 1, 'subscribed' => 1]],
            array_map(static fn ($row) => array_map('intval', $row), $this->fetchAll('SELECT user_id, posts, favorite, subscribed FROM #__kunena_user_topics'))
        );
    }

    public function testTheSubscriptionOfACategoryIsKept(): void
    {
        $this->insertRows('#__kunena_user_categories', [
            ['user_id' => self::FROM, 'category_id' => 3, 'subscribed' => 1],
            ['user_id' => self::INTO, 'category_id' => 3, 'allreadtime' => 1000],
        ]);

        $this->move('#__kunena_user_categories', 'category_id');

        $this->assertSame(
            [['user_id' => self::INTO, 'allreadtime' => 1000, 'subscribed' => 1]],
            array_map(static fn ($row) => array_map('intval', $row), $this->fetchAll('SELECT user_id, allreadtime, subscribed FROM #__kunena_user_categories'))
        );
    }
}
