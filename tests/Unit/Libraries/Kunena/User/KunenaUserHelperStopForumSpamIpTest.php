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

namespace Kunena\Tests\Unit\Libraries\Kunena\User;

use Exception;
use Joomla\CMS\Application\CMSApplicationInterface;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Language;
use Joomla\Database\DatabaseDriver;
use Joomla\Database\DatabaseInterface;
use Joomla\DI\Container;
use Joomla\Event\Dispatcher;
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\Integration\KunenaAvatar;
use Kunena\Forum\Libraries\User\KunenaUserHelper;
use Kunena\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use ReflectionProperty;

/**
 * A report to StopForumSpam with a private, local or reserved address ("TODO : prevent to do a request with a private or local
 * IP"): the service refuses it, and it would report the network of the site. It is refused before any request, with the reason.
 */
class KunenaUserHelperStopForumSpamIpTest extends UnitTestCase
{
    /**
     * The values which the test replaces, put back after the test.
     */
    private array $saved = [];

    protected function setUp(): void
    {
        $this->requireJoomla();

        // The texts are their keys; loading the helper makes the current user, which needs a site application.
        $language = $this->createStub(Language::class);
        $language->method('_')->willReturnArgument(0);
        $site = $this->createStub(CMSApplicationInterface::class);
        $site->method('get')->willReturnArgument(1);
        $site->method('getDispatcher')->willReturn(new Dispatcher());

        $this->saved = [
            'config'      => (new ReflectionProperty(KunenaConfig::class, 'instance'))->getValue(),
            'language'    => Factory::$language,
            'application' => Factory::$application,
        ];

        Factory::$application = $site;

        (new ReflectionProperty(KunenaConfig::class, 'instance'))->setValue(null, self::bare(KunenaConfig::class, ['config' => ['debug' => 0]]));
        Factory::$language = $language;

        $this->loadHelper();
    }

    protected function tearDown(): void
    {
        if (!$this->saved) {
            return;
        }

        (new ReflectionProperty(KunenaConfig::class, 'instance'))->setValue(null, $this->saved['config']);
        Factory::$language    = $this->saved['language'];
        Factory::$application = $this->saved['application'];
    }

    /**
     * Loading the helper runs KunenaUserHelper::initialize(), which makes the current user: it gets a stand-in of the database and
     * the avatars of Kunena itself, so that no database and no plugin is used.
     */
    private function loadHelper(): void
    {
        if (class_exists(KunenaUserHelper::class, false)) {
            return;
        }

        $db = $this->createStub(DatabaseDriver::class);
        $db->method('getTableColumns')->willReturn(['userid' => 'int', 'status' => 'tinyint']);

        $avatars = new ReflectionProperty(KunenaAvatar::class, 'instance');
        $saved   = [Factory::$container, $avatars->getValue()];

        Factory::$container = (new Container())->set(DatabaseInterface::class, $db)->set('DatabaseDriver', $db);
        $avatars->setValue(null, new KunenaAvatar());

        try {
            class_exists(KunenaUserHelper::class);
        } finally {
            Factory::$container = $saved[0];
            $avatars->setValue(null, $saved[1]);
        }
    }

    public static function addresses(): array
    {
        return [
            'a private network'  => ['192.168.1.5'],
            'another one'        => ['10.0.0.8'],
            'the local host'     => ['127.0.0.1'],
            'the local IPv6'     => ['::1'],
            'a unique local one' => ['fd00::1'],
            'no address'         => [''],
            'no address at all'  => ['not an address'],
        ];
    }

    #[DataProvider('addresses')]
    public function testAReportWithAPrivateAddressIsRefused(string $ip): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('COM_KUNENA_REPORTSPAM_PRIVATE_IP');

        KunenaUserHelper::storeCheckStopforumspam(
            ['username' => 'spammer', 'ip' => $ip, 'email' => 'spammer@example.com', 'stopForumSpamKey' => 'key', 'evidence' => 'Spam'],
            'add'
        );
    }
}
