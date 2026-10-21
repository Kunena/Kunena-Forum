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

namespace Kunena\Tests\Unit;

use Joomla\Registry\Registry;
use Kunena\Forum\Libraries\Forum\KunenaForum;
use ReflectionClass;

/**
 * The bootstrap of the tests: the classes of Kunena and of the tests are found, and the framework of Joomla is loaded when a Joomla
 * installation is given (KUNENA_TEST_JOOMLA).
 */
class BootstrapTest extends UnitTestCase
{
    public function testTheFilesOfKunenaRefuseToRunWithoutTheConstantOfJoomla(): void
    {
        $this->assertTrue(\defined('_JEXEC'));
    }

    public function testTheClassesOfKunenaAreFoundInThisCheckout(): void
    {
        $file = (new ReflectionClass(KunenaForum::class))->getFileName();

        $this->assertSame(
            str_replace('\\', '/', realpath(\dirname(__DIR__, 2) . '/src/libraries/kunena/src/Forum/KunenaForum.php')),
            str_replace('\\', '/', realpath($file))
        );
    }

    public function testTheTestsAreFoundByTheirNamespace(): void
    {
        $this->assertTrue(class_exists(\Kunena\Tests\Unit\Support\Files::class));
        $this->assertTrue(class_exists(UnitTestCase::class));
    }

    public function testTheParametersOfACategoryHaveARegistry(): void
    {
        $registry = new Registry('{"teaser":"1"}');

        $this->assertSame('1', $registry->get('teaser'));
        $this->assertSame('x', $registry->get('missing', 'x'));
    }

    public function testTheFrameworkOfJoomlaIsLoadedWhenAJoomlaIsGiven(): void
    {
        $this->requireJoomla();

        $this->assertTrue(class_exists(\Joomla\CMS\Version::class));
        $this->assertTrue(class_exists(\Joomla\Database\DatabaseFactory::class));
        $this->assertNotSame('', JVERSION);

        // The real Registry of Joomla is used, not the stand-in of the tests.
        $this->assertStringNotContainsString('RegistryStub.php', (string) (new ReflectionClass(Registry::class))->getFileName());
    }

    public function testWithoutJoomlaTheStandInIsUsed(): void
    {
        if (\defined('JVERSION')) {
            $this->markTestSkipped('The framework of Joomla is loaded.');
        }

        $this->assertStringContainsString('RegistryStub.php', (string) (new ReflectionClass(Registry::class))->getFileName());
    }
}
