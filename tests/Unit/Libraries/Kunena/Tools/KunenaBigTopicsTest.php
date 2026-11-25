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

use Kunena\Forum\Libraries\Tools\KunenaBigTopics as T;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The tool "Big topics" counts the pages of a topic.
 */
class KunenaBigTopicsTest extends UnitTestCase
{
    public function testThePagesOfATopic(): void
    {
        $this->assertSame(1, T::pages(0, 20));
        $this->assertSame(1, T::pages(20, 20));
        $this->assertSame(2, T::pages(21, 20));
        $this->assertSame(50, T::pages(1000, 20));
        $this->assertSame(7, T::pages(7, 0));
    }
}
