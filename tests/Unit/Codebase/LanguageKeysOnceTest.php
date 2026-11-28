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

namespace Kunena\Tests\Unit\Codebase;

use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * A key of a language file of en-GB is defined once: the last one wins, so a key defined twice showed the other text than the one
 * which was edited (COM_KUNENA_LOG_USER_WARNING was "User Warned" and "User Warning", COM_KUNENA_THANKYOU_LOGIN had two texts).
 */
class LanguageKeysOnceTest extends UnitTestCase
{
    public function testEveryKeyIsDefinedOnce(): void
    {
        $twice = [];
        $files = 0;

        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator(Files::path('src'))) as $file) {
            $path = str_replace(\DIRECTORY_SEPARATOR, '/', (string) $file);

            if (!preg_match('#/language/en-GB/[^/]+\.ini$#', $path)) {
                continue;
            }

            $files++;
            $seen = [];

            foreach (file($path) as $line) {
                if (preg_match('/^([A-Z0-9_]+)\s*=/', $line, $m)) {
                    if (isset($seen[$m[1]])) {
                        $twice[] = basename($path) . ': ' . $m[1];
                    }

                    $seen[$m[1]] = true;
                }
            }
        }

        $this->assertGreaterThan(10, $files);
        $this->assertSame([], $twice);
    }
}
