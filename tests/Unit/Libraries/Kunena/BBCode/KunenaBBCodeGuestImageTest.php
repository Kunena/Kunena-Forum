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

namespace Kunena\Tests\Unit\Libraries\Kunena\BBCode;

use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * With "show images to guests" off, a guest sees only the emoticons of the site (emoticonUrl(), which finds the file of the
 * emoticon). The exception was any address with "media/kunena/emoticons" in it: [img]https://other.example/media/kunena/emoticons/x.png
 * [/img] showed that picture to the guests. (The tag needs the application and the layouts of a site: its code is read.)
 */
class KunenaBBCodeGuestImageTest extends UnitTestCase
{
    public function testOnlyAnEmoticonOfTheSiteIsShownToTheGuests(): void
    {
        $source = Files::read('src/libraries/kunena/src/BBCode/KunenaBBCode.php');

        $this->assertStringContainsString(
            'if (Factory::getApplication()->getIdentity()->id == 0 && $this->config->showImgForGuest == 0 && $emoticonUrl === null) {',
            $source
        );
        $this->assertStringNotContainsString("!preg_match('@media\\/kunena\\/emoticons@', \$fileurl)", $source);

        // The emoticon of the site is the one whose file is there (and no address of another site keeps its host).
        $this->assertStringContainsString("if (!is_file(\$smileyDir . '/media/kunena/emoticons/' . \$file)) {", $source);
    }
}
