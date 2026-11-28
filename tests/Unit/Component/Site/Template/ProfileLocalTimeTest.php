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

namespace Kunena\Tests\Unit\Component\Site\Template;

use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * "Local time" of a profile is the time of the member: toKunena() without a time zone puts the date into the time zone of the
 * visitor, so the profile of a member in Tokyo showed the clock of a visitor in UTC, nine hours off, under "Timezone: UTC +9:00".
 * (The layouts need a whole site: their code is read.)
 */
class ProfileLocalTimeTest extends UnitTestCase
{
    public static function layouts(): array
    {
        return [
            'aurelia' => ['src/site/template/aurelia/layouts/user/item/summary/default.php'],
            'agora'   => ['src/site/template/agora/layouts/user/item/summary/default.php'],
        ];
    }

    #[DataProvider('layouts')]
    public function testTheLocalTimeIsInTheTimeZoneOfTheMember(string $file): void
    {
        $source = Files::read($file);

        $this->assertStringContainsString("\$this->profile->getTime()->toKunena('time', \$this->profile->getTime()->getTimezone()->getName())", $source);
        $this->assertStringNotContainsString("getTime()->toKunena('time')", $source);
    }
}
