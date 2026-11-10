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

namespace Kunena\Tests\Unit\Component\Site;

use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The error of a page which does not exist said the name of the class which was built from the request.
 */
class MissingPageErrorTest extends UnitTestCase
{
    public function testErrorOfAMissingPageDoesNotNameTheClass(): void
    {
        $source = Files::read('src/libraries/kunena/src/Request/KunenaRequest.php');

        $this->assertStringContainsString("throw new InvalidArgumentException('The page does not exist.', 404);", $source);
        $this->assertDoesNotMatchRegularExpression('/InvalidArgumentException\([^;]*\$classnamespaced/', $source);
    }
}
