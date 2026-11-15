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
use Nbbc\BBCodeException;

/**
 * The lexer of the parser Nbbc throws BBCodeException when PCRE fails: the class did not exist (Kunena 7.1, #10332), so the error
 * became a fatal error.
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
}
