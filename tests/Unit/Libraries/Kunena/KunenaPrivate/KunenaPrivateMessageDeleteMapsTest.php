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

namespace Kunena\Tests\Unit\Libraries\Kunena\KunenaPrivate;

use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * A private part which is deleted (an edit which empties it) takes its maps with it, as the delete of its messages does: its users,
 * its message and its files stayed mapped to a private part which was gone (the diagnostics found them). (The delete of the table
 * needs a whole site: the code is read.)
 */
class KunenaPrivateMessageDeleteMapsTest extends UnitTestCase
{
    public function testTheMapsGoWithAPrivateMessage(): void
    {
        $source = str_replace("\r\n", "\n", Files::read('src/libraries/kunena/src/KunenaPrivate/KunenaPrivateMessage.php'));

        $start = strpos($source, '    public function delete(): bool');
        $this->assertNotFalse($start);
        $body = substr($source, $start, (int) strpos($source, "\n    }\n", $start) - $start);

        foreach (['#__kunena_private_user_map', '#__kunena_private_post_map', '#__kunena_private_attachment_map'] as $table) {
            $this->assertStringContainsString("'{$table}'", $body, $table);
        }

        $this->assertStringContainsString("->where(\$db->quoteName('private_id') . ' = ' . \$id)", $body);
        // Only when the private part itself was deleted.
        $this->assertLessThan(strpos($body, '->delete($db->quoteName($table))'), strpos($body, 'if (!parent::delete()) {'));
    }
}
