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

namespace Kunena\Tests\Unit\Component\Site\Controller\Topic\Form;

use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The box "Notify me" of the forms:
 * - of a new topic: the choice of the member ("Select subscription as default" of his settings), as the reply takes it, and the
 *   default of the forum when he has none; only the default of the forum was taken, so a member who chose No was subscribed to
 *   every topic he started;
 * - of an edit: checked when the user follows the topic now; the default of the forum checked it always, and the save of a small
 *   correction subscribed again a user who had left the topic.
 * (The forms need a whole site: their code is read.)
 */
class TopicFormSubscriptionDefaultTest extends UnitTestCase
{
    public function testTheNewTopicTakesTheChoiceOfTheMember(): void
    {
        $source = Files::read('src/site/src/Controller/Topic/Form/Create/TopicFormCreateDisplay.php');

        $this->assertStringContainsString("(int) (\$this->me->canSubscribe ?? -1) === -1", $source);
        $this->assertStringContainsString('$this->subscriptionsChecked = (bool) $this->me->canSubscribe;', $source);
    }

    public function testTheEditShowsTheSubscriptionOfTheUser(): void
    {
        $source = Files::read('src/site/src/Controller/Topic/Form/Edit/TopicFormEditDisplay.php');

        $this->assertStringNotContainsString('$this->config->subscriptionsChecked', $source);
        $this->assertStringContainsString('if ($usertopic->subscribed == 1 || $this->category->getSubscribed($this->me->userid)) {', $source);
    }
}
