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
 * The code of a smiley has 12 characters in the database, the name of its picture 50. Only "not empty" was checked: a longer
 * code (":thumbsup_big:") was the error of the database, which Joomla runs in strict mode, shown to the administrator as it came,
 * and the smiley was not saved. (The task needs the backend: its code is read.)
 */
class SmiliesControllerLengthTest extends UnitTestCase
{
    public function testALongCodeIsRefusedWithAMessage(): void
    {
        $source = str_replace("\r\n", "\n", Files::read('src/admin/src/Controller/SmiliesController.php'));

        $this->assertSame(1, preg_match('/public function save\(\$key = null, \$urlVar = null\): void\s*\{(.*?)\n    \}\n/s', $source, $m));

        $check = strpos($m[1], 'if (mb_strlen($smileyCode) > 12 || mb_strlen($smileyLocation) > 50) {');
        $this->assertNotFalse($check);
        $this->assertLessThan(strpos($m[1], '->insert('), $check, 'Refused before it is stored.');
        $this->assertStringContainsString("Text::sprintf('COM_KUNENA_SMILEY_TOO_LONG', 12, 50)", $m[1]);
        $this->assertStringContainsString('COM_KUNENA_SMILEY_TOO_LONG = ', Files::read('src/admin/language/en-GB/com_kunena.controllers.ini'));
        $this->assertStringContainsString('name="smileyCode" maxlength="12"', Files::read('src/admin/tmpl/smiley/edit.php'));

        // The sizes of the columns.
        $schema = Files::read('src/admin/sql/install/mysql/create_tables.utf8.sql');
        $this->assertMatchesRegularExpression('/`code`\s+varchar\(12\)/', $schema);
        $this->assertMatchesRegularExpression('/CREATE TABLE IF NOT EXISTS `#__kunena_smileys`.*?`location`\s+varchar\(50\)/s', $schema);
    }
}
