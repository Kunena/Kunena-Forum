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
 * A wrong (or missing) answer to the captcha of a post says so: the captcha of Joomla says false without a message of its own (only
 * older captcha plugins throw one), and the form came back without a word of why. (The task needs a whole site: its code is read.)
 */
class TopicCaptchaMessageTest extends UnitTestCase
{
    public function testAWrongAnswerHasAMessage(): void
    {
        $source = str_replace("\r\n", "\n", Files::read('src/site/src/Controllers/TopicController.php'));
        $check  = strpos($source, "if (!\$captcha->checkAnswer(\$app->getInput()->post->getString('captcha', ''))) {");

        $this->assertNotFalse($check);

        $branch = substr($source, $check, (int) strpos($source, 'return;', $check) - $check);

        $this->assertStringContainsString("\$app->enqueueMessage(Text::_('COM_KUNENA_POST_ERROR_CAPTCHA'), 'error');", $branch);
        $this->assertStringContainsString("\nCOM_KUNENA_POST_ERROR_CAPTCHA = \"", Files::read('src/site/language/en-GB/com_kunena.ini'));
    }
}
