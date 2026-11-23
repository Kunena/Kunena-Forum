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

namespace Kunena\Tests\Integration\Libraries\Kunena\Forum\Category;

use Joomla\CMS\Application\CMSApplicationInterface;
use Joomla\CMS\Factory;
use Joomla\Database\DatabaseInterface;
use Joomla\DI\Container;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategoryHelper;
use Kunena\Tests\Integration\DBTestInterface;
use Kunena\Tests\Integration\DBTestTrait;
use Kunena\Tests\Integration\IntegrationTestCase;
use ReflectionProperty;

/**
 * The level of a category in the tree (0 for a section, 1 for its categories, ...): the lists of the categories indent by it (the
 * search, the new topic, the move of a topic, the jump menu, the categories of the backend). The loop which computes it started
 * with null, which ended it at once (Kunena 6, "Add Namespaces"): every category had level 0 and the lists were flat.
 */
class KunenaCategoryLevelTest extends IntegrationTestCase implements DBTestInterface
{
    use DBTestTrait;

    private array $saved = [];

    /**
     * @return  string[]
     */
    public function getSchemasToLoad(): array
    {
        return ['kunena'];
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
        $application = $this->createStub(CMSApplicationInterface::class);
        $application->method('isClient')->willReturnCallback(static fn ($client) => $client === 'cli');

        $tree        = new ReflectionProperty(KunenaCategoryHelper::class, '_tree');
        $this->saved = [
            'container'   => Factory::$container,
            'application' => Factory::$application,
            'tree'        => $tree->getValue(),
        ];

        Factory::$container   = $container;
        Factory::$application = $application;
    }

    protected function tearDown(): void
    {
        if ($this->saved) {
            Factory::$container   = $this->saved['container'];
            Factory::$application = $this->saved['application'];
            (new ReflectionProperty(KunenaCategoryHelper::class, '_tree'))->setValue(null, $this->saved['tree']);
        }

        parent::tearDown();
    }

    public function testTheCategoriesGetTheLevelOfTheTree(): void
    {
        // A section, a category in it and a subcategory of that one.
        foreach ([[1, 0, 'Section'], [2, 1, 'Category'], [3, 2, 'Subcategory']] as [$id, $parent, $name]) {
            $this->insertRow('#__kunena_categories', [
                'id'        => $id,
                'parentid'  => $parent,
                'name'      => $name,
                'alias'     => strtolower($name),
                'published' => 1,
            ]);
        }

        $levels = array_map(static fn ($category) => (int) $category->level, KunenaCategoryHelper::loadCategories());
        ksort($levels);

        $this->assertSame([1 => 0, 2 => 1, 3 => 2], $levels);
    }
}
