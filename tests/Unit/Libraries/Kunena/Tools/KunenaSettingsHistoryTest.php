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

use Kunena\Forum\Libraries\Tools\KunenaSettingsHistory as T;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The tool "Settings history" records the settings a save changed, without the values of the secrets.
 */
class KunenaSettingsHistoryTest extends UnitTestCase
{
    public function testTheSettingsWhichChanged(): void
    {
        $changes = T::diff(
            ['board_title' => 'Forum', 'enabled' => true, 'apiKey' => 'old', 'same' => ' x ', 'badges' => ['a'], 'gone' => '1'],
            ['board_title' => 'My forum', 'enabled' => '1', 'apiKey' => 'new', 'same' => 'x', 'badges' => ['b'], 'added' => '2']
        );

        $this->assertSame([
            'added'       => ['', '2'],
            'apiKey'      => [T::HIDDEN, T::HIDDEN],
            'board_title' => ['Forum', 'My forum'],
            'gone'        => ['1', ''],
        ], $changes);
    }

    public function testTheEntriesOfTheLog(): void
    {
        $entries = T::entries([
            ['id' => 9, 'user_id' => 42, 'username' => 'admin', 'time' => 100, 'data' => json_encode(['source' => 'import', 'changes' => ['a' => ['1', '2']]])],
            ['id' => 8, 'user_id' => 42, 'username' => null, 'time' => 50, 'data' => 'not json'],
            ['id' => 7, 'user_id' => 0, 'time' => 10, 'data' => json_encode(['changes' => ['b' => ['x', 'y'], 'c' => 'broken']])],
        ]);

        $this->assertSame([9, 7], array_column($entries, 'id'));
        $this->assertSame('import', $entries[0]['source']);
        $this->assertSame('config', $entries[1]['source']);
        $this->assertSame(['b' => ['x', 'y']], $entries[1]['changes']);
    }
}
