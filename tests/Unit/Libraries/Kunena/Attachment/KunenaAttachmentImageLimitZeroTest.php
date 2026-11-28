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

namespace Kunena\Tests\Unit\Libraries\Kunena\Attachment;

use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * "Maximum Image Width" or "Maximum Image Height" of 0 is no limit on the server, as in the uploader of the editor (the options allow
 * 0): every picture was resized to the other limit (0 and 800 made a picture of 300 x 200 one of 1200 x 800), and with both at 0 the
 * upload ended in a division by zero. The upload of the editor and the upload without it take the size of the picture for a limit
 * of 0. (The uploads need a whole site: their code is read.)
 */
class KunenaAttachmentImageLimitZeroTest extends UnitTestCase
{
    public static function uploads(): array
    {
        return [
            'the upload of the editor' => ['src/site/src/Controllers/TopicController.php', '$this->config->'],
            'the upload without it'    => ['src/libraries/kunena/src/Attachment/KunenaAttachment.php', '$config->'],
        ];
    }

    #[DataProvider('uploads')]
    public function testALimitOfZeroIsNoLimit(string $file, string $config): void
    {
        $source = Files::read($file);

        $this->assertStringContainsString('$maxWidth  = (int) ' . $config . 'imageWidth > 0 ? (int) ' . $config . 'imageWidth : (int) $imageInfo->width;', $source);
        $this->assertStringContainsString('$maxHeight = (int) ' . $config . 'imageHeight > 0 ? (int) ' . $config . 'imageHeight : (int) $imageInfo->height;', $source);
        $this->assertStringContainsString('$imageInfo->width > $maxWidth || $imageInfo->height > $maxHeight', $source);
        $this->assertMatchesRegularExpression('/->resize\(\$maxWidth, \$maxHeight, false\)/', $source);
        $this->assertStringNotContainsString('resize(' . $config . 'imageWidth', $source);
    }
}
