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
use Nbbc\BBCode;
use Nbbc\BBCodeException;
use Nbbc\BBCodeLexer;

/**
 * The lexer of the parser Nbbc (Kunena 7.1): an unclosed [ followed by many quotes made the pattern of a tag backtrack until PCRE
 * failed (#10341), and the lexer then threw BBCodeException, a class which did not exist (#10332).
 */
class NbbcLexerTest extends UnitTestCase
{
    protected function setUp(): void
    {
        if (!\defined('_JEXEC')) {
            \define('_JEXEC', 1);
        }

        spl_autoload_register(static function ($class) {
            if (strpos($class, 'Nbbc\\') === 0) {
                $file = Files::path('src/libraries/kunena/External/Nbbc/src/' . substr($class, 5) . '.php');

                if (is_file($file)) {
                    require_once $file;
                }
            }
        });
    }

    public function testTheExceptionOfTheLexerExists(): void
    {
        $this->assertTrue(class_exists(BBCodeException::class));
        $this->assertTrue(is_subclass_of(BBCodeException::class, \Exception::class));
    }

    public function testAnUnclosedTagWithManyQuotesIsText(): void
    {
        $text  = 'Hello [b ' . str_repeat('"a" ', 20000) . 'world';
        $lexer = new BBCodeLexer($text);
        $out   = '';

        while ($lexer->nextToken() !== BBCode::BBCODE_EOI) {
            $out .= $lexer->text;
        }

        $this->assertSame($text, $out);
        $this->assertSame(PREG_NO_ERROR, preg_last_error());
    }
}
