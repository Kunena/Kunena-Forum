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

namespace Kunena\Tests\Integration\Component\Administrator\Install;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Language;
use Joomla\Database\DatabaseInterface;
use Joomla\DI\Container;
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Tests\Integration\DBTestInterface;
use Kunena\Tests\Integration\DBTestTrait;
use Kunena\Tests\Integration\IntegrationTestCase;
use Kunena\Tests\Unit\Support\Files;
use ReflectionProperty;

/**
 * A forum which is upgraded from Kunena 7 keeps its template: Kunena 8.0 has Agora as the default, a forum whose settings never
 * stored a template got it after the upgrade. The guard was in migrateConfig() of the old web installer, which nothing ran; it is a
 * step of the upgrade of 8.0 now. A forum of a pre-release of 8.0 (the block of 8.0 runs again on its updates) and a forum with a
 * template in its settings are left as they are.
 */
class KeepTemplateOfUpgradedForumTest extends IntegrationTestCase implements DBTestInterface
{
    use DBTestTrait;

    private array $saved = [];

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

        $language = $this->createStub(Language::class);
        $language->method('_')->willReturnArgument(0);

        // The configuration which the step reads again: a stand-in, there is no cache here.
        $config = new ReflectionProperty(KunenaConfig::class, 'instance');

        $this->saved = ['container' => Factory::$container, 'language' => Factory::$language, 'config' => $config->getValue()];

        Factory::$container = $container;
        Factory::$language  = $language;
        $config->setValue(null, $this->createStub(KunenaConfig::class));

        // The table of the extensions of Joomla is not in the schema of the tests: it is there for this test only.
        $driver->setQuery('CREATE TABLE IF NOT EXISTS #__extensions (extension_id int NOT NULL AUTO_INCREMENT PRIMARY KEY, type varchar(20) NOT NULL DEFAULT \'\', element varchar(100) NOT NULL DEFAULT \'\', params text NOT NULL)')->execute();

        require_once Files::path('src/admin/install/sql/updates/php/8.0.0-2026-11-26_keep_template_of_upgraded_forum.php');
    }

    protected function tearDown(): void
    {
        if ($this->saved) {
            $this->getDBDriver()->setQuery('DROP TABLE IF EXISTS #__extensions')->execute();

            Factory::$container = $this->saved['container'];
            Factory::$language  = $this->saved['language'];
            (new ReflectionProperty(KunenaConfig::class, 'instance'))->setValue(null, $this->saved['config']);
        }

        parent::tearDown();
    }

    /**
     * The settings of Kunena after the step, for a forum with these settings and these versions in its history.
     */
    private function afterStep(string $params, array $versions): array
    {
        // Not with insertRows(): the tests would empty the table before the next test, after tearDown() dropped it.
        $db = $this->getDBDriver();
        $db->setQuery(
            'INSERT INTO #__extensions (extension_id, type, element, params) VALUES (10, ' . $db->quote('component') . ', '
            . $db->quote('com_kunena') . ', ' . $db->quote($params) . '), (11, ' . $db->quote('module') . ', ' . $db->quote('com_kunena') . ", '{}')"
        )->execute();

        foreach ($versions as $version) {
            $this->insertRow('#__kunena_version', ['version' => $version, 'versiondate' => '2026-01-01', 'installdate' => '2026-01-01', 'versionname' => 'Test', 'state' => '']);
        }

        $result = kunena_800_2026_11_26_keep_template_of_upgraded_forum(null);
        $this->assertTrue($result['success']);

        $rows = $this->fetchAll('SELECT extension_id, params FROM #__extensions ORDER BY extension_id');
        $this->assertSame('{}', $rows[1]['params'], 'Another extension is not changed.');

        return json_decode($rows[0]['params'], true);
    }

    public function testAForumOfKunena7WithoutATemplateKeepsAurelia(): void
    {
        $this->assertSame(['showStats' => '1', 'template' => 'aurelia'], $this->afterStep('{"showStats":"1"}', ['6.4.5', '7.0.6']));
    }

    public function testAForumOfKunena7WhichNeverSavedItsSettingsKeepsAurelia(): void
    {
        $this->assertSame(['template' => 'aurelia'], $this->afterStep('', ['7.0.6']));
    }

    public function testAForumWithATemplateKeepsIt(): void
    {
        $this->assertSame(['template' => 'agora'], $this->afterStep('{"template":"agora"}', ['7.0.6']));
    }

    public function testAForumOfAPreReleaseOfKunena8KeepsAgora(): void
    {
        $this->assertSame([], $this->afterStep('{}', ['7.0.6', '8.0.0-ALPHA1-DEV']));
    }

    public function testAForumOfAGitCheckoutIsLeftAlone(): void
    {
        $this->assertSame([], $this->afterStep('{}', ['@kunenaversion@']));
    }
}
