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
 * A new post subscribes its author only where he may subscribe, as the box of the form is shown: a request subscribed with the
 * subscriptions of topics turned off, and a guest got the error of the table ("could not subscribe"). (The task needs a whole site:
 * its code is read.)
 */
class TopicPostSubscribeRightTest extends UnitTestCase
{
    public function testTheRightToSubscribeIsChecked(): void
    {
        $this->assertStringContainsString(
            "if (\$fields['subscribe'] && !\$usertopic->subscribed && \$topic->isAuthorised('subscribe')) {",
            Files::read('src/site/src/Controllers/TopicController.php')
        );
    }
}
