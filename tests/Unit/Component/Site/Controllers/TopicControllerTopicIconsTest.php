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

namespace Kunena\Tests\Unit\Component\Site\Controllers;

use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The task which lists the topic icons of a category put the name of the icon set of the category into the path of a file as it
 * was stored: a name like "../../x" read a topicicons.xml of another folder. The check of the icon of a topic already took the
 * name of one folder only.
 */
class TopicControllerTopicIconsTest extends UnitTestCase
{
    public function testNameOfTheIconSetIsOneFolderBeforeItIsInAPath(): void
    {
        $source = Files::read('src/site/src/Controllers/TopicController.php');

        $this->assertSame(1, preg_match('/public function topicicons\(\)\s*\{(.*?)\n    \}\n/s', $source, $m));

        $check = strpos($m[1], "preg_match('/^[\\w\\-]+\$/D', \$categoryIconset) !== 1");
        $path  = strpos($m[1], "'/media/kunena/topic_icons/' . \$categoryIconset");

        $this->assertIsInt($check, 'The name is checked.');
        $this->assertIsInt($path);
        $this->assertLessThan($path, $check, 'The name is checked before it is a part of a path.');
        $this->assertStringContainsString("\$categoryIconset = 'default';", $m[1], 'Any other name is the default set.');
    }

    /**
     * Without the D modifier "$" also matches before a line break at the end: "default\n" passed as the name of a folder where
     * the icon of a topic is checked.
     */
    public function testNameOfAFolderHasNoLineBreakAtItsEnd(): void
    {
        $source = Files::read('src/site/src/Controllers/TopicController.php');

        $this->assertSame(1, preg_match('/^[\w\-]+$/', "default\n"), 'What the check without D lets through.');
        $this->assertSame(0, preg_match('/^[\w\-]+$/D', "default\n"));

        $this->assertStringContainsString("preg_match('/^[\\w\\-]+\$/D', \$iconset) !== 1", $source);
        $this->assertSame(0, preg_match('/preg_match\(\'\/\^\[\\\\w\\\\-\]\+\$\/\', \$\w+\)/', $source), 'No check of a folder name is left without D.');
    }
}
