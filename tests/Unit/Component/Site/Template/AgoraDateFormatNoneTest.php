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

namespace Kunena\Tests\Unit\Component\Site\Template;

use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * With the date format "None" (postDateFormat 'none') the Dense and Columns layout sets of Agora showed no date, as the other
 * layouts do: the link to the last post of a Dense row had the empty date as its only text (an invisible link on a phone and in the
 * last-post column), and the topic of Columns showed "Topic started" alone and "Topic started on  by X". (Layouts: their code is
 * read.)
 */
class AgoraDateFormatNoneTest extends UnitTestCase
{
    public function testTheDenseRowLinksTheLastPostWithAText(): void
    {
        $source = Files::read('src/site/template/agora/layoutsets/dense/layouts/topic/row/default.php');

        $this->assertStringContainsString("\$config->postDateFormat != 'none' ? \$topic->getLastPostTime()->toKunena('config_postDateFormat') : Text::_('COM_KUNENA_GEN_LAST_POST')", $source);
        $this->assertStringNotContainsString("getTopicLink(\$topic, 'last', \$topic->getLastPostTime()", $source);
    }

    public function testTheColumnsTopicShowsNoEmptyDate(): void
    {
        $source = Files::read('src/site/template/agora/layoutsets/columns/layouts/topic/item/default.php');

        foreach (["getFirstPostTime()->toKunena('config_postDateFormat')", "getLastPostTime()->toKunena('config_postDateFormat')"] as $date) {
            $offset = 0;

            while (($position = strpos($source, $date, $offset)) !== false) {
                $before = substr($source, 0, $position);
                $this->assertGreaterThan(
                    (int) strrpos($before, 'endif;'),
                    (int) strrpos($before, "postDateFormat != 'none'"),
                    $date . ' at ' . $position . ' is not inside a check of the date format'
                );
                $offset = $position + 1;
            }
        }
    }
}
