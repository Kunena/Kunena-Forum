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
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * The action buttons of a topic and of a message carry the menu item KunenaRoute chooses for the topic, not the first Kunena
 * menu item of the site (issue #9529: under a view=user item, the edit link read the message id as a user id).
 */
class ActionButtonsItemidTest extends UnitTestCase
{
    public static function files(): array
    {
        return [
            'topic'   => ['src/site/src/Controller/Topic/Item/Actions/TopicItemActionsDisplay.php'],
            'message' => ['src/site/src/Controller/Message/Item/Actions/MessageItemActionsDisplay.php'],
        ];
    }

    #[DataProvider('files')]
    public function testTheFirstKunenaMenuItemIsNotUsed(string $file): void
    {
        $this->assertStringNotContainsString('fixMissingItemID', Files::read($file));
    }

    #[DataProvider('files')]
    public function testTheMenuItemOfTheTopicIsUsed(string $file): void
    {
        $this->assertStringContainsString('KunenaRoute::getItemID("index.php?option=com_kunena&view=topic&catid=', Files::read($file));
    }
}
