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

use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The parser loads the library language file itself, because it can be used outside Kunena. It loads it through
 * KunenaFactory::loadLanguage(), which finds the translation in administrator/language first: a load from the folder of the
 * component alone finds only en-GB there and writes the English strings over the translation.
 */
class KunenaBBCodeLanguageTest extends UnitTestCase
{
    private const BBCODE = 'src/libraries/kunena/src/BBCode/KunenaBBCode.php';

    /**
     * The body of KunenaBBCode::__construct(), up to the closing brace of the method.
     */
    private function constructor(): string
    {
        $source = Files::read(self::BBCODE);
        $start  = strpos($source, 'public function __construct($relative = true)');
        $this->assertNotFalse($start, 'KunenaBBCode::__construct() is not found.');

        $end = strpos($source, "\n    }\n", $start);
        $this->assertNotFalse($end, 'The end of KunenaBBCode::__construct() is not found.');

        return substr($source, $start, $end - $start);
    }

    public function testTheConstructorLoadsTheLibraryLanguageThroughTheFactory(): void
    {
        $this->assertStringContainsString("KunenaFactory::loadLanguage('com_kunena.libraries', 'admin')", $this->constructor());
    }

    public function testTheConstructorHasNoDirectLoadOfTheLibraryLanguage(): void
    {
        $this->assertStringNotContainsString("->load('com_kunena.libraries'", $this->constructor());
    }
}
