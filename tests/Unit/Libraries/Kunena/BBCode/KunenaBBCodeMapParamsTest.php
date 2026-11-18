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

use Kunena\Forum\Libraries\BBCode\KunenaBBCode;
use Kunena\Forum\Libraries\BBCode\KunenaBBCodeLibrary;
use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use ReflectionClass;

/**
 * The parameters of [map] as the layouts read them: the map dialog of CKEditor wrote the zoom as mode= (every map had the zoom by
 * default), and both map dialogs wrote the type satellite as satelite (Google Maps showed the road map).
 */
class KunenaBBCodeMapParamsTest extends UnitTestCase
{
    private const BBCODE = 'src/libraries/kunena/src/BBCode/KunenaBBCode.php';

    protected function setUp(): void
    {
        // The parser is in the folder External of the library, and the library of the tags is in the file of KunenaBBCode.
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

        if (!class_exists(KunenaBBCode::class, false)) {
            require_once Files::path(self::BBCODE);
        }
    }

    public static function params(): array
    {
        return [
            'zoom and type as written'  => [['type' => 'roadmap', 'zoom' => '4'], ['type' => 'roadmap', 'zoom' => '4']],
            'mode is the zoom'          => [['type' => 'ROADMAP', 'mode' => '4'], ['type' => 'ROADMAP', 'mode' => '4', 'zoom' => '4']],
            'zoom wins over mode'       => [['zoom' => '6', 'mode' => '4'], ['zoom' => '6', 'mode' => '4']],
            'satelite is satellite'     => [['type' => 'SATELITE'], ['type' => 'satellite']],
            'satelite in small letters' => [['type' => 'satelite'], ['type' => 'satellite']],
            'satellite stays'           => [['type' => 'SATELLITE'], ['type' => 'SATELLITE']],
            'no parameters'             => [[], []],
        ];
    }

    #[DataProvider('params')]
    public function testMapParams(array $params, array $expected): void
    {
        $this->assertSame($expected, KunenaBBCode::mapParams($params));
    }

    public function testTheRuleAllowsModeAsANumberOnly(): void
    {
        $rules   = (new ReflectionClass(KunenaBBCodeLibrary::class))->getDefaultProperties()['default_tag_rules'];
        $pattern = $rules['map']['allow']['mode'];

        $this->assertSame(1, preg_match($pattern, '12'));
        $this->assertSame(0, preg_match($pattern, '1 onload=x'));
    }
}
