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

namespace Kunena\Tests\Unit\Component\Site\Template\Agora;

use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The chip "Accepted answer" of the layout Columns of Agora follows the box of the answer: shown when the forum has accepted answers,
 * for an answer the user may read. It was shown with the setting off, for a topic whose answer was accepted before (the box and the
 * actions of the answer hidden).
 */
class ColumnsAnswerChipTest extends UnitTestCase
{
    public function testTheChipFollowsTheSettingAndTheRightToRead(): void
    {
        $layout = Files::read('src/site/template/agora/layoutsets/columns/layouts/topic/item/default.php');

        $this->assertStringContainsString('(!isset($config->acceptedAnswers) || $config->acceptedAnswers) ? KunenaTopicAnswer::getAnswer($this->topic) : null;', $layout);
        $this->assertStringContainsString("\$solved        = \$answer !== null && \$answer->isAuthorised('read');", $layout);

        // The box of the answer of Aurelia, which the chip follows.
        $box = Files::read('src/site/template/aurelia/layouts/topic/item/answer/default.php');

        $this->assertStringContainsString('if (isset($config->acceptedAnswers) && !$config->acceptedAnswers) {', $box);
        $this->assertStringContainsString("if (!\$answer || !\$answer->isAuthorised('read')) {", $box);
    }
}
