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
use Kunena\Forum\Site\Controllers\UserController;
use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use ReflectionProperty;

/**
 * In read only mode karma is not given: the buttons are hidden, but karmaup and karmadown still changed the karma with a token
 * (like a thank you in the topic controller).
 */
class UserControllerKarmaReadOnlyTest extends UnitTestCase
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
        return ['karmaup' => ['karmaup'], 'karmadown' => ['karmadown']];
    }

    #[DataProvider('tasks')]
    public function testKarmaIsRefusedInReadOnlyMode(string $task): void
    {
        $messages = [];
        $app      = $this->createStub(CMSApplication::class);
        $app->method('getInput')->willReturn(new Input());
        $app->method('enqueueMessage')->willReturnCallback(static function ($message, $type = 'message') use (&$messages): void {
            $messages[] = [$message, $type];
        });

        $controller = $this->getMockBuilder(UserController::class)->disableOriginalConstructor()->onlyMethods(['setRedirectBack'])->getMock();
        $controller->expects($this->once())->method('setRedirectBack');

        (new ReflectionProperty(UserController::class, 'app'))->setValue($controller, $app);
        (new ReflectionProperty(UserController::class, 'config'))->setValue($controller, (object) ['readOnly' => 1]);

        $controller->$task();

        $this->assertSame([['COM_KUNENA_WARNING_READONLY', 'error']], $messages);
    }

    public function testTheKarmaButtonsUseCanSeeKarma(): void
    {
        $source = Files::read('src/libraries/kunena/src/User/KunenaUser.php');

        $this->assertStringContainsString('!$this->_config->readOnly', $source);
        $this->assertStringContainsString('if ($this->canSeeKarma()) {', $source);
    }
}