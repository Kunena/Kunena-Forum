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
use Nbbc\BBCode;

/**
 * [hr] is a horizontal line, as [rule]: SCEditor writes it (its button of the horizontal line), the reports of plg_kunena_reports
 * have it, and the help of the editor shows it. The tag was not known, so it was shown as text.
 */
class KunenaBBCodeHrTest extends UnitTestCase
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

    public function testHrIsAHorizontalLine(): void
    {
        $html = (new BBCode(self::bare(KunenaBBCodeLibrary::class)))->Parse('a[hr]b');

        $this->assertStringContainsString('<hr class="bbcode_rule" />', $html);
        $this->assertStringNotContainsString('[hr]', $html);
    }

    public function testThePlainTextHasALine(): void
    {
        $parser = new BBCode(self::bare(KunenaBBCodeLibrary::class));
        $parser->SetPlainMode(true);

        $this->assertStringNotContainsString('[hr]', $parser->Parse('a[hr]b'));
    }
}
