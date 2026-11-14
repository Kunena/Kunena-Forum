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

namespace Kunena\Tests\Unit\Libraries\Kunena\Controller;

use Kunena\Forum\Libraries\Controller\KunenaController;
use Kunena\Forum\Site\Controllers\CategoryController;
use Kunena\Forum\Site\Controllers\UserController;
use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * When the forum is for registered users only, a visitor may run the login task of the user controller (the login form of
 * the 403 page posts it, issue #9675) and no other task.
 */
class KunenaControllerTest extends UnitTestCase
{
    protected function setUp(): void
    {
        $this->requireJoomla();
    }

    private static function guestMayRun(string $class, string $task): bool
    {
        return self::invoke(self::bare($class), 'isRegOnlyGuestTask', $task);
    }

    public function testAVisitorMayLogIn(): void
    {
        $this->assertTrue(self::guestMayRun(UserController::class, 'login'));
    }

    public static function refusedTasks(): array
    {
        return [
            'logout of the user controller'   => [UserController::class, 'logout'],
            'save of the user controller'     => [UserController::class, 'save'],
            'report of the user controller'   => [UserController::class, 'report'],
            'login in capitals'               => [UserController::class, 'LOGIN'],
            'login of the base controller'    => [KunenaController::class, 'login'],
            'login of another controller'     => [CategoryController::class, 'login'],
            'subscribe of another controller' => [CategoryController::class, 'subscribe'],
        ];
    }

    #[DataProvider('refusedTasks')]
    public function testAVisitorMayRunNoOtherTask(string $class, string $task): void
    {
        $this->assertFalse(self::guestMayRun($class, $task));
    }

    public function testTheGuardOfRegisteredUsersOnlyAsksForTheTask(): void
    {
        $source = Files::read('src/libraries/kunena/src/Controller/KunenaController.php');

        $this->assertStringContainsString(
            "if (\$this->config->regOnly && !\$this->me->exists() && !\$this->isRegOnlyGuestTask(\$task)) {\n"
            . "                    throw new KunenaExceptionAuthorise(Text::_('COM_KUNENA_LOGIN_NOTIFICATION'), 403);",
            $source
        );
    }
}
