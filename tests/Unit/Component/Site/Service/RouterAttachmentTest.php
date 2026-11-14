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

use Joomla\CMS\Application\CMSApplicationInterface;
use Joomla\CMS\Factory;
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Forum\Site\Service\Router;
use Kunena\Tests\Unit\UnitTestCase;
use ReflectionProperty;

/**
 * The address of an attachment (issue #10017), as KunenaAttachment::getUrl() makes it with the protection of the attachments on:
 * index.php?option=com_kunena&view=attachment&id=N[&thumb=1][&download=1]&format=raw, without Itemid. The router writes it as
 * attachment/N[/thumb] and reads it back to the same attachment.
 */
class RouterAttachmentTest extends UnitTestCase
{
    private Router $router;

    protected function setUp(): void
    {
        $this->requireJoomla();
        $this->loadRoute();

        // build() reads the constant which the component defines when it starts.
        \defined('KUNENA_PROFILER') or \define('KUNENA_PROFILER', 0);

        $this->router = new class () extends Router {
            public function __construct()
            {
            }

            public function segments(array $segments, array $vars): array
            {
                return $this->parseSegments($segments, $vars);
            }

            // Kunena installed, with the SEF URLs on.
            protected function isEnabled(): bool
            {
                return true;
            }

            // Loading KunenaRoute::$sefviews in parseSegments() starts the application, which the tests do not have.
            protected function getSefViews(): array
            {
                return RouterTest::SEF_VIEWS;
            }

            // No category has the alias "attachment".
            protected function resolveAlias(string $alias): array
            {
                return [];
            }
        };
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
        $saved       = $config->getValue();
        $application = Factory::$application;
        $cli         = $this->createStub(CMSApplicationInterface::class);
        $cli->method('isClient')->willReturn(true);

        $config->setValue(null, $this->bare(KunenaConfig::class));
        Factory::$application = $cli;

        try {
            class_exists(KunenaRoute::class);
        } finally {
            $config->setValue(null, $saved);
            Factory::$application = $application;
        }
    }

    private function parse(array $segments, array $vars = ['view' => '']): array
    {
        return $this->router->segments($segments, $vars);
    }

    private function build(array $query): array
    {
        return [$this->router->build($query), $query];
    }

    public function testTheAddressOfAnAttachmentIsRead(): void
    {
        $this->assertSame(['view' => 'attachment', 'id' => 12, 'format' => 'raw'], $this->parse(['attachment', '12']));
    }

    public function testTheAddressOfAThumbnailIsRead(): void
    {
        $this->assertSame(['view' => 'attachment', 'id' => 12, 'thumb' => 1, 'format' => 'raw'], $this->parse(['attachment', '12', 'thumb']));
    }

    public function testTheAddressOfAnAttachmentIsWritten(): void
    {
        [$segments, $query] = $this->build(['option' => 'com_kunena', 'view' => 'attachment', 'id' => 12, 'format' => 'raw']);

        $this->assertSame(['attachment', 12], $segments);
        $this->assertSame(['option' => 'com_kunena'], $query);
    }

    public function testTheAddressOfAThumbnailIsWritten(): void
    {
        [$segments, $query] = $this->build(['option' => 'com_kunena', 'view' => 'attachment', 'id' => 12, 'thumb' => 1, 'format' => 'raw']);

        $this->assertSame(['attachment', 12, 'thumb'], $segments);
        $this->assertSame(['option' => 'com_kunena'], $query);
    }

    public function testTheDownloadStaysInTheQuery(): void
    {
        [$segments, $query] = $this->build(['option' => 'com_kunena', 'view' => 'attachment', 'id' => 12, 'download' => 1, 'format' => 'raw']);

        $this->assertSame(['attachment', 12], $segments);
        $this->assertSame(['option' => 'com_kunena', 'download' => 1], $query);

        [$segments, $query] = $this->build(['option' => 'com_kunena', 'view' => 'attachment', 'id' => 12, 'thumb' => 1, 'download' => 1, 'format' => 'raw']);

        $this->assertSame(['attachment', 12, 'thumb'], $segments);
        $this->assertSame(['option' => 'com_kunena', 'download' => 1], $query);
    }

    public function testTheWrittenAddressIsReadBackToTheSameAttachment(): void
    {
        foreach ([0, 1] as $thumb) {
            $query = ['option' => 'com_kunena', 'view' => 'attachment', 'id' => 12, 'format' => 'raw'] + ($thumb ? ['thumb' => 1] : []);

            [$segments] = $this->build($query);

            // The segments come back from the address as strings.
            $vars = $this->parse(array_map('strval', $segments));

            $this->assertSame('attachment', $vars['view']);
            $this->assertSame(12, $vars['id']);
            $this->assertSame($thumb, $vars['thumb'] ?? 0);
            $this->assertSame('raw', $vars['format']);
        }
    }
}
