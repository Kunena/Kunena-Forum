<?php

namespace Kunena\Tests\Unit\Forum\Message;

use Kunena\Forum\Libraries\Forum\Message\KunenaMessage;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopic;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use PHPUnit\Framework\TestCase;

class KunenaMessageSaveFailureTest extends TestCase
{
    public static function saveCases(): array
    {
        return [
            'refused new topic'     => [false, 'before', 1],
            'refused reply'         => [true, 'before', 0],
            'false new topic'       => [false, 'false', 1],
            'false reply'           => [true, 'false', 0],
            'saved new topic'       => [false, 'success', 0],
            'saved reply'           => [true, 'success', 0],
            'late error new topic'  => [false, 'after', 0],
            'late error reply'      => [true, 'after', 0],
        ];
    }

    #[DataProvider('saveCases')]
    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testSaveFailureCleansUpOnlyANewEmptyTopic(bool $existingTopic, string $outcome, int $deletions): void
    {
        \define('_JEXEC', 1);
        require_once __DIR__ . '/fixtures/save_stubs.php';
        require_once __DIR__ . '/../../../../src/libraries/kunena/src/Forum/Message/KunenaMessage.php';

        $topic = new KunenaTopic($existingTopic);
        $message = new class ($topic) extends KunenaMessage {
            public function __construct(private KunenaTopic $topic)
            {
            }

            public function getTopic(): ?KunenaTopic
            {
                return $this->topic;
            }

            protected function updateAttachments(): bool
            {
                return false;
            }

            protected function update($newTopic = false): void
            {
            }
        };
        $message->saveOutcome = $outcome;
        $message->failure = new \RuntimeException('This post was refused.');

        try {
            $result = $message->save();
            $this->assertContains($outcome, ['false', 'success']);
            $this->assertSame($outcome === 'success', $result);
        } catch (\Exception $e) {
            $this->assertContains($outcome, ['before', 'after']);
            $this->assertSame($message->failure, $e);
        }

        $this->assertSame($existingTopic ? 0 : 1, $topic->saved);
        $this->assertSame($deletions, $topic->deleted);
    }

    public function testControllerStopsAfterSaveExceptionAndKeepsPostFields(): void
    {
        $source = file_get_contents(__DIR__ . '/../../../../src/site/src/Controllers/TopicController.php');
        $this->assertSame(1, preg_match('/public function post\(\)\s*\{(.*?)\n    \}\n/s', $source, $method));
        $body = $method[1];

        $this->assertLessThan(strpos($body, '$message->save();'), strpos($body, "setUserState('com_kunena.postfields', \$fields)"));
        $this->assertMatchesRegularExpression(
            '/\$message->save\(\);.*?\} catch \(Exception \$e\) \{\s*\$this->app->enqueueMessage\(\$e->getMessage\(\), \'error\'\);\s*\$this->setRedirectBack\(\);\s*return;\s*\}/s',
            $body
        );
        $this->assertGreaterThan(strpos($body, '$message->save();'), strpos($body, "setUserState('com_kunena.postfields', null)"));
    }
}
