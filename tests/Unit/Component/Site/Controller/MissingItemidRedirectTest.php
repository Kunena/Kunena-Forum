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

namespace Kunena\Tests\Unit\Component\Site\Controller;

use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * A page asked without an Itemid is sent to the menu item of the forum ("SEF redirect") only when there is one: without a menu item
 * which the visitor may see (a forum of registered users for a guest, a language without a menu item of the forum), the Itemid was 0
 * and the page was sent to the same address again, until the browser gave up. (The pages need a whole site: their code is read.)
 */
class MissingItemidRedirectTest extends UnitTestCase
{
    public function testEveryRedirectNeedsAnItemid(): void
    {
        $redirect = "return \$this->app->redirect(KunenaRoute::_('index.php?' . http_build_query(KunenaRoute::keepQuery(\$params)), false), 301);";
        $pages    = 0;

        $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(Files::path('src/site/src/Controller'), \FilesystemIterator::SKIP_DOTS));

        foreach ($files as $file) {
            $file = (string) $file;

            if (!str_ends_with($file, 'Display.php')) {
                continue;
            }

            $source = str_replace("\r\n", "\n", (string) file_get_contents($file));

            if (!str_contains($source, 'KunenaRoute::fixMissingItemID()')) {
                continue;
            }

            $pages++;
            $lines = explode("\n", $source);

            foreach ($lines as $i => $line) {
                if (trim($line) === $redirect) {
                    $this->assertSame("if (!empty(\$params['Itemid'])) {", trim($lines[$i - 1]), basename($file) . ' line ' . ($i + 1));
                }
            }
        }

        $this->assertGreaterThan(15, $pages);
    }
}
