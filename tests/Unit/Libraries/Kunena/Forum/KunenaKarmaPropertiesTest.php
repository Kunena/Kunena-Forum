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

namespace Kunena\Tests\Unit\Libraries\Kunena\Forum;

use Kunena\Forum\Libraries\Forum\Message\Karma\KunenaKarma;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * KunenaKarma binds all the properties of its table row, so the class must declare them (PHP 8.2 deprecates a dynamic property).
 */
class KunenaKarmaPropertiesTest extends UnitTestCase
{
    public function testTheClassDeclaresTheIdentifiersOfTheTable(): void
    {
        foreach (['id', 'typeAlias', 'userid', 'target_userid', 'time'] as $property) {
            $this->assertTrue(property_exists(KunenaKarma::class, $property), "KunenaKarma::\${$property} is declared");
        }
    }
}
