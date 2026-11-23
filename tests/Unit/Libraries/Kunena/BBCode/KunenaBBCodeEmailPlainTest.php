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

use DOMDocument;
use Kunena\Forum\Libraries\BBCode\KunenaBBCodeLibrary;
use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;
use Nbbc\BBCode;

/**
 * [email] in the plain mode (the data of the log in the backend, KunenaParser::plainBBCode) is a link whose address is the text of
 * the tag: the text was put in the attribute as it was, so a quote in it closed the attribute and the rest became attributes of the
 * link (an edit reason "[email]x" onmouseover=alert(1) a=" ran a script in the backend of the administrator).
 */
class KunenaBBCodeEmailPlainTest extends UnitTestCase
{
    private const BBCODE = 'src/libraries/kunena/src/BBCode/KunenaBBCode.php';

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
            require_once Files::path(self::BBCODE);
        }
    }

    public function testAQuoteInTheAddressStaysInTheAttribute(): void
    {
        $parser = new BBCode();
        $parser->SetPlainMode(true);
        $parser->SetDetectURLs(false);
        $parser->SetEnableSmileys(false);
        $parser->AddRule('email', self::bare(KunenaBBCodeLibrary::class)->default_tag_rules['email']);

        $html = (string) $parser->Parse('[email]x" onmouseover=alert(1) a=[/email]');

        $dom = new DOMDocument();
        @$dom->loadHTML('<html><body>' . $html . '</body></html>');
        $link = $dom->getElementsByTagName('a')->item(0);

        $this->assertNotNull($link, $html);
        $this->assertFalse($link->hasAttribute('onmouseover'), $html);
    }
}
