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

use Kunena\Forum\Libraries\Tools\KunenaCategoryPermissions as P;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The tool "Copy category permissions" changes only the fields of the access which differ, and the moderators which differ.
 */
class KunenaCategoryPermissionsTest extends UnitTestCase
{
    public function testOnlyTheFieldsOfTheAccessWhichDiffer(): void
    {
        $source = ['name' => 'A', 'accesstype' => 'joomla.level', 'access' => 2, 'pubAccess' => 1, 'pubRecurse' => 1, 'adminAccess' => 0, 'adminRecurse' => 1];
        $target = ['name' => 'B', 'accesstype' => 'joomla.level', 'access' => 1, 'pubAccess' => 1, 'pubRecurse' => 1, 'adminAccess' => 0, 'adminRecurse' => 1];

        $this->assertSame(['access' => ['from' => '1', 'to' => '2']], P::differences($source, $target));
        $this->assertSame([], P::differences($source, $source));
    }

    public function testTheModeratorsWhichChange(): void
    {
        $this->assertSame(['add' => [7], 'remove' => [9]], P::moderatorChanges([5, 7], [5, 9]));
        $this->assertSame(['add' => [], 'remove' => []], P::moderatorChanges([5], ['5']));
    }

    public function testACategoryIsNotCopiedToItself(): void
    {
        $rows = [1 => ['accesstype' => 'joomla.level', 'access' => 2], 2 => ['accesstype' => 'joomla.level', 'access' => 1]];

        $this->assertSame([2], array_keys(P::previewOnSite($rows, 1, [1, 2, 99], false)));
        $this->assertSame([], P::previewOnSite($rows, 99, [1, 2], false));
    }
}
