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

namespace Kunena\Tests\Unit\Libraries\Kunena\Access;

use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The categories a user may read are kept in the session. For the visitor the list is cleared on every request; the list of
 * another user (the name search asks whether a found member may read a category) stayed in the session of the visitor until it
 * ended, so that member was judged by his old rights after they changed. Only the list of the visitor is kept.
 */
class KunenaAccessOtherUserSessionTest extends UnitTestCase
{
    public function testOnlyTheListOfTheVisitorIsReadFromAndWrittenToTheSession(): void
    {
        $source = Files::read('src/libraries/kunena/src/Access/KunenaAccess.php');

        $this->assertSame(1, preg_match('/public function getAllowedCategories\(.*?\n    \}\n/s', $source, $m));

        $this->assertStringContainsString('$own = (int) $id === (int) KunenaUserHelper::getMyself()->userid;', $m[0]);
        $this->assertStringContainsString('$read[$id] = $own ? $app->getUserState("com_kunena.user{$id}_read") : null;', $m[0]);
        $this->assertSame(1, preg_match('/if \(\$own\) \{\s+\$app->setUserState\("com_kunena\.user\{\$id\}_read", \$read\[\$id\]\);/', $m[0]));
        $this->assertSame(1, substr_count($m[0], 'setUserState('), 'No other write of the list to the session.');
        $this->assertSame(1, substr_count($m[0], 'getUserState('), 'No other read of the list from the session.');
    }
}
