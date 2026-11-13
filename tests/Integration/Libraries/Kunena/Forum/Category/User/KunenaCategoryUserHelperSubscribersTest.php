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

namespace Kunena\Tests\Integration\Libraries\Kunena\Forum\Category\User;

use Kunena\Forum\Libraries\Forum\Category\User\KunenaCategoryUserHelper;
use Kunena\Tests\Integration\DBTestInterface;
use Kunena\Tests\Integration\DBTestTrait;
use Kunena\Tests\Integration\IntegrationTestCase;

/**
 * The users subscribed to a category (the Subscribers tab of the category form in the backend), on the real tables of the
 * user categories and of the users.
 */
class KunenaCategoryUserHelperSubscribersTest extends IntegrationTestCase implements DBTestInterface
{
    use DBTestTrait;

    /**
     * The users 10 (charlie), 11 (alice), 12 (bob) and 13 (dave) exist.
     *
     * @return  void
     */
    protected function setUp(): void
    {
        parent::setUp();

        foreach ([10 => 'charlie', 11 => 'alice', 12 => 'bob', 13 => 'dave'] as $id => $username) {
            $this->insertRow('#__users', ['id' => $id, 'name' => 'User ' . $id, 'username' => $username, 'params' => '']);
        }
    }

    /**
     * @return  string[]
     */
    public function getSchemasToLoad(): array
    {
        return ['kunena', 'joomla'];
    }

    private function row(int $userid, int $catid, int $subscribed, int $role = 0): void
    {
        $this->insertRow('#__kunena_user_categories', ['user_id' => $userid, 'category_id' => $catid, 'role' => $role, 'subscribed' => $subscribed, 'params' => '']);
    }

    private function subscribers(int $catid): array
    {
        return KunenaCategoryUserHelper::getSubscriberIds($this->getDBDriver(), $catid);
    }

    public function testTheSubscribersOfTheCategoryAreOrderedByUsername(): void
    {
        $this->row(10, 2, 1);
        $this->row(11, 2, 1);
        $this->row(12, 2, 1);

        $this->assertSame([11, 12, 10], $this->subscribers(2));
    }

    public function testARowWhichIsNotSubscribedIsLeftOut(): void
    {
        $this->row(10, 2, 1);
        $this->row(11, 2, 0, 1);
        $this->row(12, 2, 0);

        $this->assertSame([10], $this->subscribers(2));
    }

    public function testASubscriptionToAnotherCategoryIsLeftOut(): void
    {
        $this->row(10, 2, 1);
        $this->row(11, 3, 1);

        $this->assertSame([10], $this->subscribers(2));
        $this->assertSame([11], $this->subscribers(3));
    }

    public function testABannedUserIsKept(): void
    {
        $this->insertRow('#__kunena_users', ['userid' => 13, 'banned' => '2099-01-01 00:00:00']);
        $this->row(13, 2, 1);
        $this->row(10, 2, 1);

        $this->assertSame([10, 13], $this->subscribers(2));
    }

    public function testARowOfAUserWhoIsNotInTheUsersIsLeftOut(): void
    {
        $this->row(99, 2, 1);
        $this->row(11, 2, 1);

        $this->assertSame([11], $this->subscribers(2));
    }

    public function testACategoryWithoutSubscribersGivesNoUsers(): void
    {
        $this->row(10, 2, 1);

        $this->assertSame([], $this->subscribers(4));
        $this->assertSame([], $this->subscribers(0));
    }
}
