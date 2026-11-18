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
 * The alt of [img]: any text is allowed by the rule of the tag (an alt of word characters only refused the whole tag, which stayed as
 * BBCode in the message), and the words are read from the tag as written, without the quotes around them.
 */
class KunenaBBCodeImageAltTest extends UnitTestCase
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

    public static function alts(): array
    {
        return [
            'one word'                     => ['[img alt=logo]', 'logo'],
            'several words without quotes' => ['[img alt=my words]', 'my words'],
            'several words in quotes'      => ['[img alt="my words"]', 'my words'],
            'in single quotes'             => ["[img alt='my words']", 'my words'],
            'followed by another param'    => ['[img alt="my words" size=200]', 'my words'],
            'an unclosed quote is kept'    => ['[img alt="><b>probe</b>]', '"><b>probe</b>'],
            'no alt'                       => ['[img size=200]', null],
            'an empty alt'                 => ['[img alt=]', ''],
        ];
    }

    #[DataProvider('alts')]
    public function testImageAlt(string $tag, ?string $expected): void
    {
        $this->assertSame($expected, KunenaBBCode::imageAlt($tag));
    }

    public static function allowedAlts(): array
    {
        return [
            'one word'             => ['logo', true],
            'several words'        => ['my words', true],
            'punctuation'          => ['"><b>probe</b>', true],
            'letters with accents' => ['café à Paris', true],
            'a bracket'            => ['a ] b', false],
        ];
    }

    public function testHidetextIsTheTagHide(): void
    {
        // The button "Hide text" of SCEditor wrote [hidetext], which was no tag: the text was shown to everyone.
        $rules = (new ReflectionClass(KunenaBBCodeLibrary::class))->getDefaultProperties()['default_tag_rules'];

        $this->assertArrayHasKey('hidetext', $rules);
        $this->assertSame($rules['hide'], $rules['hidetext']);
    }

    #[DataProvider('allowedAlts')]
    public function testTheRuleOfTheTagAllowsAnyAltText(string $alt, bool $allowed): void
    {
        $rules   = (new ReflectionClass(KunenaBBCodeLibrary::class))->getDefaultProperties()['default_tag_rules'];
        $pattern = $rules['img']['allow']['alt'];

        $this->assertSame($allowed, preg_match($pattern, $alt) === 1);
    }
}
