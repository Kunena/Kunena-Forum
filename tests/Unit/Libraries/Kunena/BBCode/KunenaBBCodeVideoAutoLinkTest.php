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
 * [video] leaves the counter of the links (autoLink_disable) as it is. The tag has an "allow" list, which the parser checks itself:
 * it never calls DoVideo() to check the tag, so the counter was never raised, and the lowering of it in the output put it below 0.
 * After any [video] of a message every [img] skipped the security check of the images (bbcodeImgSecure: an address which is no
 * picture is shown as text) and lost its link to the full picture.
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

    public function testTheVideoLeavesTheCounterOfTheLinks(): void
    {
        // The parser checks a tag with an "allow" list by that list only (Nbbc BBCode::doTag()).
        $rules = (new \ReflectionClass(KunenaBBCodeLibrary::class))->getDefaultProperties()['default_tag_rules'] ?? null;
        $this->assertIsArray($rules['video']['allow'] ?? null, 'the video has an allow list');

        $library = self::bare(KunenaBBCodeLibrary::class);
        $bbcode  = (object) ['autoLink_disable' => 0, 'parent' => (object) [], 'context' => ''];

        $library->DoVideo($bbcode, BBCode::BBCODE_OUTPUT, 'video', null, [], '');
        $this->assertSame(0, $bbcode->autoLink_disable, 'after the video');
    }
}
