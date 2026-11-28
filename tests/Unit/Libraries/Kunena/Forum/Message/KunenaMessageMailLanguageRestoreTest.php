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

use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * After the notification of a recipient in another language, the language of the page is restored from the folders which the switch
 * loaded it from (loadLanguageMail(): the folder of the component), for both files. It was loaded from the language folder of the
 * site, which has no file of Kunena: nothing was loaded, the strings stayed in the language of the last recipient, and a mention mail
 * sent after the notifications (or the next page of the moderator who approved) was in that language. (The mails need a whole site:
 * the code is read.)
 */
class KunenaMessageMailLanguageRestoreTest extends UnitTestCase
{
    public function testTheLanguageIsRestoredFromTheFoldersOfKunena(): void
    {
        $source = str_replace("\r\n", "\n", Files::read('src/libraries/kunena/src/Forum/Message/KunenaMessage.php'));

        $restore = strpos($source, '// Restore original language regardless of success or failure');

        $this->assertNotFalse($restore);
        $block = substr($source, $restore, 700);

        $this->assertStringContainsString("KunenaFactory::loadLanguageMail('com_kunena', 'site', \$currentLanguage);", $block);
        $this->assertStringContainsString("KunenaFactory::loadLanguageMail('com_kunena.libraries', 'admin', \$currentLanguage);", $block);
        $this->assertStringNotContainsString("\$lang->load('com_kunena', JPATH_SITE, \$currentLanguage, true);", $source);
    }
}
