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

namespace Kunena\Tests\Unit\Component\Site\Controllers;

use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Language;
use Joomla\Input\Input;
use Kunena\Forum\Site\Controllers\TopicController;
use Kunena\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use ReflectionProperty;

/**
 * In read only mode ("Allow users only to read messages") a thank you, a reaction, a vote of a poll, a bookmark and the answer of a
 * topic are refused. Their buttons are hidden in that mode, but the tasks themselves did not look at it: a request with a token
 * still wrote them (posting, editing and moderating are refused by the rights of the message and the topic).
 */
class TopicControllerReadOnlyTest extends UnitTestCase
{
    private mixed $savedLanguage = null;

    protected function setUp(): void
    {
        $this->requireJoomla();

        $language = $this->createStub(Language::class);
        $language->method('_')->willReturnArgument(0);

        $property            = new ReflectionProperty(Factory::class, 'language');
        $this->savedLanguage = $property->getValue();
        $property->setValue(null, $language);
    }

    protected function tearDown(): void
    {
        (new ReflectionProperty(Factory::class, 'language'))->setValue(null, $this->savedLanguage);
    }

    public static function tasks(): array
    {
        return ['thankyou' => ['thankyou'], 'unthankyou' => ['unthankyou'], 'react' => ['react'], 'answer' => ['answer'], 'bookmark' => ['bookmark'], 'vote' => ['vote']];
    }

    #[DataProvider('tasks')]
    public function testTheTaskIsRefusedInReadOnlyMode(string $task): void
    {
        $messages = [];
        $app      = $this->createStub(CMSApplication::class);
        $app->method('getInput')->willReturn(new Input(['task' => $task]));
        $app->method('enqueueMessage')->willReturnCallback(static function ($message, $type = 'message') use (&$messages): void {
            $messages[] = [$message, $type];
        });

        // The controller without its constructor (it needs the session and the user): only what the refusal reads.
        $controller = $this->getMockBuilder(TopicController::class)->disableOriginalConstructor()->onlyMethods(['setRedirectBack'])->getMock();
        $controller->expects($this->once())->method('setRedirectBack');

        (new ReflectionProperty(TopicController::class, 'app'))->setValue($controller, $app);
        (new ReflectionProperty(TopicController::class, 'config'))->setValue($controller, (object) ['readOnly' => 1]);

        $controller->$task();

        $this->assertSame([['COM_KUNENA_WARNING_READONLY', 'error']], $messages);
    }
}
