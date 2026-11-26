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
use Joomla\CMS\Document\HtmlDocument;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Language;
use Joomla\Input\Input;
use Joomla\Registry\Registry;
use Kunena\Forum\Libraries\Cache\KunenaLayoutCache;
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\Template\KunenaTemplate;
use Kunena\Forum\Libraries\User\KunenaUser;
use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;
use ReflectionProperty;

/**
 * The cache of the parts of the pages (the widgets of the sub-requests): the option "Enable Caching" of Kunena had no effect, nothing
 * was kept. A part which shows the same to many users is kept for the cache time, with a key of what it depends on.
 */
class KunenaLayoutCacheTest extends UnitTestCase
{
    private array $saved = [];

    protected function setUp(): void
    {
        $this->requireJoomla();

        $this->saved = [
            'application' => Factory::$application,
            'config'      => (new ReflectionProperty(KunenaConfig::class, 'instance'))->getValue(),
            'templates'   => (new ReflectionProperty(KunenaTemplate::class, '_instances'))->getValue(),
        ];

        $template = self::bare(KunenaTemplate::class, ['name' => 'agora', 'params' => new Registry(['layoutSet' => 'default'])]);
        (new ReflectionProperty(KunenaTemplate::class, '_instances'))->setValue(null, ['agora' => $template, '' => $template]);
    }

    protected function tearDown(): void
    {
        if ($this->saved) {
            Factory::$application = $this->saved['application'];
            (new ReflectionProperty(KunenaConfig::class, 'instance'))->setValue(null, $this->saved['config']);
            (new ReflectionProperty(KunenaTemplate::class, '_instances'))->setValue(null, $this->saved['templates']);
        }
    }

    /**
     * A site page of the given method, with the given option "Enable Caching" of Kunena.
     */
    private function page(string $method = 'GET', int $cache = 1, string $client = 'site'): void
    {
        $language = $this->createStub(Language::class);
        $language->method('getTag')->willReturn('nl-NL');

        $input = new class ([]) extends Input {
            public string $requestMethod = 'GET';

            public function getMethod(): string
            {
                return $this->requestMethod;
            }
        };
        $input->requestMethod = $method;

        $application = $this->createStub(CMSApplication::class);
        $application->method('isClient')->willReturnCallback(static fn ($name) => $name === $client);
        $application->method('getDocument')->willReturn($this->createStub(HtmlDocument::class));
        $application->method('getInput')->willReturn($input);
        $application->method('getLanguage')->willReturn($language);
        $application->method('getTemplate')->willReturn('cassiopeia');

        Factory::$application = $application;
        (new ReflectionProperty(KunenaConfig::class, 'instance'))->setValue(null, self::bare(KunenaConfig::class, ['config' => ['cache' => $cache, 'cacheTime' => 60, 'template' => 'agora']]));
    }

    public function testTheCacheIsForTheGetRequestsOfTheSiteWhenKunenaSaysSo(): void
    {
        $this->page();
        $this->assertTrue(KunenaLayoutCache::isEnabled());

        $this->page('POST');
        $this->assertFalse(KunenaLayoutCache::isEnabled(), 'a form which is sent');

        $this->page('GET', 0);
        $this->assertFalse(KunenaLayoutCache::isEnabled(), 'the option of Kunena is off');

        $this->page('GET', 1, 'administrator');
        $this->assertFalse(KunenaLayoutCache::isEnabled(), 'the backend');
    }

    public function testWithoutTheCacheThePartIsMade(): void
    {
        $this->page('POST');
        $made = 0;

        $html = KunenaLayoutCache::render('Widget/Statistics', [], static function () use (&$made): string {
            $made++;

            return '<div>stats</div>';
        });

        $this->assertSame('<div>stats</div>', $html);
        $this->assertSame(1, $made);
    }

    public function testTheKeyHasThePartWhatItDependsOnTheTemplatesAndTheLanguage(): void
    {
        $this->page();

        $key = KunenaLayoutCache::key('Widget/Statistics', ['user' => 'guest']);

        $this->assertSame($key, KunenaLayoutCache::key('Widget/Statistics', ['user' => 'guest']));
        $this->assertNotSame($key, KunenaLayoutCache::key('Widget/Statistics', ['user' => 'member']), 'another kind of user');
        $this->assertNotSame($key, KunenaLayoutCache::key('Widget/Whoisonline', ['user' => 'guest']), 'another part');

        (new ReflectionProperty(KunenaTemplate::class, '_instances'))->getValue()['agora']->params->set('layoutSet', 'dense');
        $this->assertNotSame($key, KunenaLayoutCache::key('Widget/Statistics', ['user' => 'guest']), 'another setting of the template');
    }

    public function testTheKindsOfUsers(): void
    {
        $user = static function (bool $exists, bool $admin, bool $moderator): KunenaUser {
            $user = new class () extends KunenaUser {
                public bool $isAdm = false;

                public bool $isMod = false;

                public function __construct()
                {
                }

                public function isAdmin($category = null): bool
                {
                    return $this->isAdm;
                }

                public function isModerator($category = null): bool
                {
                    return $this->isMod;
                }
            };

            $user->isAdm = $admin;
            $user->isMod = $moderator;
            (new ReflectionProperty(KunenaUser::class, '_exists'))->setValue($user, $exists);

            return $user;
        };

        $this->assertSame('guest', KunenaLayoutCache::userType($user(false, false, false)));
        $this->assertSame('member', KunenaLayoutCache::userType($user(true, false, false)));
        $this->assertSame('moderator', KunenaLayoutCache::userType($user(true, false, true)));
        $this->assertSame('admin', KunenaLayoutCache::userType($user(true, true, true)));
    }

    public function testThePartsAndTheChangesOfTheForum(): void
    {
        $display = Files::read('src/libraries/kunena/src/Controller/KunenaControllerDisplay.php');
        $this->assertStringContainsString('KunenaLayoutCache::render((string) $this->name, $context', $display);

        foreach (['Statistics/WidgetStatisticsDisplay', 'Whoisonline/WidgetWhoisonlineDisplay', 'Announcement/WidgetAnnouncementDisplay'] as $widget) {
            $this->assertStringContainsString('protected function getCacheContext(): ?array', Files::read("src/site/src/Controller/Widget/{$widget}.php"), $widget);
        }

        // A save or a delete of a Kunena object empties the copies, and so does the clearing of the cache of Kunena.
        $this->assertSame(2, substr_count(Files::read('src/libraries/kunena/src/Database/KunenaDatabaseObject.php'), 'KunenaLayoutCache::clear();'));
        $this->assertStringContainsString('KunenaLayoutCache::clear(true);', Files::read('src/libraries/kunena/src/Cache/KunenaCacheHelper.php'));

        // And a save of the configuration (the backend saves it through the component model, not KunenaConfig::save()).
        $this->assertStringContainsString('KunenaLayoutCache::clear(true);', Files::read('src/admin/src/Controller/ConfigController.php'));
        $this->assertStringContainsString('KunenaLayoutCache::clear(true);', Files::read('src/libraries/kunena/src/Config/KunenaConfig.php'));
    }
}
