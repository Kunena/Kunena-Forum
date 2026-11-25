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

namespace Kunena\Tests\Unit\Component\Administrator\Tmpl;

use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * The forms of a rank and of a smiley printed the stored title and code into the attributes value and alt as they were: a rank
 * named Senior "Boarder" lost the rest of its title in the form, and markup in the value ran for the administrator who opened
 * the form.
 */
class RankAndSmileyEditFormTest extends UnitTestCase
{
    public static function forms(): array
    {
        return [
            'rank'   => ['src/admin/tmpl/rank/edit.php', '$this->rankSelected->rankTitle', 3],
            'smiley' => ['src/admin/tmpl/smiley/edit.php', '$this->smileySelected->code', 2],
        ];
    }

    #[DataProvider('forms')]
    public function testStoredValueIsEncodedInEveryAttribute(string $file, string $value, int $places): void
    {
        $source = Files::read($file);

        $this->assertSame($places, substr_count($source, $value), 'The value is printed in ' . $places . ' places.');
        $this->assertSame($places, substr_count($source, '$this->escape((string) (' . $value . ' ??'), 'Every one of them is encoded.');
        $this->assertSame(0, preg_match('/echo isset\([^)]*\) \? ' . preg_quote($value, '/') . ' :/', $source), 'None prints the value as it is stored.');
    }
}
