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
 * The log of the move of one topic has the category which the topic left (the target is in the data). It was the category of the
 * topic after the move: for a move without a shadow that is the target, so the target was logged twice and the move was in the
 * logs of no source category (with a shadow it was the old category). (The task needs a whole site: its code is read.)
 */
class TopicMoveLogCategoryTest extends UnitTestCase
{
    public function testTheLogHasTheCategoryWhichTheTopicLeft(): void
    {
        $source = str_replace("\r\n", "\n", Files::read('src/site/src/Controllers/TopicController.php'));

        $before = strpos($source, '$sourceCategory = $topic->getCategory();');
        $move   = strpos($source, '$targetobject   = $topic->move($target, $ids, $shadow, $subject, $changesubject, $topic_emoticon, $keep_poll);');
        $log    = strpos($source, '$messageId ? KunenaLog::LOG_POST_MODERATE : KunenaLog::LOG_TOPIC_MODERATE,');

        $this->assertNotFalse($before);
        $this->assertLessThan($move, $before, 'The category is read before the move.');
        $this->assertMatchesRegularExpression('/\'shadow\' => \$shadow\],\n\s*\],\n(\s*\/\/[^\n]*\n)*\s*\$sourceCategory,/', substr($source, $log, 1200));
    }
}
