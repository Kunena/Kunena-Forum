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

use Kunena\Forum\Libraries\Tools\KunenaLanguageOverrides as T;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The tool "Language overrides check" finds overrides of Kunena texts which no longer fit their text.
 */
class KunenaLanguageOverridesTest extends UnitTestCase
{
    public function testTheProblemsOfTheOverrides(): void
    {
        $problems = T::check([
            'COM_KUNENA_GONE'       => 'Old text',
            'COM_KUNENA_BY'         => 'door %s %s',
            'COM_KUNENA_COUNT'      => 'Aantal',
            'COM_KUNENA_FINE'       => 'Goed %d',
            'COM_CONTENT_READ_MORE' => 'Lees meer',
        ], [
            'COM_KUNENA_BY'    => 'by %s',
            'COM_KUNENA_COUNT' => '%d posts',
            'COM_KUNENA_FINE'  => 'Fine %d',
        ]);

        $this->assertSame(['COM_KUNENA_GONE' => T::UNKNOWN, 'COM_KUNENA_BY' => 'extra', 'COM_KUNENA_COUNT' => 'dropped'], array_column($problems, 'kind', 'key'));
        $this->assertSame('by %s', $problems[1]['english']);
    }

    public function testTheOverridesOfAForm(): void
    {
        $files = T::parse(['site|nl-NL|COM_KUNENA_BY', 'administrator|en-GB|PLG_KUNENA_X', 'site|nl-NL|COM_CONTENT_X', 'site|../x|COM_KUNENA_BY', 'other|nl-NL|COM_KUNENA_BY', 'site|nl-NL|COM_KUNENA_BY"']);

        $this->assertSame([
            T::folder('site') . '/nl-NL.override.ini'          => ['COM_KUNENA_BY'],
            T::folder('administrator') . '/en-GB.override.ini' => ['PLG_KUNENA_X'],
        ], $files);
    }

    public function testRemovingFromAFile(): void
    {
        $file = sys_get_temp_dir() . '/kunena-override-' . uniqid() . '.ini';
        file_put_contents($file, "COM_KUNENA_BY=\"door %s\"\nCOM_CONTENT_X=\"Blijft\"\n");

        try {
            $this->assertSame(1, T::removeOnSite([$file => ['COM_KUNENA_BY', 'COM_KUNENA_NOT_THERE']]));
            $this->assertSame(['COM_CONTENT_X' => 'Blijft'], parse_ini_file($file));
        } finally {
            @unlink($file);
        }
    }
}
