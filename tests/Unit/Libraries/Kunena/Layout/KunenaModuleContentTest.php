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

namespace Kunena\Tests\Unit\Libraries\Kunena\Layout;

use Kunena\Forum\Libraries\Layout\KunenaModuleContent as M;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The positions of the modules get the targets of the Joomla content plugins: by name or by the start of the name, several for one
 * position, the one given last asked first.
 */
class KunenaModuleContentTest extends UnitTestCase
{
    protected function tearDown(): void
    {
        M::reset();
    }

    public function testAPositionByItsName(): void
    {
        M::prepareWith('kunena_index_top', 'index_top');

        $this->assertSame(['index_top'], M::targetsFor('kunena_index_top'));
        $this->assertSame([], M::targetsFor('kunena_index_bottom'));
    }

    public function testThePositionsWhichStartTheSame(): void
    {
        M::prepareWith('kunena_msg_*', 'topic_messages');
        M::prepareWith('kunena_section_*', 'index_sections');

        $this->assertSame(['topic_messages'], M::targetsFor('kunena_msg_3'));
        $this->assertSame(['topic_messages'], M::targetsFor('kunena_msg_row_12'));
        $this->assertSame(['index_sections'], M::targetsFor('kunena_section_top_2'));
        $this->assertSame([], M::targetsFor('kunena_msg'));
    }

    public function testTheTargetGivenLastIsAskedFirst(): void
    {
        // The frame of every page, then the list of topics.
        M::prepareWith('kunena_top', 'page_top');
        M::prepareWith('kunena_top', 'topic_list_default');

        $this->assertSame(['topic_list_default', 'page_top'], M::targetsFor('kunena_top'));
    }

    public function testTheNameBeforeTheStartAndTheLongestStartFirst(): void
    {
        M::prepareWith('kunena_*', 'everything');
        M::prepareWith('kunena_topic_*', 'topic_list_rows');
        M::prepareWith('kunena_topic_1', 'first_row');

        $this->assertSame(['first_row', 'topic_list_rows', 'everything'], M::targetsFor('kunena_topic_1'));
        $this->assertSame(['topic_list_rows', 'everything'], M::targetsFor('kunena_topic_2'));
    }

    public function testATargetGivenTwiceIsOnce(): void
    {
        M::prepareWith('kunena_top', 'page_top');
        M::prepareWith('kunena_top', 'topic_list_default');
        M::prepareWith('kunena_top', 'page_top');

        $this->assertSame(['page_top', 'topic_list_default'], M::targetsFor('kunena_top'));
    }

    public function testAnAsteriskAloneIsNoPosition(): void
    {
        M::prepareWith('*', 'everything');
        M::prepareWith('', 'nothing');

        $this->assertSame([], M::targetsFor('kunena_top'));
    }
}
