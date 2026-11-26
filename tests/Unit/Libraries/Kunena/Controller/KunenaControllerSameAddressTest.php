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
use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * The redirect of a page to the address of its menu item (a note: "check possible redirect loops!"): a redirect to the address of
 * the page itself came back to the same page, again and again until the browser stopped it. The page is shown then.
 */
class KunenaControllerSameAddressTest extends UnitTestCase
{
    protected function setUp(): void
    {
        $this->requireJoomla();
    }

    public static function sameAddresses(): array
    {
        return [
            'the same'                      => ['/forum/category', '/forum/category'],
            'a slash at the end'            => ['/forum/category/', '/forum/category'],
            'a full address'                => ['https://www.example.com/forum/category', '/forum/category'],
            'the query in another order'    => ['/index.php?option=com_kunena&view=topic&id=5', '/index.php?id=5&view=topic&option=com_kunena'],
            'an encoded &'                  => ['/index.php?option=com_kunena&amp;view=topic', '/index.php?option=com_kunena&view=topic'],
            'an encoded letter in the path' => ['/forum/caf%C3%A9', '/forum/café'],
        ];
    }

    #[DataProvider('sameAddresses')]
    public function testTheSamePage(string $a, string $b): void
    {
        $this->assertTrue(KunenaController::isSameAddress($a, $b));
    }

    public static function otherAddresses(): array
    {
        return [
            'another path'    => ['/forum/category', '/forum/topics'],
            'another Itemid'  => ['/index.php?option=com_kunena&Itemid=101', '/index.php?option=com_kunena&Itemid=102'],
            'a variable more' => ['/forum/category?start=20', '/forum/category'],
        ];
    }

    #[DataProvider('otherAddresses')]
    public function testAnotherPage(string $a, string $b): void
    {
        $this->assertFalse(KunenaController::isSameAddress($a, $b));
    }

    public function testTheDisplayDoesNotRedirectToItsOwnAddress(): void
    {
        $source = Files::read('src/libraries/kunena/src/Controller/KunenaController.php');

        $this->assertSame(1, preg_match('/if \(!self::isSameAddress\(\$route, \$current\)\) \{.*?\$this->app->redirect\(\$route, 301\);/s', $source));
    }
}
