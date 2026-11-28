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

namespace Kunena\Tests\Unit\Component\Administrator\Controller;

use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The tool "Set up the language column" tells a failure of the database: the error was printed into the redirect, where nobody saw
 * it, and the tool said that the column was created. (The tool needs a whole site: its code is read.)
 */
class ToolsColumnLanguageErrorTest extends UnitTestCase
{
    public function testAFailureIsTold(): void
    {
        $source = str_replace("\r\n", "\n", Files::read('src/admin/src/Controller/ToolsController.php'));
        $start  = strpos($source, '    public function setupcolumnlanguage(): void');
        $body   = substr($source, $start, (int) strpos($source, "\n    }\n", $start) - $start);
        $catch  = strpos($body, '} catch (Exception $e) {');

        $this->assertNotFalse($catch);

        $branch = substr($body, $catch, (int) strpos($body, "\n            }\n", $catch) - $catch);

        $this->assertStringContainsString("\$this->app->enqueueMessage(\$e->getMessage(), 'error');", $branch);
        $this->assertStringContainsString('return;', $branch);
        $this->assertStringNotContainsString('echo ', $branch);
    }
}
