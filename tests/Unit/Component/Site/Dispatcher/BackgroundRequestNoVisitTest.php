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

namespace Kunena\Tests\Unit\Component\Site\Dispatcher;

use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * A request which a page of the forum makes in the background (the refresh of the lists of topics and categories, the poll of the
 * popup notifications, every 30 seconds) is no visit of the member: it moved the time of his visit each time, so a member who left a
 * page open never had a new visit, and "since your last visit" stayed at the visit of days before. The scripts mark these requests,
 * the dispatcher neither moves nor saves the visit for them. (The dispatcher needs a whole site: its code is read.)
 */
class BackgroundRequestNoVisitTest extends UnitTestCase
{
    public function testTheDispatcherDoesNotCountABackgroundRequest(): void
    {
        $source = Files::read('src/site/src/Dispatcher/Dispatcher.php');

        $this->assertStringContainsString("getString('HTTP_X_KUNENA_BACKGROUND', '') === '1';", $source);
        $this->assertStringContainsString('$ksession   = KunenaFactory::getSession(!$background);', $source);
        $this->assertStringContainsString('if (!$background && !$ksession->save()) {', $source);
    }

    public static function scripts(): array
    {
        return [
            'the refresh of the topics'     => ['topiclist.js', 1],
            'the refresh of the categories' => ['categorylist.js', 1],
            'the poll of the notifications' => ['notifications.js', 1],
        ];
    }

    #[DataProvider('scripts')]
    public function testTheScriptsMarkTheirBackgroundRequests(string $script, int $requests): void
    {
        $this->assertSame($requests, substr_count(Files::read('src/site/template/aurelia/assets/js/' . $script), "'X-Kunena-Background': '1'"));
    }
}
