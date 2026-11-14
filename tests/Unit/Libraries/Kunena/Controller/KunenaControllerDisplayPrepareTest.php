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

use Kunena\Forum\Libraries\Controller\KunenaControllerDisplay;
use Kunena\Tests\Unit\UnitTestCase;
use RuntimeException;
use Throwable;

/**
 * A page with several lists (the tabs of a profile) prepares all of them before it makes them: before() runs once, when the
 * page prepares the list, and its answer (or its error) is used when the view is made.
 */
class KunenaControllerDisplayPrepareTest extends UnitTestCase
{
    /**
     * A controller which counts its before() and makes the view "view".
     */
    private function controller(?Throwable $error = null): KunenaControllerDisplay
    {
        return new class ($error) extends KunenaControllerDisplay {
            public int $befores = 0;

            public function __construct(private ?Throwable $error)
            {
            }

            protected function before()
            {
                $this->befores++;

                if ($this->error) {
                    throw $this->error;
                }

                return true;
            }

            protected function display()
            {
                return 'view';
            }

            protected function after()
            {
            }
        };
    }

    public function testThePreparedControllerRunsBeforeOnlyOnce(): void
    {
        $controller = $this->controller();

        $controller->prepare();
        $controller->prepare();
        $this->assertSame(1, $controller->befores);

        $this->assertSame('view', $controller->execute());
        $this->assertSame(1, $controller->befores);
    }

    public function testAControllerWhichWasNotPreparedRunsBeforeWhenItIsMade(): void
    {
        $controller = $this->controller();

        $this->assertSame('view', $controller->execute());
        $this->assertSame(1, $controller->befores);
    }

    public function testAResetPreparationRunsBeforeAgain(): void
    {
        $controller = $this->controller();

        // A page which surveyed its lists prepares them again, for real.
        $controller->prepare();
        $controller->resetPreparation();
        $controller->prepare();
        $this->assertSame(2, $controller->befores);

        $this->assertSame('view', $controller->execute());
        $this->assertSame(2, $controller->befores);
    }

    public function testTheErrorOfThePreparationComesWhenTheViewIsMade(): void
    {
        $controller = $this->controller(new RuntimeException('No list'));

        // The preparation of the page goes on: the error is the one of this list only.
        $controller->prepare();
        $this->assertSame(1, $controller->befores);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('No list');
        $controller->execute();
    }
}
