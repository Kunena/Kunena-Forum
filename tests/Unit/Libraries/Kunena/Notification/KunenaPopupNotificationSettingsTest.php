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

namespace Kunena\Tests\Unit\Libraries\Kunena\Notification;

use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The popups count the subscriptions as the settings of the forum let them, as the e-mails do: with the subscriptions of the topics
 * or of the categories turned off, the old subscriptions still gave popups. (The query is tested with the database: here the reading
 * of the settings.)
 */
class KunenaPopupNotificationSettingsTest extends UnitTestCase
{
    public function testThePopupsReadTheSettingsOfTheSubscriptions(): void
    {
        $source = Files::read('src/libraries/kunena/src/Notification/KunenaPopupNotification.php');

        $this->assertStringContainsString("\$topics = !empty(\$config->allowSubscriptions) && \$config->topicSubscriptions !== 'disabled';", $source);
        $this->assertStringContainsString("\$cats   = !empty(\$config->allowSubscriptions) && \$config->categorySubscriptions !== 'disabled';", $source);
        $this->assertStringContainsString('self::candidates($db, $userid, $after, $cursor, null, $topics, $cats);', $source);
    }
}
