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

namespace Kunena\Tests\Unit\Libraries\Kunena\BBCode;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Language;
use Kunena\Forum\Libraries\BBCode\KunenaBBCodeLibrary;
use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;
use Nbbc\BBCode;

/**
 * The error of a [map] without a place and of an [ebay] with a wrong item id is the output of the tag, in the post. It was printed
 * with echo from inside the parser: before the page, and into the JSON, raw and feed answers which have a post (they broke).
 */
class KunenaBBCodeErrorOutputTest extends UnitTestCase
{
    private mixed $language;

    protected function setUp(): void
    {
        $this->requireJoomla();

        spl_autoload_register(static function ($class) {
            if (strpos($class, 'Nbbc\\') === 0) {
                $file = Files::path('src/libraries/kunena/External/Nbbc/src/' . substr($class, 5) . '.php');

                if (is_file($file)) {
                    require_once $file;
                }
            }
        });

        if (!\defined('KPATH_FRAMEWORK')) {
            \define('KPATH_FRAMEWORK', Files::path('src/libraries/kunena'));
        }

        if (!class_exists(KunenaBBCodeLibrary::class, false)) {
            require_once Files::path('src/libraries/kunena/src/BBCode/KunenaBBCode.php');
        }

        $this->language    = Factory::$language;
        Factory::$language = $this->createStub(Language::class);
        Factory::$language->method('_')->willReturnArgument(0);
    }

    protected function tearDown(): void
    {
        Factory::$language = $this->language;
    }

    public function testTheErrorOfAMapIsItsOutput(): void
    {
        $library = self::bare(KunenaBBCodeLibrary::class);
        $bbcode  = (object) ['autoLink_disable' => 0, 'parent' => (object) [], 'context' => ''];

        $this->expectOutputString('');

        $this->assertStringContainsString(
            'COM_KUNENA_LIB_BBCODE_MAP_ERROR_CITY_MISSING',
            (string) $library->DoMap($bbcode, BBCode::BBCODE_OUTPUT, 'map', null, [], '  ')
        );

        // The [ebay] (its handler needs the template of the site first: its code is read).
        $source = Files::read('src/libraries/kunena/src/BBCode/KunenaBBCode.php');

        $this->assertStringContainsString("return '<b>' . Text::_('COM_KUNENA_LIB_BBCODE_EBAY_ERROR_WRONG_ITEM_ID') . '</b>';", $source);
        $this->assertStringNotContainsString("echo '<b>' . Text::_('COM_KUNENA_LIB_BBCODE_EBAY_ERROR_WRONG_ITEM_ID')", $source);
    }
}
