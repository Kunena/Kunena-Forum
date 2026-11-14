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

namespace Kunena\Tests\Unit\Libraries\Kunena\Forum\Message;

use BadMethodCallException;
use Exception;
use Joomla\CMS\Application\CMSApplicationInterface;
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Event\Dispatcher;
use Kunena\Forum\Libraries\Event\KunenaCheckSpamEvent;
use Kunena\Forum\Libraries\Event\KunenaDisplayEvent;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategory;
use Kunena\Forum\Libraries\Forum\Message\KunenaMessage;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopic;
use Kunena\Forum\Libraries\User\KunenaUser;
use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use ReflectionProperty;
use stdClass;
use TypeError;

/**
 * No plugin could stop or hold a post before it is saved, so no anti-spam service other than StopForumSpam could be used
 * (issue #9676). Before a new topic or a reply is saved, the plugins of the kunena group get onKunenaCheckSpam and answer
 * with reject() or hold(); the strictest answer wins, and a listener that fails never stops the post.
 */
class KunenaMessageSpamCheckEventTest extends UnitTestCase
{
    private const string EVENT = 'onKunenaCheckSpam';

    /**
     * The values which the test replaces, put back after the test.
     */
    private array $saved = [];

    private Dispatcher $dispatcher;

    private KunenaMessage $message;

    private KunenaTopic $topic;

    private KunenaCategory $category;

    private KunenaUser $user;

    protected function setUp(): void
    {
        $this->requireJoomla();

        // A stub application with a real dispatcher, and no plugin to load from the database.
        $this->dispatcher = new Dispatcher();
        $application      = $this->createStub(CMSApplicationInterface::class);
        $application->method('getDispatcher')->willReturn($this->dispatcher);

        $plugins     = new ReflectionProperty(PluginHelper::class, 'plugins');
        $this->saved = [
            'application' => Factory::$application,
            'plugins'     => $plugins->getValue(),
        ];

        Factory::$application = $application;
        $plugins->setValue(null, []);

        $this->message  = self::bare(KunenaMessage::class, ['id' => 0, 'subject' => 'Cheap pills', 'message' => 'Buy now', 'userid' => 7]);
        $this->topic    = self::bare(KunenaTopic::class, ['id' => 0]);
        $this->category = self::bare(KunenaCategory::class, ['id' => 3]);
        $this->user     = self::bare(KunenaUser::class, ['userid' => 7]);
    }

    protected function tearDown(): void
    {
        if (!$this->saved) {
            return;
        }

        Factory::$application = $this->saved['application'];
        (new ReflectionProperty(PluginHelper::class, 'plugins'))->setValue(null, $this->saved['plugins']);
    }

    private function check(): KunenaCheckSpamEvent
    {
        return $this->message->dispatchSpamCheck($this->topic, $this->category, $this->user, true, '203.0.113.5');
    }

    private function arguments(): array
    {
        return [
            'message'    => $this->message,
            'topic'      => $this->topic,
            'category'   => $this->category,
            'user'       => $this->user,
            'isNewTopic' => false,
            'ip'         => '203.0.113.5',
        ];
    }

    public function testNoListenerGivesNoVerdict(): void
    {
        $event = $this->check();

        $this->assertSame(KunenaCheckSpamEvent::NONE, $event->getVerdict());
        $this->assertSame('', $event->getReason());
    }

    public function testListenerGetsTheArgumentsOnce(): void
    {
        $events = [];

        $this->dispatcher->addListener(self::EVENT, function ($event) use (&$events) {
            $events[] = $event;
        });

        $returned = $this->message->dispatchSpamCheck($this->topic, $this->category, $this->user, false, '198.51.100.9');

        $this->assertCount(1, $events);
        $this->assertInstanceOf(KunenaCheckSpamEvent::class, $events[0]);
        $this->assertSame($returned, $events[0]);
        $this->assertSame($this->message, $events[0]->getMessage());
        $this->assertSame($this->topic, $events[0]->getTopic());
        $this->assertSame($this->category, $events[0]->getCategory());
        $this->assertSame($this->user, $events[0]->getUser());
        $this->assertFalse($events[0]->isNewTopic());
        $this->assertSame('198.51.100.9', $events[0]->getIp());
    }

    public function testRejectWithAReason(): void
    {
        $this->dispatcher->addListener(self::EVENT, fn (KunenaCheckSpamEvent $event) => $event->reject('text'));

        $event = $this->check();

        $this->assertSame(KunenaCheckSpamEvent::REJECT, $event->getVerdict());
        $this->assertSame('text', $event->getReason());
    }

    public function testRejectWithoutAReason(): void
    {
        $this->dispatcher->addListener(self::EVENT, fn (KunenaCheckSpamEvent $event) => $event->reject());

        $event = $this->check();

        $this->assertSame(KunenaCheckSpamEvent::REJECT, $event->getVerdict());
        $this->assertSame('', $event->getReason());
    }

    public function testHold(): void
    {
        $this->dispatcher->addListener(self::EVENT, fn (KunenaCheckSpamEvent $event) => $event->hold());

        $this->assertSame(KunenaCheckSpamEvent::HOLD, $this->check()->getVerdict());
    }

    public function testHoldThenRejectRejects(): void
    {
        $this->dispatcher->addListener(self::EVENT, fn (KunenaCheckSpamEvent $event) => $event->hold());
        $this->dispatcher->addListener(self::EVENT, fn (KunenaCheckSpamEvent $event) => $event->reject('spam'), -1);

        $event = $this->check();

        $this->assertSame(KunenaCheckSpamEvent::REJECT, $event->getVerdict());
        $this->assertSame('spam', $event->getReason());
    }

    public function testRejectThenHoldStillRejects(): void
    {
        $this->dispatcher->addListener(self::EVENT, fn (KunenaCheckSpamEvent $event) => $event->reject('spam'));
        $this->dispatcher->addListener(self::EVENT, fn (KunenaCheckSpamEvent $event) => $event->hold(), -1);

        $event = $this->check();

        $this->assertSame(KunenaCheckSpamEvent::REJECT, $event->getVerdict());
        $this->assertSame('spam', $event->getReason());
    }

    public function testFirstRejectReasonIsKept(): void
    {
        $this->dispatcher->addListener(self::EVENT, fn (KunenaCheckSpamEvent $event) => $event->reject());
        $this->dispatcher->addListener(self::EVENT, fn (KunenaCheckSpamEvent $event) => $event->reject('first'), -1);
        $this->dispatcher->addListener(self::EVENT, fn (KunenaCheckSpamEvent $event) => $event->reject('second'), -2);
        $this->dispatcher->addListener(self::EVENT, fn (KunenaCheckSpamEvent $event) => $event->reject(), -3);

        $event = $this->check();

        $this->assertSame(KunenaCheckSpamEvent::REJECT, $event->getVerdict());
        $this->assertSame('first', $event->getReason());
    }

    public function testThrowingListenerDoesNotStopThePostAndKeepsTheVerdict(): void
    {
        $called = false;

        $this->dispatcher->addListener(self::EVENT, fn (KunenaCheckSpamEvent $event) => $event->hold());
        $this->dispatcher->addListener(self::EVENT, function () use (&$called) {
            $called = true;

            throw new Exception('The spam service is down.');
        }, -1);

        $event = $this->check();

        $this->assertTrue($called);
        $this->assertSame(KunenaCheckSpamEvent::HOLD, $event->getVerdict());
    }

    public function testWronglyTypedListenerDoesNotStopThePostAndKeepsTheVerdict(): void
    {
        // The dispatcher passes the spam event to a listener which expects another event: a TypeError.
        $this->dispatcher->addListener(self::EVENT, fn (KunenaCheckSpamEvent $event) => $event->reject('spam'));
        $this->dispatcher->addListener(self::EVENT, function (KunenaDisplayEvent $event) {
        }, -1);

        $event = $this->check();

        $this->assertSame(KunenaCheckSpamEvent::REJECT, $event->getVerdict());
        $this->assertSame('spam', $event->getReason());

        // The same dispatch outside the method does throw.
        $this->expectException(TypeError::class);
        $this->dispatcher->dispatch(self::EVENT, new KunenaCheckSpamEvent(self::EVENT, $this->arguments()));
    }

    public function testEventWithAllArguments(): void
    {
        $event = new KunenaCheckSpamEvent(self::EVENT, $this->arguments());

        $this->assertSame(KunenaCheckSpamEvent::NONE, $event->getVerdict());
        $this->assertFalse($event->isNewTopic());
    }

    public static function badArguments(): array
    {
        return [
            'message missing'       => ['message', null],
            'message wrong type'    => ['message', new stdClass()],
            'topic missing'         => ['topic', null],
            'topic wrong type'      => ['topic', new stdClass()],
            'category missing'      => ['category', null],
            'category wrong type'   => ['category', new stdClass()],
            'user missing'          => ['user', null],
            'user wrong type'       => ['user', new stdClass()],
            'isNewTopic missing'    => ['isNewTopic', null],
            'isNewTopic wrong type' => ['isNewTopic', 1],
            'ip missing'            => ['ip', null],
            'ip wrong type'         => ['ip', 2130706433],
        ];
    }

    #[DataProvider('badArguments')]
    public function testEventNeedsEveryArgumentWithItsType(string $key, mixed $value): void
    {
        $arguments = $this->arguments();

        if ($value === null) {
            unset($arguments[$key]);
        } else {
            $arguments[$key] = $value;
        }

        $this->expectException(BadMethodCallException::class);

        new KunenaCheckSpamEvent(self::EVENT, $arguments);
    }

    public function testPostChecksSpamAfterTheOwnChecksAndBeforeTheSave(): void
    {
        $source = Files::read('src/site/src/Controllers/TopicController.php');

        $this->assertSame(1, preg_match('/public function post\(\)[^{]*\{(.*?)\n    \}\n/s', $source, $m));

        $maxLinks  = strpos($m[1], '$this->checkMaxLinks(');
        $dispatch  = strpos($m[1], '$message->dispatchSpamCheck(');
        $rejected  = strpos($m[1], 'COM_KUNENA_POST_ERROR_SPAM_REJECTED');
        $hold      = strpos($m[1], '$activity->onBeforeHold(');
        $save      = strpos($m[1], '$message->save()');

        $this->assertIsInt($maxLinks);
        $this->assertIsInt($dispatch);
        $this->assertIsInt($rejected, 'The default text of a rejection.');
        $this->assertSame(1, substr_count($m[1], 'dispatchSpamCheck('), 'Once per post.');
        $this->assertLessThan($dispatch, $maxLinks, 'After the own checks of Kunena.');
        $this->assertLessThan($hold, $dispatch, 'Before the activity integration reads the hold.');
        $this->assertLessThan($save, $dispatch, 'Before the message is saved.');
    }
}
