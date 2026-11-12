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

namespace Kunena\Tests\Unit\Component\Site\Service;

use Kunena\Forum\Libraries\Forum\Category\KunenaCategory;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategoryHelper;
use Kunena\Forum\Site\Service\Router;
use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The router also reads the parent categories before the category (forum/main-forum/welcome-mat/subcategory2).
 */
class RouterTest extends UnitTestCase
{
    public const SEF_VIEWS = ['' => 1, 'home' => 1, 'category' => 1, 'topic' => 1];

    private mixed $saved = null;

    private Router $router;

    protected function setUp(): void
    {
        $this->requireJoomla();

        $this->saved = KunenaCategoryHelper::$_instances;

        // main-forum (1) > welcome-mat (2) > subcategory2 (3); other (4) is under main-forum.
        KunenaCategoryHelper::$_instances = [
            1 => self::category(1, 0, 'main-forum'),
            2 => self::category(2, 1, 'welcome-mat'),
            3 => self::category(3, 2, 'subcategory2'),
            4 => self::category(4, 1, 'other'),
        ];

        $this->router = new class () extends Router {
            public array $aliases = [
                'main-forum'   => ['catid' => 1, 'view' => 'category'],
                'welcome-mat'  => ['catid' => 2, 'view' => 'category'],
                'subcategory2' => ['catid' => 3, 'view' => 'category'],
                'other'        => ['catid' => 4, 'view' => 'category'],
            ];

            public int $lookups = 0;

            public function __construct()
            {
            }

            public function segments(array $segments, array $vars): array
            {
                return $this->parseSegments($segments, $vars);
            }

            // Loading KunenaRoute starts the application, which the tests do not have.
            protected function getSefViews(): array
            {
                return RouterTest::SEF_VIEWS;
            }

            protected function resolveAlias(string $alias): array
            {
                $this->lookups++;

                return $this->aliases[$alias] ?? [];
            }
        };
    }

    protected function tearDown(): void
    {
        KunenaCategoryHelper::$_instances = $this->saved;
    }

    private static function category(int $id, int $parent, string $alias): KunenaCategory
    {
        return self::bare(KunenaCategory::class, ['id' => $id, 'parentid' => $parent, 'alias' => $alias]);
    }

    private function parse(array $segments, array $vars = ['view' => '']): array
    {
        return $this->router->segments($segments, $vars);
    }

    private function isDescendant(int $catid, int $ancestor): bool
    {
        return self::invoke($this->router, 'isDescendantCategory', $catid, $ancestor);
    }

    public function testTheSefViewsOfTheTestAreThoseOfKunenaRoute(): void
    {
        $this->assertStringContainsString(
            "public static array \$sefviews = ['' => 1, 'home' => 1, 'category' => 1, 'topic' => 1];",
            Files::read('src/libraries/kunena/src/Route/KunenaRoute.php')
        );
    }

    public function testTheChainOpensTheDeepestCategory(): void
    {
        $vars = $this->parse(['main-forum', 'welcome-mat', 'subcategory2']);

        $this->assertSame(['catid' => 3, 'view' => 'category'], $vars);
    }

    public function testTheChainFollowedByATopicOpensTheTopic(): void
    {
        $long  = $this->parse(['main-forum', 'welcome-mat', 'subcategory2', '123-topicname']);
        $short = $this->parse(['subcategory2', '123-topicname']);

        $this->assertSame(['catid' => 3, 'view' => 'topic', 'id' => 123], $long);
        $this->assertSame(['catid' => 3, 'view' => 'topic', 'id' => 123], $short);
        $this->assertSame($short, $long);
    }

    public function testTheRestOfTheUrlAfterTheChainIsHandledAsBefore(): void
    {
        $this->assertSame(
            ['catid' => 3, 'view' => 'topic', 'id' => 123, 'layout' => 'reply', 'mesid' => 124],
            $this->parse(['main-forum', 'subcategory2', '123-topicname', 'reply', '124'])
        );
        $this->assertSame(['catid' => 3, 'view' => 'category', 'layout' => 'create'], $this->parse(['main-forum', 'subcategory2', 'create']));
    }

    public function testShortUrlsAreUnchanged(): void
    {
        $this->assertSame(['catid' => 3, 'view' => 'category'], $this->parse(['subcategory2']));
        $this->assertSame(['catid' => 3, 'view' => 'topic', 'id' => 123], $this->parse(['subcategory2', '123-topicname']));
        $this->assertSame(['catid' => 3, 'view' => 'category', 'layout' => 'create'], $this->parse(['subcategory2', 'create']));
        $this->assertSame(['catid' => 3, 'view' => 'topic', 'id' => 123, 'layout' => 'reply'], $this->parse(['subcategory2', '123-topicname', 'reply']));
    }

    public function testASiblingCategoryEndsTheChain(): void
    {
        $this->assertSame(['catid' => 2, 'view' => 'category', 'layout' => 'other'], $this->parse(['welcome-mat', 'other']));
    }

    public function testAnAncestorCategoryEndsTheChain(): void
    {
        $this->assertSame(['catid' => 3, 'view' => 'category', 'main' => 'forum'], $this->parse(['subcategory2', 'main-forum']));
    }

    public function testNoAliasIsLookedUpAfterTheChainEnded(): void
    {
        $this->assertSame(
            ['catid' => 1, 'view' => 'topic', 'id' => 123, 'welcome' => 'mat'],
            $this->parse(['main-forum', '123-topicname', 'welcome-mat'])
        );
        $this->assertSame(2, $this->router->lookups);
    }

    public function testAMenuItemWhichNamesAViewKeepsIt(): void
    {
        $vars = $this->parse(['main-forum', 'subcategory2'], ['view' => 'topic', 'layout' => 'create']);

        $this->assertSame(['catid' => 3, 'view' => 'topic', 'layout' => 'create'], $vars);
    }

    public function testAMenuItemWithAnIdResolvesNoAlias(): void
    {
        $this->parse(['main-forum', 'subcategory2'], ['view' => 'topic', 'id' => 5]);

        $this->assertSame(0, $this->router->lookups);
    }

    public function testDescendantCategories(): void
    {
        $this->assertTrue($this->isDescendant(3, 1));
        $this->assertTrue($this->isDescendant(3, 2));
        $this->assertFalse($this->isDescendant(4, 2));
        $this->assertFalse($this->isDescendant(1, 3));
        $this->assertFalse($this->isDescendant(3, 3));
    }

    public function testACycleOfParentsIsNoDescendant(): void
    {
        // 5 > 6 > 7 > 5
        KunenaCategoryHelper::$_instances[5] = self::category(5, 7, 'five');
        KunenaCategoryHelper::$_instances[6] = self::category(6, 5, 'six');
        KunenaCategoryHelper::$_instances[7] = self::category(7, 6, 'seven');

        $this->assertFalse($this->isDescendant(7, 5));
        $this->assertFalse($this->isDescendant(7, 1));
        $this->assertFalse($this->isDescendant(5, 5));
    }
}
