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

namespace Kunena\Tests\Unit\Libraries\Kunena\Forum\Category;

use Kunena\Forum\Libraries\Forum\Category\KunenaCategory;
use Kunena\Forum\Libraries\Forum\Message\KunenaMessage;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopic;
use Kunena\Tests\Unit\UnitTestCase;
use ReflectionProperty;

/**
 * With the option "moderators may delete permanently" a moderator deleted one message for good with its button (which asks
 * the topic), but not several messages in a list of posts: that asks the message, and the category wanted an administrator
 * for it. The option is decided by the rule Permdelete of the message and of the topic; the category asks for a moderator, as
 * it does for a topic.
 */
class KunenaCategoryPermdeleteRuleTest extends UnitTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->requireJoomla();
    }

    private function actions(string $class): array
    {
        return (new ReflectionProperty($class, 'actions'))->getValue();
    }

    public function testTheCategoryAsksAModeratorForAMessageAsForATopic(): void
    {
        $category = $this->actions(KunenaCategory::class);

        $this->assertSame($category['topic.permdelete'], $category['topic.post.permdelete']);
        $this->assertContains('Moderate', $category['topic.post.permdelete']);
        $this->assertContains('NotBanned', $category['topic.post.permdelete']);
    }

    public function testTheOptionIsStillDecidedByTheMessageAndTheTopic(): void
    {
        $this->assertContains('Permdelete', $this->actions(KunenaMessage::class)['permdelete']);
        $this->assertContains('Permdelete', $this->actions(KunenaTopic::class)['post.permdelete']);
        $this->assertContains('Permdelete', $this->actions(KunenaTopic::class)['permdelete']);
    }
}
