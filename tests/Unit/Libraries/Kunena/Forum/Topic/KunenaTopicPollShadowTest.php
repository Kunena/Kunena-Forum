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
use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;
use ReflectionProperty;

/**
 * The shadow of a moved topic keeps its poll. A vote (and the reset of the votes) through the shadow was checked against the old
 * category and the shadow, which is never locked: a user voted on a poll of a topic moved to a category which he cannot read, or
 * closed by a lock, and a moderator of the old category reset the votes of a poll which he does not moderate.
 */
class KunenaTopicPollShadowTest extends UnitTestCase
{
    public function testNoVoteThroughAShadow(): void
    {
        $this->requireJoomla();

        $actions = (new ReflectionProperty(KunenaTopic::class, 'actions'))->getValue();

        $this->assertContains('NotMoved', $actions['poll.vote']);
    }

    public function testNoResetThroughAShadow(): void
    {
        $source = str_replace("\r\n", "\n", Files::read('src/site/src/Controllers/TopicController.php'));

        $this->assertSame(1, preg_match('/public function resetvotes\(\)\s*\{(.*?)\n    \}\n/s', $source, $m));
        $this->assertStringContainsString('if (!$topic->exists() || $topic->moved_id || !$this->me->isModerator($topic->getCategory())) {', $m[1]);
    }
}
