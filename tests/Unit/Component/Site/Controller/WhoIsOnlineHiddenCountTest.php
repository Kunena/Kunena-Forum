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
 * Who is online: the number of members is the number of the members whom the viewer sees. A member who hides that he is online
 * (or chose "Invisible") is left out of the list for all but the moderators, but he was counted: "3 members" with 2 names told a
 * guest when the hidden member came and went. (The widget and the view need a whole site: their code is read.)
 */
class WhoIsOnlineHiddenCountTest extends UnitTestCase
{
    public function testTheNumberOfMembersIsTheNumberOfTheShownMembers(): void
    {
        foreach (
            [
                'src/site/src/Controller/Widget/Whoisonline/WidgetWhoisonlineDisplay.php' => '$this->onlineList',
                'src/site/src/View/Common/HtmlView.php'                                    => '$onlineList',
            ] as $file => $list
        ) {
            $source = str_replace("\r\n", "\n", Files::read($file));
            $count  = strpos($source, "\$members = \$moderator ? (int) \$onlineusers['user'] : \\count(" . $list . ');');

            $this->assertNotFalse($count, $file);
            $this->assertStringContainsString("\$who = '<strong>' . \$members . ' </strong>';", $source, $file);
            $this->assertStringNotContainsString("\$who = '<strong>' . \$onlineusers['user']", $source, $file);
            // The list is made before it is counted.
            $this->assertLessThan($count, strpos($source, $list . "[\$user->getName() . '|' . (int) \$userid] = \$user;"), $file);
        }
    }

    /**
     * Two members of the same name (the forum shows the real names) are two in the lists: the lists were keyed by the name, so
     * they were one, and the number of the members was one too low.
     */
    public function testTwoMembersOfTheSameNameAreTwo(): void
    {
        foreach (
            [
                'src/site/src/Controller/Widget/Whoisonline/WidgetWhoisonlineDisplay.php',
                'src/site/src/View/Common/HtmlView.php',
            ] as $file
        ) {
            $source = Files::read($file);

            $this->assertStringNotContainsString('List[$user->getName()] = $user;', $source, $file);
            $this->assertSame(2, substr_count($source, "List[\$user->getName() . '|' . (int) \$userid] = \$user;"), $file);
        }
    }
}
