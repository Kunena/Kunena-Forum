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

namespace Kunena\Tests\Unit\Libraries\Kunena\Forum\Topic;

use Kunena\Forum\Libraries\Forum\Topic\KunenaTopic;
use Kunena\Tests\Unit\UnitTestCase;
use ReflectionProperty;

/**
 * A poll takes no vote in a topic which waits for approval or is closed for inactivity, as its form shows: the form needs the right
 * to reply (it showed only the results there), the vote did not, so a request (a stale page) was counted all the same.
 */
class KunenaTopicPollVoteClosedTest extends UnitTestCase
{
    public function testTheVoteFollowsTheStateOfTheTopic(): void
    {
        $this->requireJoomla();

        $actions = (new ReflectionProperty(KunenaTopic::class, 'actions'))->getValue();

        $this->assertContains('NotHold', $actions['poll.vote']);
        $this->assertContains('NotInactive', $actions['poll.vote']);
        $this->assertContains('NotHold', $actions['post.reply']);
        $this->assertContains('NotInactive', $actions['post.reply']);
    }
}
