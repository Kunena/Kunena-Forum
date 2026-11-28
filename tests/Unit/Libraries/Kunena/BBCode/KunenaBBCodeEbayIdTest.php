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

use Kunena\Forum\Libraries\BBCode\KunenaBBCodeLibrary;
use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * The number of an item of eBay ([ebay] and a pasted link of eBay, embedded by default) is digits only: is_numeric() took 1e400,
 * 1.5 or a number beyond an int of PHP, which renderEbayLayout(int) refused with a TypeError, and the post was an error box for
 * every reader. (The tag needs the template of a site: the method which checks the number is run, the callers are read.)
 */
class KunenaBBCodeEbayIdTest extends UnitTestCase
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

        // The file of KunenaBBCode loads the parser from the folder of the library.
        if (!\defined('KPATH_FRAMEWORK')) {
            \define('KPATH_FRAMEWORK', Files::path('src/libraries/kunena'));
        }

        if (!class_exists(KunenaBBCodeLibrary::class, false)) {
            require_once Files::path(self::BBCODE);
        }
    }

    public static function ids(): array
    {
        return [
            'an item'              => ['123456789012', true],
            'an exponent'          => ['1e400', false],
            'a fraction'           => ['1.5', false],
            'beyond an int of PHP' => ['99999999999999999999', false],
            'a sign'               => ['-5', false],
            'empty'                => ['', false],
        ];
    }

    #[DataProvider('ids')]
    public function testTheNumberOfAnItem(string $id, bool $valid): void
    {
        $this->assertSame($valid, KunenaBBCodeLibrary::isEbayItemId($id));
    }

    public function testTheTagAndTheLinkCheckTheNumber(): void
    {
        $source = Files::read(self::BBCODE);

        $this->assertStringNotContainsString('is_numeric($content)', $source);
        $this->assertStringNotContainsString('is_numeric($path[', $source);
        $this->assertStringContainsString('return self::renderEbayLayout((int) $content);', $source);
        $this->assertStringContainsString('return KunenaBBCodeLibrary::renderEbayLayout((int) $itemid);', $source);
    }
}
