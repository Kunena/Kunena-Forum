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

namespace Kunena\Tests\Unit\Libraries\Kunena\Tools;

use Kunena\Forum\Libraries\Tools\KunenaSubscriptions;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The tool "Subscriptions" removes the subscriptions of a known scope only.
 */
class KunenaSubscriptionsTest extends UnitTestCase
{
    public function testAnUnknownScopeUnsubscribesNobody(): void
    {
        $this->assertSame(0, KunenaSubscriptions::unsubscribeOnSite('everyone', 5));
        $this->assertSame(0, KunenaSubscriptions::unsubscribeOnSite('user', 0));
        $this->assertSame(['user', 'category', 'topic'], array_keys(KunenaSubscriptions::SCOPES));
        $this->assertSame(0, KunenaSubscriptions::unsubscribeOnSite('topic', 5, -1));
    }

    public function testNoUserHasNoSubscription(): void
    {
        $this->assertSame(['categories' => [], 'topics' => [], 'topicsTotal' => 0], KunenaSubscriptions::ofUserOnSite(0));
    }
}
