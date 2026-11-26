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

namespace Kunena\Tests\Unit\Component;

use Kunena\Forum\Libraries\Model\KunenaModel;
use Kunena\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use ReflectionClass;

/**
 * The models of Kunena say that their component is com_kunena. A model of Joomla without that reads it from the namespace of its
 * class, what follows "\Component\": the namespace of Kunena has no such part, and Joomla made "com_m" of "...\Model". The events
 * of the model (the clean-up of the cache, the plugins of a form) and the context of its saved filters then had that name.
 */
class ModelOptionTest extends UnitTestCase
{
    /**
     * The model classes of the backend and of the site. The model of the configuration is no model of the database of Joomla and
     * has no component.
     */
    public static function models(): array
    {
        $root   = \dirname(__DIR__, 3);
        $models = [];

        foreach (['admin' => 'Kunena\\Forum\\Administrator\\Model\\', 'site' => 'Kunena\\Forum\\Site\\Model\\'] as $folder => $namespace) {
            foreach (glob("$root/src/$folder/src/Model/*Model.php") as $file) {
                $name = basename($file, '.php');

                if ($name !== 'ConfigModel') {
                    $models["$folder $name"] = [$namespace . $name];
                }
            }
        }

        return $models;
    }

    #[DataProvider('models')]
    public function testTheModelIsOfKunena(string $class): void
    {
        $this->requireJoomla();

        // The models of the library say it in their constructor, before the one of Joomla reads it.
        if (is_subclass_of($class, KunenaModel::class)) {
            $this->assertStringContainsString(
                "\$this->option = 'com_kunena';\n        parent::__construct(",
                str_replace("\r\n", "\n", file_get_contents((new ReflectionClass(KunenaModel::class))->getFileName()))
            );

            return;
        }

        $this->assertSame('com_kunena', (new ReflectionClass($class))->getDefaultProperties()['option'] ?? null);
    }

    public function testAllTheModelsAreChecked(): void
    {
        $this->assertGreaterThan(30, \count(self::models()));
    }
}
