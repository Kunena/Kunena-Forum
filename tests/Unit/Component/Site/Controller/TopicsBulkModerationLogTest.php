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
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * The moderation log of the actions on the selected topics of a list (delete for good, delete, restore, approve) has the topics on
 * which the action was done: it had an entry for every topic of the request once one of them was done, also the refused ones (a
 * category which the moderator does not moderate) and, for the approval, the ones which did not wait for it. The move of the
 * selected topics was fixed so already. (The tasks need a whole site: their code is read.)
 */
class TopicsBulkModerationLogTest extends UnitTestCase
{
    private static function method(string $signature): string
    {
        $source = str_replace("\r\n", "\n", Files::read('src/site/src/Controllers/TopicsController.php'));
        $start  = strpos($source, $signature);

        return $start === false ? '' : substr($source, $start, (int) strpos($source, "\n    }\n", $start) - $start);
    }

    public static function tasks(): array
    {
        return [
            'delete for good' => ['permdel', 'LOG_TOPIC_DESTROY'],
            'delete'          => ['delete', 'LOG_TOPIC_DELETE'],
            'restore'         => ['restore', 'LOG_TOPIC_UNDELETE'],
            'approve'         => ['approve', 'LOG_TOPIC_APPROVE'],
        ];
    }

    #[DataProvider('tasks')]
    public function testOnlyTheTopicsOnWhichTheActionWasDoneAreLogged(string $task, string $log): void
    {
        $body = self::method('    public function ' . $task . '()');

        $this->assertNotSame('', $body);
        $this->assertStringContainsString('KunenaLog::' . $log, $body);
        $this->assertStringContainsString('$done[] = $topic;', $body);
        $this->assertStringNotContainsString('foreach ($topics as $topic) {' . "\n" . '                    KunenaLog::log(', $body);
        $this->assertStringContainsString('foreach ($done as $topic) {' . "\n" . '                    KunenaLog::log(', $body);
    }
}
