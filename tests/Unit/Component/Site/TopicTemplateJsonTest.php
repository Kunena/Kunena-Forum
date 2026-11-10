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

namespace Kunena\Tests\Unit\Component\Site;

use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The topic template of a category (json) was given for any category, also a private one.
 */
class TopicTemplateJsonTest extends UnitTestCase
{
    private function before(string $file): string
    {
        $this->assertSame(1, preg_match('/protected function before\(\)[^{]*\{(.*?)\n    \}\n/s', Files::read($file), $m), $file);

        return $m[1];
    }

    public function testTopicTemplateIsForThoseWhoMayStartATopic(): void
    {
        $body = $this->before('src/site/src/Controller/Topic/Json/TopicJsonDisplay.php');

        $this->assertStringContainsString("\$category->exists() && \$category->isAuthorised('topic.create') ? (string) \$category->topictemplate : ''", $body);
        $this->assertSame(1, substr_count($body, 'topictemplate'), 'The template is read in one place, after the check.');
    }
}
