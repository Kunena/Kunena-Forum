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

use Kunena\Forum\Libraries\Layout\KunenaLayout;
use Kunena\Tests\Unit\UnitTestCase;
use ReflectionProperty;

/**
 * A page can show the same list more than once (a list of topics in each tab of a profile): its ids stay unique on the page.
 * The first one keeps its id, for the scripts and the styles which look for it.
 */
class KunenaLayoutTest extends UnitTestCase
{
    private array $saved = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->requireJoomla();

        $property    = new ReflectionProperty(KunenaLayout::class, 'uniqueIds');
        $this->saved = $property->getValue();
        $property->setValue(null, []);
    }

    protected function tearDown(): void
    {
        (new ReflectionProperty(KunenaLayout::class, 'uniqueIds'))->setValue(null, $this->saved);

        parent::tearDown();
    }

    public function testTheFirstElementKeepsItsIdTheNextOnesGetANumber(): void
    {
        $layout = self::bare(KunenaLayout::class);

        $this->assertSame('ktopicsform', $layout->uniqueId('ktopicsform'));
        $this->assertSame('ktopicsform-2', $layout->uniqueId('ktopicsform'));
        $this->assertSame('ktopicsform-3', $layout->uniqueId('ktopicsform'));
    }

    public function testEachIdIsCountedOnItsOwnAndForTheWholePage(): void
    {
        // Two layouts (the list of a tab and the list of another tab) share the count of the page.
        $first  = self::bare(KunenaLayout::class);
        $second = self::bare(KunenaLayout::class);

        $this->assertSame('forumtop', $first->uniqueId('forumtop'));
        $this->assertSame('forumbottom', $first->uniqueId('forumbottom'));
        $this->assertSame('forumtop-2', $second->uniqueId('forumtop'));
        $this->assertSame('forumbottom-2', $second->uniqueId('forumbottom'));
    }
}
