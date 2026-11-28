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

namespace Kunena\Tests\Integration\Libraries\Kunena\Tools;

use Joomla\CMS\Cache\CacheControllerFactoryInterface;
use Joomla\CMS\Cache\Controller\OutputController;
use Joomla\CMS\Factory;
use Joomla\Database\DatabaseInterface;
use Joomla\DI\Container;
use Kunena\Forum\Libraries\Cache\KunenaLayoutCache;
use Kunena\Forum\Libraries\Tools\KunenaAnnouncementsReview;
use Kunena\Tests\Integration\DBTestInterface;
use Kunena\Tests\Integration\DBTestTrait;
use Kunena\Tests\Integration\IntegrationTestCase;
use ReflectionProperty;

/**
 * The tool "Announcements review" empties the cache of the layouts when it unpublishes announcements, as a save of an announcement
 * does: the banner of the index and the list are cached per type of user, and the unpublished announcements stayed shown until the
 * cache time of the forum ran out.
 */
class KunenaAnnouncementsReviewUnpublishTest extends IntegrationTestCase implements DBTestInterface
{
    use DBTestTrait;

    private mixed $container;

    private array $cleaned = [];

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

        // The cache records the groups which it empties.
        // (clean() of the cache is a magic method of the controller of Joomla: a subclass has it.)
        $output = new class () extends OutputController {
            public ?\Closure $onClean = null;

            public function __construct()
            {
            }

            public function clean($group = null)
            {
                return ($this->onClean)($group);
            }
        };
        $output->onClean = function ($group): bool {
            $this->cleaned[] = $group;

            return true;
        };
        $factory = $this->createStub(CacheControllerFactoryInterface::class);
        $factory->method('createCacheController')->willReturn($output);

        $driver    = $this->getDBDriver();
        $container = new Container();
        $container->share('DatabaseDriver', static fn () => $driver);
        $container->share(DatabaseInterface::class, static fn () => $driver);
        $container->share(CacheControllerFactoryInterface::class, static fn () => $factory);

        $this->container    = Factory::$container;
        Factory::$container = $container;
        (new ReflectionProperty(KunenaLayoutCache::class, 'cleared'))->setValue(null, false);

        $this->insertRow('#__kunena_announcement', ['id' => 5, 'title' => 'Old', 'sdescription' => '', 'description' => '', 'published' => 1]);
    }

    protected function tearDown(): void
    {
        if (\defined('JVERSION')) {
            Factory::$container = $this->container;
            (new ReflectionProperty(KunenaLayoutCache::class, 'cleared'))->setValue(null, false);
        }

        parent::tearDown();
    }

    public function testTheCacheIsEmptiedWhenAnnouncementsAreUnpublished(): void
    {
        $this->assertSame(1, KunenaAnnouncementsReview::unpublishOnSite([5]));
        $this->assertSame(0, (int) $this->fetchAll('SELECT published FROM #__kunena_announcement WHERE id = 5')[0]['published']);
        $this->assertSame([KunenaLayoutCache::GROUP], $this->cleaned);
    }
}
