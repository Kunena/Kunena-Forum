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
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;
use ReflectionProperty;

/**
 * An eBay item ([ebay]123[/ebay], or a pasted address of an item with "embed eBay" on, the default) is shown as a widget when the forum
 * has the keys of the eBay API, else as a link to the item. Without the keys (the default) the item printed the error for the
 * administrator ("No eBay APP ID ...") into the post, for every reader, and the link of the member disappeared from the post.
 */
class KunenaBBCodeEbayWithoutKeysTest extends UnitTestCase
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

    private mixed $config = null;

    protected function tearDown(): void
    {
        if ($this->config !== null) {
            (new ReflectionProperty(KunenaConfig::class, 'instance'))->setValue(null, $this->config);
        }

        parent::tearDown();
    }

    public function testAnItemWithoutTheKeysIsALinkAndNoError(): void
    {
        $this->requireJoomla();

        $instance     = new ReflectionProperty(KunenaConfig::class, 'instance');
        $this->config = $instance->getValue();
        $instance->setValue(null, self::bare(KunenaConfig::class, ['config' => ['ebayApiKey' => '', 'ebayCertId' => '']]));

        $this->expectOutputString('');

        $html = (string) KunenaBBCodeLibrary::renderEbayLayout(123456789012);

        $this->assertStringContainsString('href="https://www.ebay.com/itm/123456789012"', $html);
    }
}
