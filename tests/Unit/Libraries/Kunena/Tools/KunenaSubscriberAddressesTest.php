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

use Kunena\Forum\Libraries\Tools\KunenaSubscriberAddresses as A;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The tool "Subscriber address check" finds the addresses which cannot get the e-mails, without asking the DNS here.
 */
class KunenaSubscriberAddressesTest extends UnitTestCase
{
    private static function mail(string $domain): ?bool
    {
        return ['example.org' => true, 'gone.example' => false][$domain] ?? null;
    }

    public function testATypingErrorInTheNameOfAProvider(): void
    {
        $this->assertSame('gmail.com', A::typo('GMIAL.com'));
        $this->assertSame('hotmail.com', A::typo('hotmial.com'));
        $this->assertSame('example.com', A::typo('example.con'));
        $this->assertNull(A::typo('gmail.com'));
        $this->assertNull(A::typo('example.org'));
    }

    public function testTheProblemsOfAnAddress(): void
    {
        $mail = static fn (string $domain) => self::mail($domain);

        $this->assertNull(A::problem('jane@example.org', $mail));
        $this->assertSame([A::SYNTAX, ''], A::problem('jane.example.org', $mail));
        $this->assertSame([A::TYPO, 'gmail.com'], A::problem('jane@gmial.com', $mail));
        $this->assertSame([A::NO_MAIL, 'gone.example'], A::problem('jane@gone.example', $mail));
    }

    /**
     * A domain which was not looked up (the limit of a run) is no problem yet.
     */
    public function testADomainWhichWasNotLookedUp(): void
    {
        $this->assertNull(A::problem('jane@unknown.example', static fn () => null));
    }
}
