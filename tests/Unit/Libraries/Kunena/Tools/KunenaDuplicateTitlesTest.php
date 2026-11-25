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

namespace Kunena\Tests\Unit\Libraries\Kunena\Tools;

use Kunena\Forum\Libraries\Tools\KunenaDuplicateTitles as T;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The tool "Duplicate topic titles" groups the topics of a category with the same title.
 */
class KunenaDuplicateTitlesTest extends UnitTestCase
{
    public function testTheKeyOfATitle(): void
    {
        $this->assertSame(T::key('How to install the forum?'), T::key('Re: how to INSTALL the forum'));
        $this->assertSame(T::key('Forum install: how to'), T::key('how to install forum'));
        $this->assertSame('', T::key('Help!'));
        $this->assertSame(T::key('Résumé upload &amp; avatars'), T::key('résumé upload & AVATARS'));
    }

    public function testTheGroups(): void
    {
        $groups = T::groups([
            ['id' => 5, 'category_id' => 2, 'subject' => 'Re: Cannot upload an avatar', 'first_post_time' => 300, 'posts' => 1],
            ['id' => 4, 'category_id' => 2, 'subject' => 'Cannot upload an avatar', 'first_post_time' => 200, 'posts' => 3],
            ['id' => 3, 'category_id' => 3, 'subject' => 'Cannot upload an avatar', 'first_post_time' => 100, 'posts' => 1],
            ['id' => 2, 'category_id' => 2, 'subject' => 'Help', 'first_post_time' => 50, 'posts' => 1],
            ['id' => 1, 'category_id' => 2, 'subject' => 'Help', 'first_post_time' => 10, 'posts' => 1],
        ]);

        // The same title in another category, or a title too short, is no duplicate; the oldest topic first.
        $this->assertCount(1, $groups);
        $this->assertSame([4, 5], array_column($groups[0], 'id'));
    }
}
