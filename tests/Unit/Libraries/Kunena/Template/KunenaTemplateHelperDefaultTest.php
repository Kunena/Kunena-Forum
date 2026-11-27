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

namespace Kunena\Tests\Unit\Libraries\Kunena\Template;

use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\Template\KunenaTemplateHelper;
use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;
use ReflectionProperty;

/**
 * The uninstall of a template refuses the locked templates and the one in use. Both were compared with the case of the name of
 * the request: on Windows and macOS "Agora" is the folder agora, and the uninstall of "Agora" deleted it.
 */
class KunenaTemplateHelperDefaultTest extends UnitTestCase
{
    public function testTheTemplateInUseIsFoundWhateverTheCase(): void
    {
        $this->requireJoomla();

        $instance = new ReflectionProperty(KunenaConfig::class, 'instance');
        $saved    = $instance->getValue();
        $instance->setValue(null, self::bare(KunenaConfig::class, ['config' => ['template' => 'agora']]));

        try {
            $this->assertSame(1, KunenaTemplateHelper::isDefault('agora'));
            $this->assertSame(1, KunenaTemplateHelper::isDefault('Agora'));
            $this->assertSame(0, KunenaTemplateHelper::isDefault('aurelia'));
        } finally {
            $instance->setValue(null, $saved);
        }
    }

    public function testALockedTemplateIsNotUninstalledWhateverTheCase(): void
    {
        $source = Files::read('src/admin/src/Controller/TemplateController.php');

        $this->assertStringContainsString("\\in_array(strtolower((string) \$id), array_map('strtolower', \$this->locked), true)", $source);
    }
}
