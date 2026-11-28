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

namespace Kunena\Tests\Unit\Component\Site\Template\Aurelia;

use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * "Maximum Image Height" of 0 (the option allows it, the uploader takes it for no limit) gives the pictures of the posts no height:
 * the attachments and the [img] of the lightbox had max-height:0px, which hid them. (The layouts need a whole site: their code is
 * read.)
 */
class ImageMaxHeightZeroTest extends UnitTestCase
{
    public static function layouts(): array
    {
        $layouts = [];

        foreach (['aurelia', 'system'] as $template) {
            foreach (['attachment/item/image.php', 'bbcode/image/default.php'] as $layout) {
                $layouts[$template . ' ' . $layout] = ['src/site/template/' . $template . '/layouts/' . $layout];
            }
        }

        return $layouts;
    }

    #[DataProvider('layouts')]
    public function testAHeightOfZeroIsNoHeight(string $file): void
    {
        $source = Files::read($file);

        $this->assertStringContainsString('(int) $config->imageHeight > 0 ?', $source);
        $this->assertDoesNotMatchRegularExpression("/'max-height: ?' \\. \\(int\\) \\\$config->imageHeight \\. 'px;'(?! :)/", $source);
    }
}
