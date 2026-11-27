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

namespace Kunena\Tests\Unit\Libraries\Kunena\Email;

use Kunena\Forum\Libraries\Email\KunenaEmail;
use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * The option "UTM Source" ("Add UTM source to the email"): the links of the e-mails of the forum say that the visit comes from an
 * e-mail of the forum, for the statistics of the site. The option was in the configuration and nothing read it.
 */
class KunenaEmailUtmTest extends UnitTestCase
{
    private const UTM = 'utm_source=kunena&utm_medium=email';

    public static function addresses(): array
    {
        return [
            'a SEF address'            => ['https://example.org/forum/topic/5', true, 'https://example.org/forum/topic/5?' . self::UTM],
            'an address with values'   => ['https://example.org/index.php?option=com_kunena&id=5', true, 'https://example.org/index.php?option=com_kunena&id=5&' . self::UTM],
            'an address with a place'  => ['https://example.org/forum/topic/5#12', true, 'https://example.org/forum/topic/5?' . self::UTM . '#12'],
            'values and a place'       => ['https://example.org/index.php?id=5#12', true, 'https://example.org/index.php?id=5&' . self::UTM . '#12'],
            'a source already'         => ['https://example.org/topic/5?utm_source=newsletter', true, 'https://example.org/topic/5?utm_source=newsletter'],
            'the option switched off'  => ['https://example.org/forum/topic/5', false, 'https://example.org/forum/topic/5'],
            'no address'               => ['', true, ''],
        ];
    }

    #[DataProvider('addresses')]
    public function testTheAddressGetsTheSourceWhenTheOptionIsOn(string $url, bool $enabled, string $expected): void
    {
        $this->assertSame($expected, KunenaEmail::utm($url, $enabled));
    }

    public function testTheMailsOfTheForumUseIt(): void
    {
        $message = Files::read('src/libraries/kunena/src/Forum/Message/KunenaMessage.php');
        $digest  = Files::read('src/libraries/kunena/src/Email/KunenaDigest.php');

        // The notification of a post (both kinds of mail: the template of Joomla and the layout), the mention and the digest.
        $this->assertSame(1, preg_match('/protected function deliverNotification\(.*?\n    \}\n/s', $message, $m));
        $this->assertStringContainsString('$url = KunenaEmail::utm($url);', $m[0]);
        $this->assertSame(1, preg_match('/public function sendMentionNotifications\(.*?\n    \}\n/s', $message, $m));
        $this->assertStringContainsString('= KunenaEmail::utm($url);', $m[0]);
        $this->assertSame(2, substr_count($digest, 'KunenaEmail::utm('));
    }
}
