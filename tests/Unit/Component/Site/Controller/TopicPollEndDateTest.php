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

use Kunena\Forum\Site\Controllers\TopicController;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * A poll without an end keeps it when its topic is edited. Its end is stored as 1000-01-01, and the poll dialog of CKEditor sends
 * that date back: it was the end of that day, so any edit of the topic closed the poll ("poll expired").
 */
class TopicPollEndDateTest extends UnitTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->requireJoomla();
    }

    private function endDate(string $value): ?string
    {
        $controller = (new \ReflectionClass(TopicController::class))->newInstanceWithoutConstructor();

        return (new \ReflectionMethod(TopicController::class, 'pollEndDate'))->invoke($controller, $value);
    }

    public function testTheDateOfNoEndIsNoEnd(): void
    {
        $this->assertNull($this->endDate('1000-01-01'));
        $this->assertNull($this->endDate('1000-01-01 00:00:00'));

        // A real day is the end of that day.
        $this->assertSame('2030-05-01 23:59:59', $this->endDate('2030-05-01'));
    }
}
