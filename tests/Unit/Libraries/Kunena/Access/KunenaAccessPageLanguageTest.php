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

namespace Kunena\Tests\Unit\Libraries\Kunena\Access;

use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The language of the page (a multilingual site) and the forum of the page (several forums on one site) limit the categories of the
 * visitor only, not those of another user: the e-mails of the queue and the digest are sent in the request of any visitor (the
 * scheduler of Joomla), and a subscriber "could not read" the categories of the other languages, so their e-mails were dropped as
 * sent. (The language of a page needs a whole site: the code is read.)
 */
class KunenaAccessPageLanguageTest extends UnitTestCase
{
    public function testTheAllowedCategoriesOfAnotherUserHaveEveryLanguage(): void
    {
        $source = Files::read('src/libraries/kunena/src/Access/KunenaAccess.php');

        $this->assertStringContainsString('if (KunenaMultilanguage::isEnabled() && $user->userid == KunenaUserHelper::getMyself()->userid) {', $source);
    }

    public function testTheReadOfACategoryByAnotherUserHasEveryLanguage(): void
    {
        $source = str_replace("\r\n", "\n", Files::read('src/libraries/kunena/src/Forum/Category/KunenaCategory.php'));

        $start = strpos($source, '    protected function authoriseRead(KunenaUser $user)');
        $this->assertNotFalse($start);
        $body = substr($source, $start, (int) strpos($source, "\n    }\n", $start) - $start);

        $guard = strpos($body, 'if ($user->userid == KunenaUserHelper::getMyself()->userid) {');
        $this->assertNotFalse($guard);
        $this->assertGreaterThan($guard, strpos($body, 'KunenaMultilanguage::isVisible($this->language)'));
        $this->assertGreaterThan($guard, strpos($body, 'KunenaCategoryHelper::getForumScope()'));
    }
}
