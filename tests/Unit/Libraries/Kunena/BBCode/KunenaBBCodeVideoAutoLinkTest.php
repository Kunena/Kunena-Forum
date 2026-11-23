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
 * [video] stops the links of the addresses inside it (autoLink_disable, raised when the tag is checked), and lowers the counter again
 * when it is written, as [url] and [tableau] do. It never lowered it: after any [video] of a message, every [img] skipped the security
 * check of the images (bbcodeImgSecure: an address which is no picture is shown as text) and lost its link to the full picture.
 */
class KunenaBBCodeVideoAutoLinkTest extends UnitTestCase
{
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

        if (!class_exists(KunenaBBCodeLibrary::class, false)) {
            require_once Files::path('src/libraries/kunena/src/BBCode/KunenaBBCode.php');
        }
    }

    public function testTheVideoGivesTheLinksBackAfterIt(): void
    {
        $library = self::bare(KunenaBBCodeLibrary::class);
        $bbcode  = (object) ['autoLink_disable' => 0, 'parent' => (object) [], 'context' => ''];

        $library->DoVideo($bbcode, BBCode::BBCODE_CHECK, 'video', null, [], '');
        $this->assertSame(1, $bbcode->autoLink_disable, 'inside the video');

        $library->DoVideo($bbcode, BBCode::BBCODE_OUTPUT, 'video', null, [], '');
        $this->assertSame(0, $bbcode->autoLink_disable, 'after the video');
    }
}
