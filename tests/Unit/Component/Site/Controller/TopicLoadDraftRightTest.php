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
 * Opening a draft checks the right which its form checks (edit, reply or create) before its fields are put in the session: a draft
 * which may not be used any more (the edit time is over, the topic is locked) was refused by the form after that, and its subject,
 * text and private part came back in the next form which the user opened. (The task needs a whole site: its code is read.)
 */
class TopicLoadDraftRightTest extends UnitTestCase
{
    public function testTheRightIsCheckedBeforeTheFieldsAreKept(): void
    {
        $source = str_replace("\r\n", "\n", Files::read('src/site/src/Controllers/TopicController.php'));

        $start = strpos($source, '    public function loaddraft()');
        $this->assertNotFalse($start);
        $body = substr($source, $start, (int) strpos($source, "\n    }\n", $start) - $start);

        foreach ([
            "KunenaMessageHelper::get((int) \$draft->message_id)->isAuthorised('edit')",
            "KunenaMessageHelper::get((int) \$draft->parent_id)->isAuthorised('reply')",
            "KunenaCategoryHelper::get((int) \$draft->catid)->isAuthorised('topic.create')",
        ] as $check) {
            $this->assertStringContainsString($check, $body);
        }

        $this->assertLessThan(strpos($body, "setUserState('com_kunena.postfields'"), strpos($body, 'if (!$allowed) {'));
    }
}
