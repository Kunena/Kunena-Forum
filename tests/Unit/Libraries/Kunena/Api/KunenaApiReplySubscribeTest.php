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

namespace Kunena\Tests\Unit\Libraries\Kunena\Api;

use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * A reply through the API subscribes its author only where he may subscribe, as on the site: "subscribe": true subscribed also with
 * the subscriptions turned off (and the mails went). (The reply needs a whole site: its code is read.)
 */
class KunenaApiReplySubscribeTest extends UnitTestCase
{
    public function testTheRightToSubscribeIsChecked(): void
    {
        $this->assertStringContainsString(
            "if (\$fields['subscribe'] && !\$topic->getUserTopic(\$me)->subscribed && \$topic->isAuthorised('subscribe', \$me)) {",
            Files::read('src/libraries/kunena/src/Api/KunenaApiReply.php')
        );
    }
}
