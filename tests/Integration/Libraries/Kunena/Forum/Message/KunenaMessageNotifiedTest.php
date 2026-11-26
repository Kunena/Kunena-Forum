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

namespace Kunena\Tests\Integration\Libraries\Kunena\Forum\Message;

use Kunena\Forum\Libraries\Forum\KunenaForum;
use Kunena\Forum\Libraries\Forum\Message\KunenaMessage;
use Kunena\Tests\Integration\DBTestInterface;
use Kunena\Tests\Integration\DBTestTrait;
use Kunena\Tests\Integration\IntegrationTestCase;
use Kunena\Tests\Unit\Support\Files;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;

/**
 * The subscribers are told about a message once ("TODO: this is just a workaround for #1862"): an edit of a published message in a
 * category with review sends it back to review, and its approval told the subscribers again. The workaround did not tell them when its
 * author had changed it, which also left out the first approval of a message which its author changed while it waited. A message
 * keeps whether its subscribers were told (the column notified).
 */
class KunenaMessageNotifiedTest extends IntegrationTestCase implements DBTestInterface
{
    use DBTestTrait;

    protected function setUp(): void
    {
        parent::setUp();

        if (!\defined('JVERSION')) {
            $this->markTestSkipped('The framework of Joomla is not loaded: set KUNENA_TEST_JOOMLA to a Joomla installation.');
        }
    }

    /**
     * A message of the table with the given state, and its object (no constructor: the database of the tests).
     */
    private function message(int $id, int $hold, int $notified): KunenaMessage
    {
        $this->insertRow('#__kunena_messages', ['id' => $id, 'parent' => 0, 'thread' => 1, 'catid' => 2, 'subject' => 'Message', 'hold' => $hold, 'notified' => $notified]);

        $message = (new ReflectionClass(KunenaMessage::class))->newInstanceWithoutConstructor();
        (new ReflectionProperty(KunenaMessage::class, '_db'))->setValue($message, $this->getDBDriver());
        $message->id       = $id;
        $message->hold     = $hold;
        $message->notified = $notified;

        return $message;
    }

    private function notified(int $id): int
    {
        return (int) $this->fetchAll('SELECT notified FROM #__kunena_messages WHERE id = ' . $id)[0]['notified'];
    }

    public function testAPublishedMessageIsMarkedOnce(): void
    {
        $message = $this->message(10, KunenaForum::PUBLISHED, 0);

        (new ReflectionMethod(KunenaMessage::class, 'markNotified'))->invoke($message);

        $this->assertSame(1, $this->notified(10));
        $this->assertSame(1, (int) $message->notified);
    }

    public function testAMessageWhichWaitsIsNotMarked(): void
    {
        $message = $this->message(11, KunenaForum::UNAPPROVED, 0);

        (new ReflectionMethod(KunenaMessage::class, 'markNotified'))->invoke($message);

        $this->assertSame(0, $this->notified(11));
    }

    public function testTheApprovalTellsTheSubscribersOfAMessageWhichWasNotToldAbout(): void
    {
        $source = Files::read('src/site/src/Controllers/TopicController.php');

        $this->assertSame(1, preg_match('/public function approve\(\)\s*\{(.*?)\n    \}/s', $source, $m));
        $this->assertStringContainsString('if (!$message->notified) {', $m[1]);
        $this->assertStringNotContainsString('modifiedByAuthor', $m[1]);

        $this->assertStringContainsString('$this->markNotified();', Files::read('src/libraries/kunena/src/Forum/Message/KunenaMessage.php'));
    }
}
