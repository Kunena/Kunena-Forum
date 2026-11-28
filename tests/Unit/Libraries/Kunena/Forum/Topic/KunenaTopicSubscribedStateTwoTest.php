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

namespace Kunena\Tests\Unit\Libraries\Kunena\Forum\Topic;

use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * A subscription in the state 2 ("first new post only" after its mail) is a subscription in the latest topics of a user, in the
 * backend edit of a user (listed and ended) and in the edit form of a post (its box was empty, and the save of the edit ended the
 * subscription). (These need a whole site: their code is read.)
 */
class KunenaTopicSubscribedStateTwoTest extends UnitTestCase
{
    public static function places(): array
    {
        return [
            'the latest topics of a user'   => ['src/libraries/kunena/src/Forum/Topic/KunenaTopicHelper.php', "\$whereuser[] = \$db->quoteName('ut.subscribed') . ' > 0';"],
            'the backend list of a user'    => ['src/admin/src/Model/UserModel.php', "\$db->quoteName('subscribed') . ' > 0');"],
            'the backend end of the user'   => ['src/admin/src/Controller/UserController.php', "->where(\$db->quoteName('subscribed') . ' > 0')"],
            'the box of the edit of a post' => ['src/site/src/Controller/Topic/Form/Edit/TopicFormEditDisplay.php', 'if ($usertopic->subscribed > 0 ||'],
        ];
    }

    #[DataProvider('places')]
    public function testTheStateTwoIsASubscription(string $file, string $code): void
    {
        $this->assertStringContainsString($code, Files::read($file));
    }
}
