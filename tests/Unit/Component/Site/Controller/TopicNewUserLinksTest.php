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

use Joomla\CMS\Application\CMSApplicationInterface;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Language;
use Kunena\Forum\Site\Controllers\TopicController;
use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * A new user who may not post links and images (newUsersPreventPostUrlImages) posts none: every [url], [img] and [email] in any case
 * and without a value, and every address with a scheme, are taken out of the post (and of the signature). Only [url=...] and
 * [img=...] in lower case went, and the pattern of the other addresses finds no IP address and no domain in other letters: these were
 * posted and shown as an image or a link.
 */
class TopicNewUserLinksTest extends UnitTestCase
{
    private mixed $language;

    protected function setUp(): void
    {
        parent::setUp();

        $this->requireJoomla();
        $this->language    = Factory::$language;
        Factory::$language = $this->createStub(Language::class);
        Factory::$language->method('_')->willReturnArgument(0);
    }

    protected function tearDown(): void
    {
        if (\defined('JVERSION')) {
            Factory::$language = $this->language;
        }

        parent::tearDown();
    }

    public static function links(): array
    {
        return [
            'an image of an IP address'     => ['[img]http://93.184.216.34/a[/img]'],
            'a link of an IP address'       => ['[url]http://93.184.216.34/x[/url]'],
            'an image of a Unicode domain'  => ['[img]https://пример.рф/pic[/img]'],
            'a link in upper case'          => ['[URL=http://8.8.8.8]x[/URL]'],
            'an address of a Unicode domain' => ['https://пример.рф/pic'],
        ];
    }

    #[DataProvider('links')]
    public function testTheLinkIsTakenOut(string $link): void
    {
        $controller = (new \ReflectionClass(TopicController::class))->newInstanceWithoutConstructor();
        (new \ReflectionProperty(TopicController::class, 'app'))->setValue($controller, $this->createStub(CMSApplicationInterface::class));

        $text = (new \ReflectionMethod(TopicController::class, 'removeLinksInMessage'))->invoke($controller, "Hello $link world");

        $this->assertStringNotContainsString('93.184', $text);
        $this->assertStringNotContainsString('8.8.8.8', $text);
        $this->assertStringNotContainsString('пример', $text);
        $this->assertStringContainsString('Hello', $text);
    }

    /**
     * The signature has the same patterns (its save needs a whole site: its code is read).
     */
    public function testTheSignatureTakesTheSameLinksOut(): void
    {
        $source = Files::read('src/site/src/Controllers/UserController.php');

        $this->assertStringContainsString("\$signature = preg_replace('/\\[(url|img|email)\\b[^\\]]*\\].*?\\[\\/\\1\\s*\\]/isu', '', \$signature);", $source);
        $this->assertStringContainsString("\$signature = preg_replace('#(?:https?|ftp)://\\S+#iu', '', \$signature);", $source);
    }
}
