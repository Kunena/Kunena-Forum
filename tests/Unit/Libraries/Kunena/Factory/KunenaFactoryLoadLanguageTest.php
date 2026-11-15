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

namespace Kunena\Tests\Unit\Libraries\Kunena\Factory;

use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Language;
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Tests\Unit\UnitTestCase;
use ReflectionProperty;

/**
 * The language files of the forum (Kunena 8.0): a file is loaded once in a request. The key of the check and the key of the
 * result were not the same, so a page parsed the language files again at every call (about nine times a page).
 */
class KunenaFactoryLoadLanguageTest extends UnitTestCase
{
    private mixed $application = null;

    private ReflectionProperty $config;

    private ?KunenaConfig $savedConfig = null;

    protected function setUp(): void
    {
        $this->requireJoomla();

        $this->application = Factory::$application;
        $this->config      = new ReflectionProperty(KunenaConfig::class, 'instance');
        $this->savedConfig = $this->config->getValue();

        // A configuration without the English fallback: one load() for each file.
        $this->config->setValue(null, self::bare(KunenaConfig::class, ['config' => ['fallbackEnglish' => 0]]));
    }

    protected function tearDown(): void
    {
        Factory::$application = $this->application;
        $this->config->setValue(null, $this->savedConfig);
    }

    /**
     * The application of the test, with a language which counts the files it loads.
     */
    private function language(): Language
    {
        $language = $this->createMock(Language::class);

        $application = $this->createStub(CMSApplication::class);
        $application->method('getLanguage')->willReturn($language);
        Factory::$application = $application;

        return $language;
    }

    public function testAFileIsLoadedOnceInARequest(): void
    {
        // A name which no other test loads: the files which were loaded are kept for the whole request.
        $file = 'com_kunena.test_' . bin2hex(random_bytes(4));

        $this->language()->expects($this->once())->method('load')->willReturn(true);

        $this->assertTrue(KunenaFactory::loadLanguage($file, 'admin'));
        $this->assertTrue(KunenaFactory::loadLanguage($file, 'admin'));
        $this->assertTrue(KunenaFactory::loadLanguage($file, 'admin'));
    }

    public function testAFileWhichWasNotFoundIsLookedForAgain(): void
    {
        $file = 'com_kunena.test_' . bin2hex(random_bytes(4));

        // Not found in the language folder of Joomla, nor in the one of the component: two load() for each call.
        $this->language()->expects($this->exactly(4))->method('load')->willReturn(false);

        $this->assertFalse(KunenaFactory::loadLanguage($file, 'admin'));
        $this->assertFalse(KunenaFactory::loadLanguage($file, 'admin'));
    }
}
