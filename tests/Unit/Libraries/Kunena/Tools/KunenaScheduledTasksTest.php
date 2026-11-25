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

use Kunena\Forum\Libraries\Tools\KunenaScheduledTasks as T;
use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The tool "Scheduled tasks" tells for each routine of plg_task_kunena whether its task is missing, disabled, failing, late.
 */
class KunenaScheduledTasksTest extends UnitTestCase
{
    private const NOW = 1_800_000_000;

    private static function task(array $values): array
    {
        return $values + [
            'state'          => 1,
            'last_exit_code' => 0,
            'last_execution' => gmdate('Y-m-d H:i:s', self::NOW - 600),
            'next_execution' => gmdate('Y-m-d H:i:s', self::NOW + 600),
        ];
    }

    public function testTheStates(): void
    {
        $this->assertSame('missing', T::state(null, self::NOW));
        $this->assertSame('disabled', T::state(self::task(['state' => 0]), self::NOW));
        $this->assertSame('ok', T::state(self::task([]), self::NOW));
        $this->assertSame('never', T::state(self::task(['last_execution' => null]), self::NOW));
        $this->assertSame('failed', T::state(self::task(['last_exit_code' => 1]), self::NOW));
        $this->assertSame('late', T::state(self::task(['next_execution' => gmdate('Y-m-d H:i:s', self::NOW - 2 * 86400)]), self::NOW));
    }

    public function testALongTaskWhichResumesIsNoFailure(): void
    {
        $this->assertSame('ok', T::state(self::task(['last_exit_code' => 123]), self::NOW));
    }

    public function testTheRoutinesAreThoseOfThePlugin(): void
    {
        $plugin = Files::read('src/plugins/plg_task_kunena/src/Extension/Kunena.php');

        preg_match_all("#^\s+'([a-z]+\.[a-z]+)' => \[#m", $plugin, $matches);

        $this->assertEqualsCanonicalizing($matches[1], array_keys(T::ROUTINES));

        foreach (T::ROUTINES as $routine => $prefix) {
            $this->assertStringContainsString("'langConstPrefix' => '" . $prefix . "'", $plugin, $routine);
        }
    }
}
