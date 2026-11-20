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

namespace Kunena\Tests\Unit\Libraries\Kunena\User;

use Kunena\Forum\Libraries\User\KunenaGuestName;
use Kunena\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * The name of a guest: invisible characters, other spaces and compatibility forms of letters can not make the name of an
 * account look different to the check.
 */
class KunenaGuestNameTest extends UnitTestCase
{
    public static function names(): array
    {
        return [
            'a plain name'              => ['Visitor', 'Visitor'],
            'spaces around'             => ["  Visitor \t", 'Visitor'],
            'zero width space'          => ["ad\u{200B}min", 'admin'],
            'zero width joiner'         => ["ad\u{200D}min", 'admin'],
            'right to left override'    => ["\u{202E}admin", 'admin'],
            'soft hyphen'               => ["ad\u{00AD}min", 'admin'],
            'byte order mark'           => ["\u{FEFF}admin", 'admin'],
            'no-break space'            => ["Jane\u{00A0}Doe", 'Jane Doe'],
            'several spaces'            => ['Jane    Doe', 'Jane Doe'],
            'fullwidth letters'         => ["\u{FF41}\u{FF44}\u{FF4D}\u{FF49}\u{FF4E}", 'admin'],
            'only invisible characters' => ["\u{200B}\u{200C}", ''],
            'not UTF-8'                 => ["adm\xFFin", ''],
        ];
    }

    #[DataProvider('names')]
    public function testClean(string $name, string $expected): void
    {
        if (str_contains($name, "\u{FF41}") && !class_exists('Normalizer')) {
            $this->markTestSkipped('The intl extension is not loaded.');
        }

        $this->assertSame($expected, KunenaGuestName::clean($name));
    }

    public function testTheNameIsLimitedToTheLengthOfTheField(): void
    {
        $this->assertSame(KunenaGuestName::MAX_LENGTH, mb_strlen(KunenaGuestName::clean(str_repeat('é', 50))));
    }
}
