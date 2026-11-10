<?php

/**
 * Kunena Component
 *
 * The lists of the subscriptions and the favorites of a user, and the tasks that remove subscriptions, took the id of the user
 * from the request without asking who asks. Everybody (a guest too, for the topics) could remove the subscriptions of any member,
 * and read which topics a member follows or keeps as favorites.
 *
 * @package         Kunena.Tests
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 **/

namespace Kunena\Tests\Unit\Security;

use Kunena\Forum\Libraries\User\KunenaUserScope;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

if (!\defined('_JEXEC')) {
    \define('_JEXEC', 1);
}

require_once __DIR__ . '/../../../src/libraries/kunena/src/User/KunenaUserScope.php';

class UserScopeTest extends TestCase
{
    private const SRC = __DIR__ . '/../../../src/site/src';

    public static function actions(): array
    {
        return [
            // who asks, moderator, the user of the request, allowed
            'for oneself'                    => [42, false, 42, true],
            'for oneself, no id'             => [42, false, null, true],
            'for oneself, id 0'              => [42, false, 0, true],
            'for oneself, id as text'        => [42, false, '42', true],
            'a member for another'           => [42, false, 7, false],
            'a member for an administrator'  => [42, false, 1, false],
            'a moderator for another'        => [42, true, 7, true],
            'a moderator for oneself'        => [42, true, 42, true],
            'a guest for a member'           => [0, false, 7, false],
            'a guest for nobody'             => [0, false, null, false],
            'a guest, id 0'                  => [0, false, 0, false],
            'a guest who says moderator'     => [0, true, 7, false],
            'negative id of the request'     => [42, false, -7, true],
        ];
    }

    #[DataProvider('actions')]
    public function testWhoMayRemoveSubscriptions(int $actor, bool $moderator, mixed $target, bool $expected): void
    {
        $this->assertSame($expected, KunenaUserScope::mayActFor($actor, $moderator, $target));
    }

    public static function lists(): array
    {
        return [
            // list, who asks, moderator, the user of the list, allowed
            'own favorites'                        => ['favorites', 42, false, 42, true],
            'favorites of another'                 => ['favorites', 42, false, 7, false],
            'favorites of another, moderator'      => ['favorites', 42, true, 7, false],
            'favorites, guest'                     => ['favorites', 0, false, 7, false],
            'favorites, guest, user 0'             => ['favorites', 0, false, 0, false],
            'own subscriptions'                    => ['subscriptions', 42, false, 42, true],
            'subscriptions of another'             => ['subscriptions', 42, false, 7, false],
            'subscriptions of another, moderator'  => ['subscriptions', 42, true, 7, true],
            'subscriptions, guest'                 => ['subscriptions', 0, false, 7, false],
            'subscriptions, guest as moderator'    => ['subscriptions', 0, true, 7, false],
            'other spelling'                       => ['Subscriptions', 42, false, 7, false],
            'id as text'                           => ['favorites', 42, false, '42', true],
            'posts of another'                     => ['posted', 42, false, 7, true],
            'started topics, guest'                => ['started', 0, false, 7, true],
            'default list, guest'                  => ['default', 0, false, 7, true],
        ];
    }

    #[DataProvider('lists')]
    public function testWhoMaySeeAList(string $list, int $viewer, bool $moderator, mixed $target, bool $expected): void
    {
        $this->assertSame($expected, KunenaUserScope::maySeeList($list, $viewer, $moderator, $target));
    }

    private function body(string $file, string $method): string
    {
        $source = file_get_contents(self::SRC . '/' . $file);

        $this->assertSame(1, preg_match('/(?:public|protected) function ' . $method . '\(\)[^{]*\{(.*?)\r?\n    \}\r?\n/s', $source, $m), "$file::$method");
        $this->assertStringContainsString('use Kunena\Forum\Libraries\User\KunenaUserScope;', $source);

        return $m[1];
    }

    public function testTopicsAreUnsubscribedAfterTheCheck(): void
    {
        $body  = $this->body('Controllers/TopicsController.php', 'unsubscribe');
        $check = strpos($body, 'KunenaUserScope::mayActFor((int) $this->me->userid, (bool) $this->me->isModerator(), $userid)');

        $this->assertIsInt($check);
        $this->assertLessThan(strpos($body, 'KunenaTopicHelper::subscribe('), $check);
        $this->assertLessThan($check, strpos($body, 'Session::checkToken('));
    }

    public function testCategoriesAreUnsubscribedAfterTheCheck(): void
    {
        $body  = $this->body('Controllers/CategoryController.php', 'unsubscribe');
        $check = strpos($body, 'KunenaUserScope::mayActFor((int) $me->userid, (bool) $me->isModerator(), $userid)');

        $this->assertIsInt($check);
        $this->assertLessThan(strpos($body, '$category->subscribe(0, $userid)'), $check);
        $this->assertLessThan($check, strpos($body, 'Session::checkToken('));
    }

    public function testListOfTopicsOfAUserAsksBeforeItReads(): void
    {
        $body  = $this->body('Controller/Topic/Listing/User/TopicListingUserDisplay.php', 'before');
        $check = strpos($body, 'KunenaUserScope::maySeeList(');

        $this->assertIsInt($check);
        $this->assertLessThan(strpos($body, 'new KunenaTopicFinder()'), $check);
        $this->assertMatchesRegularExpression('/maySeeList\(.{0,300}?\)\s*\)\s*\{\s*throw new KunenaExceptionAuthorise\(/s', $body);
        $this->assertStringContainsString("\$this->state->get('list.mode')", substr($body, $check, 300), 'The list is the mode of the request.');
    }

    public function testSubscribedCategoriesOfAUserAskBeforeTheyAreRead(): void
    {
        $body  = $this->body('Controller/Category/Subscriptions/CategorySubscriptionsDisplay.php', 'before');
        $check = strpos($body, "KunenaUserScope::maySeeList('subscriptions',");

        $this->assertIsInt($check);
        $this->assertLessThan(strpos($body, 'KunenaCategoryHelper::getLatestSubscriptions('), $check);
    }
}
