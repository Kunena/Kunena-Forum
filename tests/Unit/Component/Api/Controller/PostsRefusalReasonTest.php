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

namespace Kunena\Tests\Unit\Component\Api\Controller;

use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * A reply which the forum refuses (403: a captcha user, read only, banned, a locked topic, links not allowed, the spam check) keeps
 * its reason in the answer: it was thrown as NotAllowed, whose handler of Joomla answers "Access Denied" whatever the message, so a
 * client could not tell them apart. The handler of Save keeps the message and the status. (The controller needs the API application:
 * its code is read.)
 */
class PostsRefusalReasonTest extends UnitTestCase
{
    public function testARefusalKeepsItsReason(): void
    {
        $source = Files::read('src/api/src/Controller/PostsController.php');

        $this->assertStringContainsString("403      => new Save(\$e->getMessage(), 403),", $source);
        $this->assertStringNotContainsString('new NotAllowed(', $source);
    }
}
