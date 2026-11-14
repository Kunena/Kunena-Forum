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

namespace Kunena\Tests\Unit\Libraries\Kunena\Icons;

use Joomla\CMS\Application\CMSApplicationInterface;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Language;
use Joomla\Input\Input;
use Joomla\Registry\Registry;
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\Icons\KunenaIcons;
use Kunena\Forum\Libraries\Template\KunenaTemplate;
use Kunena\Tests\Unit\UnitTestCase;
use ReflectionProperty;

/**
 * The SVG icons of the categories carry the class kcaticon, and knewchar when the category has new posts, so that the
 * colours of the template reach them (issue #9252).
 */
class KunenaIconsTest extends UnitTestCase
{
    private const string TEMPLATE = 'kunenaiconstest';

    /**
     * The values which the test replaces, put back after the test.
     */
    private array $saved = [];

    protected function setUp(): void
    {
        $this->requireJoomla();

        $application = $this->createStub(CMSApplicationInterface::class);
        $application->method('getInput')->willReturn(new Input([]));
        $language = $this->createStub(Language::class);
        $language->method('_')->willReturnArgument(0);

        $this->saved = [
            'config'      => (new ReflectionProperty(KunenaConfig::class, 'instance'))->getValue(),
            'templates'   => (new ReflectionProperty(KunenaTemplate::class, '_instances'))->getValue(),
            'application' => Factory::$application,
            'language'    => Factory::$language,
        ];

        Factory::$application = $application;
        Factory::$language    = $language;
        (new ReflectionProperty(KunenaConfig::class, 'instance'))
            ->setValue(null, $this->bare(KunenaConfig::class, ['config' => ['template' => self::TEMPLATE]]));
    }

    protected function tearDown(): void
    {
        if (!$this->saved) {
            return;
        }

        (new ReflectionProperty(KunenaConfig::class, 'instance'))->setValue(null, $this->saved['config']);
        (new ReflectionProperty(KunenaTemplate::class, '_instances'))->setValue(null, $this->saved['templates']);
        Factory::$application = $this->saved['application'];
        Factory::$language    = $this->saved['language'];
    }

    /**
     * The template of the forum, with the type of the icons.
     */
    private function iconType(string $type): void
    {
        $template = $this->bare(KunenaTemplate::class, ['params' => new Registry(['topicicontype' => $type])]);

        (new ReflectionProperty(KunenaTemplate::class, '_instances'))->setValue(null, [self::TEMPLATE => $template]);
    }

    public function testTheDefaultSvgIconIsWrappedInKcaticon(): void
    {
        $this->iconType('svg');

        $this->assertStringStartsWith('<span class="kcaticon">', KunenaIcons::caticon('', false));
        $this->assertStringEndsWith('</span>', KunenaIcons::caticon('', false));
        $this->assertStringStartsWith('<span class="kcaticon">', KunenaIcons::caticon('', false, false));
    }

    public function testTheDefaultSvgIconOfANewCategoryHasKnewchar(): void
    {
        $this->iconType('svg');

        $this->assertStringStartsWith('<span class="kcaticon knewchar">', KunenaIcons::caticon('', true));
        $this->assertStringEndsWith('</span>', KunenaIcons::caticon('', true));
        $this->assertStringStartsWith('<span class="kcaticon knewchar">', KunenaIcons::caticon('', true, false));
    }

    public function testAnSvgIconWithoutFileHasKcaticon(): void
    {
        $this->iconType('svg');

        $icon = KunenaIcons::caticon('kunena-no-such-icon', false);

        $this->assertMatchesRegularExpression('/^<span class="icon kunena-no-such-icon icon-big kcaticon"/', $icon);
        $this->assertStringNotContainsString('knewchar', $icon);
    }

    public function testAnSvgIconWithoutFileOfANewCategoryHasKnewchar(): void
    {
        $this->iconType('svg');

        $icon = KunenaIcons::caticon('kunena-no-such-icon', true);

        $this->assertMatchesRegularExpression('/^<span class="icon kunena-no-such-icon icon-big kcaticon knewchar"/', $icon);
    }

    public function testAFontAwesomeIconIsUnchanged(): void
    {
        $this->iconType('fa');

        $new = KunenaIcons::caticon('fa-folder', true);
        $old = KunenaIcons::caticon('fa-folder', false);

        $this->assertMatchesRegularExpression('/^<i class="fa fa-folder fa-3x knewchar"/', $new);
        $this->assertMatchesRegularExpression('/^<i class="fa fa-folder fa-3x "/', $old);
        $this->assertStringNotContainsString('kcaticon', $new . $old);
    }
}
