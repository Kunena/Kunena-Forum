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
 * The box "Subscribe" of a reply which failed (a refused captcha, the flood protection) comes back as the member left it, as in the
 * form of a new topic: a member with a setting of his own got his setting again, so the box he ticked came back empty. (The form
 * needs a whole site: its code is read.)
 */
class TopicReplySavedSubscribeTest extends UnitTestCase
{
    public function testTheSavedBoxComesFirst(): void
    {
        $source = str_replace("\r\n", "\n", Files::read('src/site/src/Controller/Topic/Form/Reply/TopicFormReplyDisplay.php'));

        $this->assertStringContainsString("if (\$saved) {\n            \$this->subscriptionsChecked = \$saved['subscribe'];\n        } elseif (\$this->me->canSubscribe == -1) {", $source);
    }
}
