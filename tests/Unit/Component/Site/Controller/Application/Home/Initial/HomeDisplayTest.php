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

namespace Kunena\Tests\Unit\Component\Site\Controller\Application\Home\Initial;

use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Language;
use Joomla\CMS\Menu\AbstractMenu;
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Site\Controller\Application\Home\Initial\HomeDisplay;
use Kunena\Tests\Unit\UnitTestCase;
use ReflectionProperty;

/**
 * The default page of a Home menu item of the forum: a Home menu item can have another Home menu item as its default page, whose
 * own default page is then shown. The id of that menu item was given to the next step instead of the menu item, so the chain
 * always ended with nothing and the forum showed the category index (the breadcrumb found the right page).
 */
class HomeDisplayTest extends UnitTestCase
{
    private mixed $application = null;

    private ?ReflectionProperty $config = null;

    private ?KunenaConfig $savedConfig = null;

    protected function setUp(): void
    {
        $this->requireJoomla();

        if (!\defined('KPATH_SITE')) {
            \define('KPATH_SITE', \dirname(__DIR__, 8) . '/src/site');
        }

        // The method loads the language of the controllers: an application whose language loads nothing.
        $this->application = Factory::$application;
        $this->config      = new ReflectionProperty(KunenaConfig::class, 'instance');
        $this->savedConfig = $this->config->getValue();
        $this->config->setValue(null, self::bare(KunenaConfig::class, ['config' => ['fallbackEnglish' => 0]]));

        $application = $this->createStub(CMSApplication::class);
        $application->method('getLanguage')->willReturn($this->createStub(Language::class));
        Factory::$application = $application;
    }

    protected function tearDown(): void
    {
        if ($this->config) {
            Factory::$application = $this->application;
            $this->config->setValue(null, $this->savedConfig);
        }
    }

    private static function item(int $id, array $query): object
    {
        return (object) ['id' => $id, 'component' => 'com_kunena', 'query' => ['option' => 'com_kunena'] + $query];
    }

    private function menu(array $items): AbstractMenu
    {
        $menu = $this->createStub(AbstractMenu::class);
        $menu->method('getItem')->willReturnCallback(static fn ($id) => $items[(int) $id] ?? null);

        return $menu;
    }

    public function testTheDefaultPageOfAHomeMenuItem(): void
    {
        $home     = self::item(101, ['view' => 'home', 'defaultmenu' => 102]);
        $category = self::item(102, ['view' => 'category', 'layout' => 'list']);
        $display  = self::bare(HomeDisplay::class);

        $this->assertSame($category, self::invoke($display, 'getDefaultMenuItem', $this->menu([101 => $home, 102 => $category]), $home));
    }

    public function testTheDefaultPageOfAnotherHomeMenuItem(): void
    {
        $first   = self::item(101, ['view' => 'home', 'defaultmenu' => 102]);
        $second  = self::item(102, ['view' => 'home', 'defaultmenu' => 103]);
        $recent  = self::item(103, ['view' => 'topics', 'mode' => 'replies']);
        $display = self::bare(HomeDisplay::class);
        $menu    = $this->menu([101 => $first, 102 => $second, 103 => $recent]);

        $this->assertSame($recent, self::invoke($display, 'getDefaultMenuItem', $menu, $first));
    }
}
