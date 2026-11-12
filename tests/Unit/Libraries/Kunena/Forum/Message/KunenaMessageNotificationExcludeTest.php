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

namespace Kunena\Tests\Unit\Libraries\Kunena\Forum\Message;

use Kunena\Forum\Libraries\Forum\Message\KunenaMessage;
use Kunena\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * The users who get no notification of a message: the current user and the author.
 */
class KunenaMessageNotificationExcludeTest extends UnitTestCase
{
    public static function users(): array
    {
        return [
            'a moderator approves the message of an author' => [2, 5, [2, 5]],
            'the author posts himself'                      => [5, 5, [5]],
            'a moderator approves the message of a guest'   => [2, 0, [2]],
            'a guest posts'                                 => [0, 0, []],
        ];
    }

    #[DataProvider('users')]
    public function testNotificationExcludeList(int $currentUserId, int $authorId, array $expected): void
    {
        $this->assertSame($expected, KunenaMessage::notificationExcludeList($currentUserId, $authorId));
    }

    public function testAnotherSubscriberIsNotExcluded(): void
    {
        $this->assertNotContains(7, KunenaMessage::notificationExcludeList(2, 5));
    }

    public function testTheIdsAreIntegers(): void
    {
        foreach (KunenaMessage::notificationExcludeList(2, 5) as $id) {
            $this->assertIsInt($id);
        }
    }
}
