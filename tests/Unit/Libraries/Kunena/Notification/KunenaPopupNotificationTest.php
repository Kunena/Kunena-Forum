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

namespace Kunena\Tests\Unit\Libraries\Kunena\Notification;

use Kunena\Forum\Libraries\Notification\KunenaPopupNotification;
use Kunena\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class KunenaPopupNotificationTest extends UnitTestCase
{
    public static function reasons(): array
    {
        return [
            'mention beats all'      => [1, 1, 1, 'mention'],
            'own topic'              => [0, 1, 0, 'reply'],
            'own topic and followed' => [0, 1, 1, 'reply'],
            'followed topic'         => [0, 0, 1, 'subscribed'],
            'new topic in category'  => [0, 0, 0, 'topic'],
        ];
    }

    #[DataProvider('reasons')]
    public function testTheStrongestReasonWins(int $mentioned, int $own, int $topicsub, string $expected): void
    {
        $this->assertSame($expected, KunenaPopupNotification::reason((object) ['mentioned' => $mentioned, 'own' => $own, 'topicsub' => $topicsub]));
    }

    public function testEveryReasonHasATitleInTheLanguageFile(): void
    {
        $ini = (string) file_get_contents(\dirname(__DIR__, 5) . '/src/site/language/en-GB/com_kunena.ini');

        foreach (['mention', 'reply', 'subscribed', 'topic'] as $reason) {
            $key = (new \ReflectionClassConstant(KunenaPopupNotification::class, 'TITLES'))->getValue()[$reason];
            $this->assertMatchesRegularExpression('/^' . preg_quote($key, '/') . '\s*=/m', $ini, "$key is not in the language file.");
        }
    }
}
