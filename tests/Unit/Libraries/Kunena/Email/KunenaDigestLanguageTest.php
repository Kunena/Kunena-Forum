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

use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The digest is in the language and the time zone of the user who gets it, as the e-mails of the subscriptions: it was written in
 * those of whoever ran the task (the visitor whose page ran the scheduler of Joomla, an administrator, the command line), so a
 * German member got an English digest with the times of the administrator. The language of the request is put back afterwards.
 * (The digest sends e-mails: its code is read.)
 */
class KunenaDigestLanguageTest extends UnitTestCase
{
    public function testTheDigestIsInTheLanguageAndTimeZoneOfItsReader(): void
    {
        $source = str_replace("\r\n", "\n", Files::read('src/libraries/kunena/src/Email/KunenaDigest.php'));

        $start = strpos($source, '    public static function send(KunenaUser $user, array $ids): bool');
        $this->assertNotFalse($start);
        $send = substr($source, $start, (int) strpos($source, "\n    }\n", $start) - $start);

        // The language of the user, loaded before the text is made and put back in a finally.
        $load    = strpos($send, "KunenaFactory::loadLanguageMail('com_kunena', 'site', \$mailLanguage);");
        $text    = strpos($send, "Text::plural('COM_KUNENA_DIGEST_INTRO'");
        $restore = strpos($send, "KunenaFactory::loadLanguageMail('com_kunena', 'site', \$currentLanguage);");

        $this->assertNotFalse($load);
        $this->assertNotFalse($restore);
        $this->assertLessThan($text, $load);
        $this->assertLessThan($restore, strpos($send, '} finally {'));

        // The times of the posts in his time zone.
        $this->assertStringContainsString("toKunena('datetime', \$timezone)", $send);
        $this->assertStringContainsString("KunenaUserHelper::getJoomlaParam((int) \$user->userid, 'timezone', '')", $send);
    }
}
