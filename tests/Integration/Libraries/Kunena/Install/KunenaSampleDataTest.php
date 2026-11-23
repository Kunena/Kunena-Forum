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

namespace Kunena\Tests\Integration\Libraries\Kunena\Install;

use Joomla\CMS\Application\CMSApplicationInterface;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Language;
use Joomla\CMS\User\User;
use Joomla\Database\DatabaseInterface;
use Joomla\DI\Container;
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\Install\KunenaSampleData;
use Kunena\Tests\Integration\DBTestHelper;
use Kunena\Tests\Integration\DBTestInterface;
use Kunena\Tests\Integration\DBTestTrait;
use Kunena\Tests\Integration\IntegrationTestCase;
use ReflectionClass;
use ReflectionProperty;

/**
 * The sample data on an empty forum: the section and its two categories, with the welcome topic in the first one. Only the section
 * went in (the check "the table is empty" ran for each insert, and after the section the table of the categories was not empty), so
 * the welcome topic had no category.
 */
class KunenaSampleDataTest extends IntegrationTestCase implements DBTestInterface
{
    use DBTestTrait;

    private array $saved = [];

    /**
     * @return  string[]
     */
    public function getSchemasToLoad(): array
    {
        return ['kunena', 'joomla'];
    }

    protected function setUp(): void
    {
        parent::setUp();

        if (!\defined('JVERSION')) {
            $this->markTestSkipped('The framework of Joomla is not loaded: set KUNENA_TEST_JOOMLA to a Joomla installation.');
        }

        $driver    = $this->getDBDriver();
        $container = new Container();
        $container->share(DatabaseInterface::class, static fn () => $driver);
        $container->share('DatabaseDriver', static fn () => $driver);

        // The texts are their keys; the user who installs is the user 42.
        $language = $this->createStub(Language::class);
        $language->method('_')->willReturnArgument(0);
        $language->method('getTag')->willReturn('en-GB');

        $user     = new User();
        $user->id = 42;

        $application = $this->createStub(CMSApplicationInterface::class);
        $application->method('getLanguage')->willReturn($language);
        $application->method('getIdentity')->willReturn($user);

        // The aliases of the categories are made by the router, which needs a configuration and stops at once in an application
        // of the command line.
        $application->method('isClient')->willReturn(true);
        $application->method('get')->willReturnCallback(static fn ($name, $default = null) => $name === 'unicodeslugs' ? 1 : $default);
        $config = (new ReflectionClass(KunenaConfig::class))->newInstanceWithoutConstructor();
        (new ReflectionProperty(KunenaConfig::class, 'config'))->setValue($config, []);
        $instance = new ReflectionProperty(KunenaConfig::class, 'instance');

        $this->saved = ['container' => Factory::$container, 'application' => Factory::$application, 'language' => Factory::$language,
            'config' => $instance->getValue()];
        $instance->setValue(null, $config);

        Factory::$container   = $container;
        Factory::$application = $application;
        Factory::$language    = $language;
    }

    protected function tearDown(): void
    {
        // The sample data wrote these tables itself (not with insertRow()): they are emptied before the next test.
        foreach (['ranks', 'smileys', 'categories', 'aliases', 'messages', 'messages_text', 'topics', 'users', 'user_topics'] as $table) {
            DBTestHelper::markDirty('#__kunena_' . $table);
        }

        if ($this->saved) {
            Factory::$container   = $this->saved['container'];
            Factory::$application = $this->saved['application'];
            Factory::$language    = $this->saved['language'];
            (new ReflectionProperty(KunenaConfig::class, 'instance'))->setValue(null, $this->saved['config']);
        }

        parent::tearDown();
    }

    public function testTheSectionAndBothCategoriesAreInstalled(): void
    {
        KunenaSampleData::installSampleData();

        $this->assertSame(
            [1, 2, 3],
            array_map('intval', array_column($this->fetchAll('SELECT id FROM #__kunena_categories ORDER BY id'), 'id'))
        );
    }
}
