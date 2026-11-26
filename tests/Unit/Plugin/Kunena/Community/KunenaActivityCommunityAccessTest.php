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

namespace Kunena\Tests\Unit\Plugin\Kunena\Community;

use Kunena\Forum\Plugin\Kunena\Community\Helper\KunenaActivityCommunity;
use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The JomSocial activities of the replies of a deleted topic were removed under a name which no activity has: they stayed.
 */
class KunenaActivityCommunityAccessTest extends UnitTestCase
{
    protected function setUp(): void
    {
        $this->requireJoomla();

        require_once \dirname(__DIR__, 5) . '/src/plugins/plg_kunena_community/src/Helper/KunenaActivityCommunity.php';
    }

    public function testTheRepliesOfADeletedTopicAreRemovedUnderTheirName(): void
    {
        $source = Files::read('src/plugins/plg_kunena_community/src/Helper/KunenaActivityCommunity.php');

        $this->assertSame(1, preg_match('/public function onAfterDeleteTopic\(\$target\): void\s*\{(.*?)\n    \}/s', $source, $m));
        $this->assertStringContainsString("\$act->app     = 'kunena.thread.reply';", $source, 'The name of the activity of a reply.');
        $this->assertStringContainsString("CActivityStream::remove('kunena.thread.reply', \$target->id);", $m[1]);
        $this->assertStringNotContainsString("remove('kunena.thread.replied'", $source);
    }
}
