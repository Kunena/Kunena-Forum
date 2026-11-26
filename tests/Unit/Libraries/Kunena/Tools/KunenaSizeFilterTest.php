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

use Kunena\Forum\Libraries\Tools\KunenaSizeFilter as T;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The size filter of the attachments reads a size, a bound or a range.
 */
class KunenaSizeFilterTest extends UnitTestCase
{
    public function testASize(): void
    {
        $this->assertSame(500, T::bytes('500'));
        $this->assertSame(512000, T::bytes('500k'));
        $this->assertSame(1572864, T::bytes('1.5 MB'));
        $this->assertSame(1572864, T::bytes('1,5M'));
        $this->assertSame(2147483648, T::bytes('2GB'));
        $this->assertNull(T::bytes('big'));
        $this->assertNull(T::bytes('5 TB'));
        $this->assertNull(T::bytes(''));
    }

    public function testTheRangeOfAFilter(): void
    {
        $this->assertSame([1024, 1024], T::range('1k'));
        $this->assertSame([1024, 1024], T::range('=1k'));
        $this->assertSame([null, 1023], T::range('<1k'));
        $this->assertSame([null, 1024], T::range('<= 1 kB'));
        $this->assertSame([2097153, null], T::range('>2MB'));
        $this->assertSame([2097152, null], T::range('>=2M'));
        $this->assertSame([1048576, 5242880], T::range('1M-5M'));
        $this->assertSame([1048576, 5242880], T::range('5M-1M'));
        $this->assertSame([null, 0], T::range('<0'));
    }

    public function testNoSize(): void
    {
        $this->assertNull(T::range('photo.jpg'));
        $this->assertNull(T::range('1M-big'));
        $this->assertNull(T::range('>'));
    }
}
