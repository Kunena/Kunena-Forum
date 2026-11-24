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

namespace Kunena\Tests\Unit\Codebase;

use Joomla\CMS\Application\AdministratorApplication;
use Joomla\CMS\Factory;
use Joomla\CMS\Installer\InstallerAdapter;
use Joomla\CMS\Installer\InstallerScriptInterface;
use Joomla\Database\DatabaseInterface;
use Joomla\Database\Mysqli\MysqliDriver;
use Joomla\Database\Mysqli\MysqliQuery;
use Joomla\DI\Container;
use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;
use ReflectionProperty;
use SimpleXMLElement;

/**
 * The installer script of the package (src/script.php) keeps the custom styles of the templates (custom.scss) during an update: it
 * copies them to a temporary file before the files of the package are written, and back afterwards. It used the folder tmp of the
 * site, not the folder for temporary files of the Joomla configuration: on a site which has that folder elsewhere (and no folder
 * tmp in the site, as the hardening guides advise), the copy failed and threw, and every update of Kunena stopped in preflight.
 */
class PackageScriptTemporaryFolderTest extends UnitTestCase
{
    private string $temporary = '';

    /** @var array<string,string|null> */
    private array $siteFiles = [];

    private mixed $savedContainer = null;

    private mixed $savedApplication = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->requireJoomla();

        $this->temporary = sys_get_temp_dir() . '/kunena-package-tmp-' . bin2hex(random_bytes(4));
        mkdir($this->temporary);

        // The temporary files which the old code wrote in the folder tmp of the site of the tests are put back afterwards.
        foreach (['agora', 'aurelia'] as $template) {
            $file                   = JPATH_SITE . "/tmp/custom-{$template}.scss";
            $this->siteFiles[$file] = is_file($file) ? (string) file_get_contents($file) : null;
        }

        $this->savedContainer   = Factory::$container;
        $this->savedApplication = Factory::$application;
    }

    protected function tearDown(): void
    {
        foreach ($this->siteFiles as $file => $content) {
            if ($content === null) {
                @unlink($file);
            } else {
                file_put_contents($file, $content);
            }
        }

        foreach ((array) glob($this->temporary . '/*') as $file) {
            @unlink($file);
        }

        if (is_dir($this->temporary)) {
            rmdir($this->temporary);
        }

        Factory::$container   = $this->savedContainer;
        Factory::$application = $this->savedApplication;

        parent::tearDown();
    }

    public function testTheCustomStylesAreKeptInTheTemporaryFolderOfJoomla(): void
    {
        // A database without Kunena (no installed version: no folder of an old version is removed), MySQL 8. Small classes of their
        // own instead of stubs: the stubs of these two large classes took the memory of the whole suite over its limit.
        $db = new class () extends MysqliDriver {
            public $name = 'mysqli';

            public function __construct()
            {
            }

            public function createQuery(): MysqliQuery
            {
                return new MysqliQuery();
            }

            public function setQuery($query, $offset = 0, $limit = 0)
            {
                return $this;
            }

            public function getPrefix()
            {
                return 'jos_';
            }

            public function escape($text, $extra = false)
            {
                return addslashes((string) $text);
            }

            public function getVersion()
            {
                return '8.0.40';
            }

            public function loadResult()
            {
                return null;
            }
        };

        $temporary = $this->temporary;
        $app       = new class ($temporary) extends AdministratorApplication {
            public function __construct(private string $temporary)
            {
            }

            public function get($name, $default = null)
            {
                return $name === 'tmp_path' ? $this->temporary : $default;
            }

            public function enqueueMessage($msg, $type = self::MSG_INFO)
            {
            }
        };

        $container = new Container();
        $container->share(DatabaseInterface::class, static fn () => $db);
        $container->share(AdministratorApplication::class, static fn () => $app);
        Factory::$container   = $container;
        Factory::$application = $app;

        $provider = require Files::path('src/script.php');
        $provider->register($container);
        $script = $container->get(InstallerScriptInterface::class);

        // The test is about the temporary folder, not the requirements: any version of Joomla passes (the Joomla of the site of
        // the tests can be a development version, which is older than the release it leads to for version_compare()).
        $versions = new ReflectionProperty($script, 'versions');
        $versions->setValue($script, ['Joomla!' => ['1.0' => '1.0.0', '0' => '1.0.0']] + $versions->getValue($script));

        $parent           = $this->createStub(InstallerAdapter::class);
        $parent->manifest = new SimpleXMLElement('<extension><version>8.0.0</version></extension>');

        $this->assertTrue($script->preflight('update', $parent));

        foreach (['agora', 'aurelia'] as $template) {
            $this->assertFileExists($this->temporary . "/custom-{$template}.scss", "The custom style of $template is not in the temporary folder of Joomla.");
        }
    }
}
