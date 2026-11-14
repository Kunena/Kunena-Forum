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

namespace Kunena\Tests\Unit\Libraries\Kunena\Attachment;

use Joomla\CMS\Application\CMSApplicationInterface;
use Joomla\CMS\Application\SiteApplication;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\DI\Container;
use Joomla\Registry\Registry;
use Kunena\Forum\Libraries\Attachment\KunenaAttachment;
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Tests\Unit\UnitTestCase;
use ReflectionProperty;

/**
 * The address of a PDF attachment follows the rule of every other attachment (issue #7850): with the protection of the
 * attachments on, the PDF shown in the message goes through the forum, which checks the access and sends it as a PDF; the
 * stored file, with its random name without .pdf, is not shown to the browser.
 */
class KunenaAttachmentPdfUrlTest extends UnitTestCase
{
    private const string FOLDER = 'media/kunena/attachments/42';

    private const string FILENAME = '0123456789abcdef0123456789abcdef';

    /**
     * The values which the test replaces, put back after the test.
     */
    private array $saved = [];

    protected function setUp(): void
    {
        $this->requireJoomla();
        $this->loadRoute();

        $site = $this->createStub(CMSApplicationInterface::class);
        $site->method('isClient')->willReturn(false);

        $container = (new Container())->set(SiteApplication::class, new Registry(['sef_suffix' => 0]));

        $this->saved = [
            'config'      => (new ReflectionProperty(KunenaConfig::class, 'instance'))->getValue(),
            'application' => Factory::$application,
            'container'   => Factory::$container,
            'adminApp'    => KunenaRoute::$adminApp,
            'base'        => (new ReflectionProperty(Uri::class, 'base'))->getValue(),
            'root'        => (new ReflectionProperty(Uri::class, 'root'))->getValue(),
        ];

        Factory::$application = $site;
        Factory::$container   = $container;

        // The routing of the administration gives the plain address, without the menus of a site, on the site at
        // https://example.com/forum/.
        KunenaRoute::$adminApp = true;
        (new ReflectionProperty(Uri::class, 'base'))->setValue(null, ['prefix' => 'https://example.com', 'path' => '/forum']);
        (new ReflectionProperty(Uri::class, 'root'))->setValue(null, ['prefix' => 'https://example.com', 'path' => '/forum']);
    }

    protected function tearDown(): void
    {
        if (!$this->saved) {
            return;
        }

        (new ReflectionProperty(KunenaConfig::class, 'instance'))->setValue(null, $this->saved['config']);
        Factory::$application  = $this->saved['application'];
        Factory::$container    = $this->saved['container'];
        KunenaRoute::$adminApp = $this->saved['adminApp'];
        (new ReflectionProperty(Uri::class, 'base'))->setValue(null, $this->saved['base']);
        (new ReflectionProperty(Uri::class, 'root'))->setValue(null, $this->saved['root']);
    }

    /**
     * Loading KunenaRoute runs KunenaRoute::initialize(), which needs the configuration and an application: give it an empty
     * configuration and an application of the command line, for which it stops at once, and take both back afterwards.
     */
    private function loadRoute(): void
    {
        if (class_exists(KunenaRoute::class, false)) {
            return;
        }

        $config      = new ReflectionProperty(KunenaConfig::class, 'instance');
        $application = Factory::$application;
        $cli         = $this->createStub(CMSApplicationInterface::class);
        $cli->method('isClient')->willReturn(true);

        $config->setValue(null, $this->bare(KunenaConfig::class));
        Factory::$application = $cli;

        try {
            class_exists(KunenaRoute::class);
        } finally {
            $config->setValue(null, null);
            Factory::$application = $application;
        }
    }

    /**
     * A public PDF of a registered user, with the protection of the attachments on or off.
     */
    private function pdf(bool $protection): KunenaAttachment
    {
        (new ReflectionProperty(KunenaConfig::class, 'instance'))
            ->setValue(null, $this->bare(KunenaConfig::class, ['config' => ['attachmentProtection' => (int) $protection]]));

        return $this->bare(KunenaAttachment::class, [
            'id'        => 77,
            'userid'    => 0,
            'mesid'     => 0,
            'protected' => 0,
            'folder'    => self::FOLDER,
            'filename'  => self::FILENAME,
            'filetype'  => 'application/pdf',
        ]);
    }

    public function testAProtectedPdfGoesThroughTheForum(): void
    {
        $url = $this->pdf(true)->getUrl();

        $this->assertSame('/forum/index.php?option=com_kunena&view=attachment&id=77&format=raw', $url);
        $this->assertStringNotContainsString(self::FILENAME, $url);
    }

    public function testTheDownloadOfAProtectedPdfAsksForTheDownload(): void
    {
        $url = $this->pdf(true)->getUrl(false, false);

        $this->assertStringContainsString('view=attachment', $url);
        $this->assertStringContainsString('id=77', $url);
        $this->assertStringContainsString('download=1', $url);
        $this->assertStringContainsString('format=raw', $url);
        $this->assertStringNotContainsString(self::FILENAME, $url);
    }

    public function testAnUnprotectedPublicPdfKeepsTheDirectAddress(): void
    {
        $url = $this->pdf(false)->getUrl();

        $this->assertSame('https://example.com/forum/' . self::FOLDER . '/' . self::FILENAME, $url);
    }
}
