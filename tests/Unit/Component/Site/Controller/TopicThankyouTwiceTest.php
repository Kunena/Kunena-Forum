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

namespace Kunena\Tests\Unit\Component\Site\Controller;

use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * A thank you which is not saved (a double click: both requests passed the check, the second insert met the key of the table) gives
 * no success: the result of save() was not read, so the log was written again, the activity integrations (JomSocial, EasySocial)
 * gave the author his points twice and the inbox had the thanks twice. (The task needs a whole site: its code is read.)
 */
class TopicThankyouTwiceTest extends UnitTestCase
{
    public function testAThankYouWhichIsNotSavedStopsTheTask(): void
    {
        $source = str_replace("\r\n", "\n", Files::read('src/site/src/Controllers/TopicController.php'));

        $start = strpos($source, '    protected function setThankyou(');
        $this->assertNotFalse($start);
        $body = substr($source, $start, (int) strpos($source, "\n    }\n", $start) - $start);

        $this->assertMatchesRegularExpression('/if \(!\$thankyou->save\(\$this->me\)\) \{\s*\$this->setRedirectBack\(\);\s*return;/', $body);
        $this->assertLessThan(strpos($body, 'onAfterThankyou('), strpos($body, 'if (!$thankyou->save($this->me))'));
    }
}
