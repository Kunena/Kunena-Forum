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
use Joomla\CMS\Uri\Uri;
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategory;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopic;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Forum\Libraries\User\KunenaUser;
use Kunena\Forum\Site\Controllers\TopicController;
use Kunena\Tests\Unit\UnitTestCase;
use ReflectionProperty;

/**
 * The limit of the links of a post (maxLinks) counts all its links. A post with an eBay or X widget skipped the count, and in the
 * HTML of a post with an image one "(domain.tld)" in the text made only those be counted: either way any number of links passed.
 */
class TopicMaxLinksTest extends UnitTestCase
{
    private array $uri = [];

    private mixed $host;

    protected function setUp(): void
    {
        $this->requireJoomla();
        $this->loadRoute();

        // Uri::isInternal() compares with the base of the site, which needs a host; the base and the instances are taken back.
        foreach (['base', 'root', 'instances', 'current'] as $name) {
            $this->uri[$name] = (new ReflectionProperty(Uri::class, $name))->getValue();
        }

        $this->host              = $_SERVER['HTTP_HOST'] ?? null;
        $_SERVER['HTTP_HOST']    = 'forum.test';
    }

    protected function tearDown(): void
    {
        foreach ($this->uri as $name => $value) {
            (new ReflectionProperty(Uri::class, $name))->setValue(null, $value);
        }

        if ($this->host === null) {
            unset($_SERVER['HTTP_HOST']);
        } else {
            $_SERVER['HTTP_HOST'] = $this->host;
        }
    }

    /**
     * Loading KunenaRoute runs KunenaRoute::initialize(), which needs the configuration and an application: give it an empty
     * configuration and an application of the command line, for which it stops at once, and take both back afterwards.
     */
    private function loadRoute(): void
    {
        if (class_exists(KunenaRoute::class, false)) {
            return;
        }

        $config      = new ReflectionProperty(KunenaConfig::class, 'instance');
        $application = Factory::$application;
        $cli         = $this->createStub(CMSApplicationInterface::class);
        $cli->method('isClient')->willReturn(true);

        $config->setValue(null, $this->bare(KunenaConfig::class));
        Factory::$application = $cli;

        try {
            class_exists(KunenaRoute::class);
        } finally {
            $config->setValue(null, null);
            Factory::$application = $application;
        }
    }

    private function check(string $text): bool
    {
        $controller = (new \ReflectionClass(TopicController::class))->newInstanceWithoutConstructor();

        $me = $this->createStub(KunenaUser::class);
        $me->method('isAdmin')->willReturn(false);
        $me->method('isModerator')->willReturn(false);

        $topic = $this->createStub(KunenaTopic::class);
        $topic->method('getCategory')->willReturn($this->createStub(KunenaCategory::class));
        $topic->method('isAuthorised')->willReturn(false);

        $config = new \stdClass();
        $config->maxLinks = 1;

        foreach (['me' => $me, 'config' => $config] as $name => $value) {
            $property = new \ReflectionProperty(TopicController::class, $name);
            $property->setValue($controller, $value);
        }

        $method = new \ReflectionMethod(TopicController::class, 'checkMaxLinks');

        return $method->invoke($controller, $text, $topic);
    }

    public function testAWidgetDoesNotSkipTheCount(): void
    {
        $this->assertFalse($this->check('<div class="kunena_ebay_widget" data-id="1">item</div>'
            . '<a href="https://spam.example/1">a</a> <a href="https://spam.example/2">b</a>'));
        $this->assertTrue($this->check('<div class="kunena_ebay_widget" data-id="1">item</div><a href="https://spam.example/1">a</a>'));
    }

    public function testADomainInBracketsDoesNotHideTheLinksOfTheHtml(): void
    {
        $this->assertFalse($this->check('<img src="x.png"> see (example.com) '
            . '<a href="https://spam.example/1">a</a> <a href="https://spam.example/2">b</a>'));

        // The plain text of a post without images is still counted by its "(url)".
        $this->assertFalse($this->check('a (https://spam.example/1) b (https://spam.example/2)'));
        $this->assertTrue($this->check('a (https://spam.example/1)'));
    }
}
