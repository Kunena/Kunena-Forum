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

namespace Kunena\Tests\Unit\Libraries\Kunena\Cache;

use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Cache\CacheControllerFactoryInterface;
use Joomla\CMS\Cache\Controller\OutputController;
use Joomla\CMS\Document\HtmlDocument;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Language;
use Joomla\CMS\Session\Session;
use Joomla\CMS\User\User;
use Joomla\DI\Container;
use Joomla\Input\Input;
use Joomla\Registry\Registry;
use Kunena\Forum\Libraries\Cache\KunenaLayoutCache;
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\Template\KunenaTemplate;
use Kunena\Tests\Unit\UnitTestCase;
use ReflectionProperty;

/**
 * A part of a page made for the cache of Kunena (a widget) keeps the meta tags of the page as they are: the head was put back with
 * mergeHeadData() of Joomla, which makes every tag but http-equiv a "name" one, so the og: tags of the page (property="og:title")
 * came out as name="og:title" whenever such a part was not in the cache yet (after a change of the forum, every minute): the sites
 * which read them found none (the spec of the profile page failed now and then).
 */
class KunenaLayoutCacheHeadTest extends UnitTestCase
{
    private array $saved = [];

    protected function setUp(): void
    {
        $this->requireJoomla();

        $this->saved = [
            'application' => Factory::$application,
            'container'   => Factory::$container,
            'config'      => (new ReflectionProperty(KunenaConfig::class, 'instance'))->getValue(),
            'templates'   => (new ReflectionProperty(KunenaTemplate::class, '_instances'))->getValue(),
        ];

        $template = self::bare(KunenaTemplate::class, ['name' => 'agora', 'params' => new Registry(['layoutSet' => 'default'])]);
        (new ReflectionProperty(KunenaTemplate::class, '_instances'))->setValue(null, ['agora' => $template, '' => $template]);
        (new ReflectionProperty(KunenaConfig::class, 'instance'))->setValue(null, self::bare(KunenaConfig::class, ['config' => ['cache' => 1, 'cacheTime' => 60, 'template' => 'agora']]));
    }

    protected function tearDown(): void
    {
        if ($this->saved) {
            Factory::$application = $this->saved['application'];
            Factory::$container   = $this->saved['container'];
            (new ReflectionProperty(KunenaConfig::class, 'instance'))->setValue(null, $this->saved['config']);
            (new ReflectionProperty(KunenaTemplate::class, '_instances'))->setValue(null, $this->saved['templates']);
        }
    }

    public function testTheMetaTagsOfThePageKeepTheirAttribute(): void
    {
        $document = new HtmlDocument();
        $document->setMetaData('og:title', 'Tom & Jerry', 'property');
        $document->setMetaData('robots', 'index, follow');

        $language = $this->createStub(Language::class);
        $language->method('getTag')->willReturn('en-GB');

        $application = $this->createStub(CMSApplication::class);
        $application->method('isClient')->willReturnCallback(static fn ($name) => $name === 'site');
        $application->method('getDocument')->willReturn($document);
        // A GET request of the site: the cache is used.
        $input = new class ([]) extends Input {
            public function getMethod(): string
            {
                return 'GET';
            }
        };
        $application->method('getInput')->willReturn($input);
        $application->method('getLanguage')->willReturn($language);
        $application->method('getTemplate')->willReturn('cassiopeia');
        // The token of the forms, which a copy must not have (Session::getFormToken()).
        $session = $this->createStub(Session::class);
        $session->method('getToken')->willReturn('token');
        $session->method('get')->willReturn(new User());
        $application->method('getSession')->willReturn($session);
        Factory::$application = $application;

        // A cache which has no copy of the part yet.
        $cache = $this->createStub(OutputController::class);
        $cache->method('get')->willReturn(false);
        $factory = $this->createStub(CacheControllerFactoryInterface::class);
        $factory->method('createCacheController')->willReturn($cache);
        $container = new Container(Factory::getContainer());
        $container->share(CacheControllerFactoryInterface::class, static fn () => $factory);
        Factory::$container = $container;

        $html = KunenaLayoutCache::render('Widget/Statistics', [], static function () use ($document): string {
            $document->setMetaData('og:site_name', 'Forum', 'property');

            return '<div>stats</div>';
        });

        $this->assertSame('<div>stats</div>', $html);
        $this->assertSame('Tom & Jerry', $document->getMetaData('og:title', 'property'), 'the tag of the page');
        $this->assertSame('', $document->getMetaData('og:title'), 'not a name tag');
        $this->assertSame('Forum', $document->getMetaData('og:site_name', 'property'), 'the tag of the part');
        $this->assertSame('index, follow', $document->getMetaData('robots'));
    }
}
