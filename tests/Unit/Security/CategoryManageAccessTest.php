<?php

/**
 * Kunena Component
 *
 * The form of a category returned the exception of the missing access from before(). Only false or a thrown exception stops a
 * page, so the page went on with nothing loaded and crashed (error 500) for everybody who is no administrator.
 *
 * @package         Kunena.Tests
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 **/

namespace Kunena\Tests\Unit\Security;

use PHPUnit\Framework\TestCase;

class CategoryManageAccessTest extends TestCase
{
    private const SRC = __DIR__ . '/../../../src/site/src';

    /**
     * The body of before() of a display controller.
     */
    private function before(string $file): string
    {
        $source = file_get_contents(self::SRC . '/' . $file);

        $this->assertSame(1, preg_match('/protected function before\(\)[^{]*\{(.*?)\r?\n    \}\r?\n/s', $source, $m), $file);

        return $m[1];
    }

    public function testFormOfACategoryThrowsForWhoIsNoAdministrator(): void
    {
        $body = $this->before('Controller/Category/Manage/CategoryManageDisplay.php');

        $this->assertMatchesRegularExpression('/if \(!\$this->me->isAdmin\(\)\) \{\s*throw new KunenaExceptionAuthorise\(/', $body);
        $this->assertLessThan(strpos($body, 'KunenaCategoryHelper::get('), strpos($body, 'throw new KunenaExceptionAuthorise('));
    }
}
