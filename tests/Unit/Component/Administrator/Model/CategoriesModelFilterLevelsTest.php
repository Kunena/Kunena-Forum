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

namespace Kunena\Tests\Unit\Component\Administrator\Model;

use Kunena\Forum\Administrator\Model\CategoriesModel;
use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * Categories of the backend with filter[levels]=x in the address: "Unsupported operand types: string - int". The filter is kept
 * in the session, so the list ended in the error page on every visit until the session ended.
 */
class CategoriesModelFilterLevelsTest extends UnitTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->requireJoomla();
    }

    public function testLevelsOfTheFilterAreANumber(): void
    {
        $this->assertSame(0, CategoriesModel::filterLevels(''));
        $this->assertSame(0, CategoriesModel::filterLevels(null));
        $this->assertSame(2, CategoriesModel::filterLevels('2'));
        $this->assertSame(3, CategoriesModel::filterLevels(3));
        $this->assertSame(0, CategoriesModel::filterLevels('x'));
        $this->assertSame(0, CategoriesModel::filterLevels("kx9'\")) OR 1=1 -- "));
        $this->assertSame(0, CategoriesModel::filterLevels('-4'));
        $this->assertSame(0, CategoriesModel::filterLevels(['2']));
    }

    public function testTheListCountsWithTheNumberOnly(): void
    {
        $source = Files::read('src/admin/src/Model/CategoriesModel.php');

        $this->assertStringContainsString("\$levels     = self::filterLevels(\$this->getState('filter.levels', ''));", $source);
        $this->assertStringNotContainsString("? 0 : \$this->getState('filter.levels')", $source, 'The value of the request is not counted with.');
    }
}
