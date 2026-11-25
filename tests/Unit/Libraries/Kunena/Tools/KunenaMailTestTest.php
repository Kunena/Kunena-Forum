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

use Kunena\Forum\Libraries\Tools\KunenaMailTest as M;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The tool "Test email" names the settings which stop the notifications.
 */
class KunenaMailTestTest extends UnitTestCase
{
    private static function settings(array $values): array
    {
        return $values + [
            'mailonline' => true,
            'mailer'     => 'smtp',
            'smtphost'   => 'smtp.example.org',
            'sender'     => 'forum@example.org',
            'sendEmails' => true,
            'queue'      => 0,
            'queueTask'  => true,
        ];
    }

    public function testGoodSettingsHaveNoProblem(): void
    {
        $this->requireJoomla();

        $this->assertSame([], M::problems(self::settings([])));
    }

    public function testEachProblem(): void
    {
        $this->requireJoomla();

        $this->assertSame(['COM_KUNENA_TOOLS_MAILTEST_PROBLEM_OFFLINE'], M::problems(self::settings(['mailonline' => false])));
        $this->assertSame(['COM_KUNENA_TOOLS_MAILTEST_PROBLEM_KUNENA_OFF'], M::problems(self::settings(['sendEmails' => false])));
        $this->assertSame(['COM_KUNENA_TOOLS_MAILTEST_PROBLEM_SENDER'], M::problems(self::settings(['sender' => 'not an address'])));
        $this->assertSame(['COM_KUNENA_TOOLS_MAILTEST_PROBLEM_SMTP'], M::problems(self::settings(['smtphost' => ''])));
        $this->assertSame([], M::problems(self::settings(['mailer' => 'mail', 'smtphost' => ''])));
        $this->assertSame(['COM_KUNENA_TOOLS_MAILTEST_PROBLEM_QUEUE'], M::problems(self::settings(['queue' => 4, 'queueTask' => false])));
        $this->assertSame([], M::problems(self::settings(['queue' => 4])));
    }
}
