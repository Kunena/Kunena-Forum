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
 * "User can send own report" off: a member reports no post of his own (a moderator may), in the task and in the form of the report,
 * as the button: only the button followed the option, so the address of the form or a request reported the own post (mails and
 * notifications to the moderators, the log). (The task and the form need a whole site: their code is read.)
 */
class TopicReportOwnPostTest extends UnitTestCase
{
    public static function places(): array
    {
        return [
            'the task' => [
                'src/site/src/Controllers/TopicController.php',
                "if (!\$this->config->userReport && (int) \$message->userid === (int) \$this->me->userid && !\$this->me->isModerator(\$topic->getCategory())) {",
            ],
            'the form' => [
                'src/site/src/Controller/Topic/Report/TopicReportDisplay.php',
                "if (!\$this->config->userReport && (int) \$reported->userid === (int) \$me->userid && !\$me->isModerator(\$this->category)) {",
            ],
        ];
    }

    #[DataProvider('places')]
    public function testTheOptionIsApplied(string $file, string $rule): void
    {
        $this->assertStringContainsString($rule, Files::read($file));
    }
}
