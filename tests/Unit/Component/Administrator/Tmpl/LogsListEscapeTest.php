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

namespace Kunena\Tests\Unit\Component\Administrator\Tmpl;

use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The list of the logs of the backend printed the subject of the topic and the name of the category as they are stored: a member
 * who started a topic with the subject <img src=x onerror=...> ran his script in the session of the administrator who opened the
 * logs (any log of that topic: a move, a lock, a thank you). The other columns were escaped. (The page needs the backend: its
 * code is read.)
 */
class LogsListEscapeTest extends UnitTestCase
{
    public function testTheSubjectAndTheCategoryAreEscaped(): void
    {
        $source = Files::read('src/admin/tmpl/logs/default.php');

        $this->assertStringContainsString("\$this->escape(\$item->topic_subject) . ' <small>('", $source);
        $this->assertStringContainsString("\$this->escape(\$item->category_name) . ' <small>('", $source);
        $this->assertDoesNotMatchRegularExpression('/\?\s*\$item->(topic_subject|category_name)\s*\./', $source);
    }
}
