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

namespace Kunena\Tests\Unit\Plugin\Kunena\Joomla;

use Joomla\Registry\Registry;
use Kunena\Forum\Libraries\Event\KunenaGetLoginEvent;
use Kunena\Forum\Plugin\Kunena\Joomla\Extension\Joomla;
use Kunena\Forum\Plugin\Kunena\Joomla\Helper\KunenaLoginJoomla;
use Kunena\Tests\Unit\UnitTestCase;
use ReflectionProperty;

/**
 * The option "Enable Joomla Login" of the plugin Kunena - Joomla: with No, the forum uses another login (Community Builder, ...).
 * The plugin gave its login in any case, and the forum takes the first login it gets.
 */
class JoomlaLoginOptionTest extends UnitTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->requireJoomla();

        $root = \dirname(__DIR__, 5);
        require_once $root . '/src/plugins/plg_kunena_joomla/src/Extension/Joomla.php';
        require_once $root . '/src/plugins/plg_kunena_joomla/src/Helper/KunenaLoginJoomla.php';
    }

    private function logins(int $login): array
    {
        $plugin = self::bare(Joomla::class);
        (new ReflectionProperty(Joomla::class, 'params'))->setValue($plugin, new Registry(['login' => $login]));

        $event = new KunenaGetLoginEvent('onKunenaGetLogin', []);
        $plugin->onKunenaGetLogin($event);

        return (array) $event->getArgument('result', []);
    }

    public function testTheJoomlaLoginIsGivenWhenTheOptionIsOn(): void
    {
        $logins = $this->logins(1);

        $this->assertCount(1, $logins);
        $this->assertInstanceOf(KunenaLoginJoomla::class, $logins[0]);
    }

    public function testNoLoginIsGivenWhenTheOptionIsOff(): void
    {
        $this->assertSame([], $this->logins(0));
    }
}
