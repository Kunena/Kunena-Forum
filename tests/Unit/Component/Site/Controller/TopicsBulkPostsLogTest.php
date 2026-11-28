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
 * The actions on the selected messages of the lists (approve, delete, restore, delete for good) are in the moderation log, as the
 * action on one message: they were not logged at all, a delete for good neither. (The tasks need a whole site: their code is read.)
 */
class TopicsBulkPostsLogTest extends UnitTestCase
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
            'approve'         => ['approve_posts', 'LOG_POST_APPROVE'],
            'delete'          => ['delete_posts', 'LOG_POST_DELETE'],
            'restore'         => ['restore_posts', 'LOG_POST_UNDELETE'],
            'delete for good' => ['permdel_posts', 'LOG_POST_DESTROY'],
        ];
    }

    #[DataProvider('tasks')]
    public function testTheActionOnEachMessageIsLogged(string $task, string $log): void
    {
        $body = self::method('    public function ' . $task . '()');

        $this->assertNotSame('', $body);
        $this->assertStringContainsString('$this->logPost(KunenaLog::' . $log . ', $message);', $body);
    }

    public function testTheLogFollowsTheSettingAndTheActionOnOneMessage(): void
    {
        $body = self::method('    private function logPost(');

        $this->assertStringContainsString('if (!$this->config->logModeration) {', $body);
        $this->assertStringContainsString("['mesid' => \$message->id]", $body);
        $this->assertStringContainsString('$this->me->isModerator($category) ? KunenaLog::TYPE_MODERATION : KunenaLog::TYPE_ACTION', $body);
    }
}
