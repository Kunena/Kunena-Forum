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

namespace Kunena\Tests\Unit\Component\Site\Template\Aurelia;

use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The moderators line at the bottom of a topic has its own option (topicShowModerators): the option is in the configuration with
 * its texts, and the topic layout renders the line only when the option is on.
 */
class TopicItemModeratorsTest extends UnitTestCase
{
    private const LAYOUT = 'src/site/template/aurelia/layouts/topic/item/default.php';

    public function testTheOptionIsInTheConfiguration(): void
    {
        $xml    = simplexml_load_string(Files::read('src/admin/config.xml'));
        $fields = $xml->xpath('//field[@name="topicShowModerators"]');

        $this->assertCount(1, $fields, 'config.xml has no field topicShowModerators.');
        $this->assertSame('radio', (string) $fields[0]['type']);
        $this->assertSame('1', (string) $fields[0]['default']);

        $strings = Files::languageFolder('src/admin/language/en-GB');

        $this->assertArrayHasKey('COM_KUNENA_TOPIC_SHOW_MODERATORS', $strings);
        $this->assertArrayHasKey('COM_KUNENA_TOPIC_SHOW_MODERATORS_DESC', $strings);
    }

    public function testTheTopicLayoutRendersTheModeratorsOnlyWhenTheOptionIsOn(): void
    {
        $content = Files::read(self::LAYOUT);

        $this->assertSame(1, substr_count($content, "'Category/Moderators'"), 'The topic layout must render the moderators once.');

        $condition = strpos($content, 'if ($this->config->topicShowModerators)');

        $this->assertNotFalse($condition, 'The topic layout does not check topicShowModerators.');
        $this->assertLessThan(strpos($content, "'Category/Moderators'"), $condition, 'The moderators are rendered before the check of topicShowModerators.');
        $this->assertLessThan(strpos($content, 'class="float-end"><?php echo $this->subLayout(\'Category/Moderators\')'), $condition, 'The wrapper of the moderators is outside the check.');
    }
}
