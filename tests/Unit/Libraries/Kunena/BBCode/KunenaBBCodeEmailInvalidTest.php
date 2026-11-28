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
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * An [email] whose address is not one keeps the tag and its text, as [img] and [url] do: the tag and all its text were gone from the
 * post (the plain text of the excerpts kept them).
 */
class KunenaBBCodeEmailInvalidTest extends UnitTestCase
{
    protected function setUp(): void
    {
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
    }

    public static function texts(): array
    {
        return [
            'no address'                 => ['a [email]not an email[/email] b', 'not an email'],
            'an address which is wrong'  => ['a [email=bad]Important text[/email] b', 'Important text'],
        ];
    }

    #[DataProvider('texts')]
    public function testTheTextStays(string $post, string $text): void
    {
        // The parser with the tags of Kunena, as the forum makes it.
        $parser         = new BBCode(self::bare(KunenaBBCodeLibrary::class));
        $parser->SetDetectURLs(false);
        $parser->SetEnableSmileys(false);

        $this->assertStringContainsString($text, (string) $parser->Parse($post));
    }
}
