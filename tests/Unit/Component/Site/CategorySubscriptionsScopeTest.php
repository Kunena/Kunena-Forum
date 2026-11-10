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

namespace Kunena\Tests\Unit\Component\Site;

use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The subscribed categories of a user were listed for any member who named the user in the request.
 */
class CategorySubscriptionsScopeTest extends UnitTestCase
{
    private function before(string $file): string
    {
        $this->assertSame(1, preg_match('/protected function before\(\)[^{]*\{(.*?)\n    \}\n/s', Files::read($file), $m), $file);

        return $m[1];
    }

    public function testSubscribedCategoriesAreForTheUserAndTheModerators(): void
    {
        $body  = $this->before('src/site/src/Controller/Category/Subscriptions/CategorySubscriptionsDisplay.php');
        $check = strpos($body, '$this->user->userid != $me->userid && !$me->isModerator()');

        $this->assertIsInt($check);
        $this->assertLessThan(strpos($body, 'KunenaCategoryHelper::getLatestSubscriptions('), $check, 'Nothing is read before the check.');
        $this->assertMatchesRegularExpression('/!\$me->isModerator\(\)\) \{\s*throw new KunenaExceptionAuthorise\(/', $body);
    }
}
