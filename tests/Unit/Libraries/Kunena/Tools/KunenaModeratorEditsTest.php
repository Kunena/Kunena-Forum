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

use Kunena\Forum\Libraries\Tools\KunenaModeratorEdits as T;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The tool "Moderator edits" counts the edits per editor and the edits without a reason.
 */
class KunenaModeratorEditsTest extends UnitTestCase
{
    public function testTheEditsPerEditor(): void
    {
        $editors = T::editors([
            ['modified_by' => 2, 'modified_reason' => 'Spelling'],
            ['modified_by' => 3, 'modified_reason' => ''],
            ['modified_by' => 3, 'modified_reason' => '  '],
            ['modified_by' => 3, 'modified_reason' => 'Link removed'],
        ]);

        $this->assertSame([3 => ['edits' => 3, 'noreason' => 2], 2 => ['edits' => 1, 'noreason' => 0]], $editors);
    }
}
