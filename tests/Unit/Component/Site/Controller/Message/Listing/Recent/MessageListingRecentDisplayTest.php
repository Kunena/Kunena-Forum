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

namespace Kunena\Tests\Unit\Component\Site\Controller\Message\Listing\Recent;

use Joomla\CMS\Uri\Uri;
use Joomla\Registry\Registry;
use Kunena\Forum\Site\Controller\Message\Listing\Recent\MessageListingRecentDisplay;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The page links of a list of messages embedded in the profile keep the time selection of the list (issue #5382: page 2 used the
 * default time filter instead of "all posts").
 */
class MessageListingRecentDisplayTest extends UnitTestCase
{
    protected function setUp(): void
    {
        $this->requireJoomla();
    }

    private function moreUri(int $time): Uri
    {
        $state = new Registry();
        $state->set('list.mode', 'latest');
        $state->set('user', 42);
        $state->set('list.limit', 10);
        $state->set('list.time', $time);

        return $this->invoke($this->bare(MessageListingRecentDisplay::class, ['state' => $state]), 'getMoreUri');
    }

    public function testAllPostsGiveSelMinusOne(): void
    {
        $this->assertSame('-1', $this->moreUri(-1)->getVar('sel'));
    }

    public function testATimeGivesThatSel(): void
    {
        $this->assertSame('8760', $this->moreUri(8760)->getVar('sel'));
    }

    public function testTheOtherVariablesAreKept(): void
    {
        $uri = $this->moreUri(8760);

        $this->assertSame('com_kunena', $uri->getVar('option'));
        $this->assertSame('topics', $uri->getVar('view'));
        $this->assertSame('posts', $uri->getVar('layout'));
        $this->assertSame('latest', $uri->getVar('mode'));
        $this->assertSame('42', $uri->getVar('userid'));
        $this->assertSame('10', $uri->getVar('limit'));
    }
}
