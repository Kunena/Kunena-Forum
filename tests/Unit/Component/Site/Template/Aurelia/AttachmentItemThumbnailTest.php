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

use Closure;
use Joomla\CMS\Application\CMSApplicationInterface;
use Joomla\CMS\Factory;
use Joomla\Input\Input;
use Joomla\Registry\Registry;
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\Template\KunenaTemplate;
use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use ReflectionProperty;

/**
 * A picture in the attachment list of a message is shown from its inline address, never from the download address, which
 * the forum sends as a forced download when the attachments are protected (issue #10013).
 */
class AttachmentItemThumbnailTest extends UnitTestCase
{
    private const string TEMPLATE = 'attachmentitemthumbnailtest';

    private const string INLINE = '/forum/index.php?option=com_kunena&amp;view=attachment&amp;id=77&amp;format=raw';

    private const string DOWNLOAD = '/forum/index.php?option=com_kunena&amp;view=attachment&amp;id=77&amp;download=1&amp;format=raw';

    /**
     * The values which the test replaces, put back after the test.
     */
    private array $saved = [];

    public static function layouts(): array
    {
        return [
            'aurelia' => ['src/site/template/aurelia/layouts/attachment/item/thumbnail.php'],
            'system'  => ['src/site/template/system/layouts/attachment/item/thumbnail.php'],
        ];
    }

    protected function setUp(): void
    {
        $this->requireJoomla();

        if (!\defined('_JEXEC')) {
            \define('_JEXEC', 1);
        }

        $application = $this->createStub(CMSApplicationInterface::class);
        $application->method('getInput')->willReturn(new Input([]));

        $this->saved = [
            'config'      => (new ReflectionProperty(KunenaConfig::class, 'instance'))->getValue(),
            'templates'   => (new ReflectionProperty(KunenaTemplate::class, '_instances'))->getValue(),
            'application' => Factory::$application,
        ];

        Factory::$application = $application;

        $template = $this->bare(KunenaTemplate::class, ['params' => new Registry(['topicicontype' => 'fa'])]);
        (new ReflectionProperty(KunenaTemplate::class, '_instances'))->setValue(null, [self::TEMPLATE => $template]);
    }

    protected function tearDown(): void
    {
        if (!$this->saved) {
            return;
        }

        (new ReflectionProperty(KunenaConfig::class, 'instance'))->setValue(null, $this->saved['config']);
        (new ReflectionProperty(KunenaTemplate::class, '_instances'))->setValue(null, $this->saved['templates']);
        Factory::$application = $this->saved['application'];
    }

    /**
     * The configuration of the forum, with the protection of the attachments on and the lightbox on or off.
     */
    private function config(bool $lightbox): void
    {
        (new ReflectionProperty(KunenaConfig::class, 'instance'))->setValue(null, $this->bare(KunenaConfig::class, ['config' => [
            'template'                  => self::TEMPLATE,
            'attachmentProtection'      => 1,
            'lightbox'                  => (int) $lightbox,
            'thumbHeight'               => 32,
            'thumbWidth'                => 32,
            'attachStart'               => 10,
            'attachEnd'                 => 14,
            'displayFilenameAttachment' => 0,
        ]]));
    }

    private function render(string $layoutFile, bool $image, bool $lightbox): string
    {
        $this->config($lightbox);

        // The download address has download=1, as KunenaAttachment::getUrl() gives it when $inline is false.
        $attachment = new class ($image, self::INLINE, self::DOWNLOAD) {
            public function __construct(private bool $image, private string $inline, private string $download)
            {
            }

            public function isImage(): bool
            {
                return $this->image;
            }

            public function getPath(): string
            {
                return '/path/to/the/file';
            }

            public function getUrl($thumb = false, $inline = true, $escape = true): string
            {
                return $inline ? $this->inline : $this->download;
            }

            public function getShortName($start, $end): string
            {
                return $this->image ? 'picture.png' : 'notes.txt';
            }

            public function getFilename(): string
            {
                return $this->image ? 'picture.png' : 'notes.txt';
            }
        };

        $layout = new class ($attachment) {
            public function __construct(public object $attachment)
            {
            }

            public function subLayout(string $name): string
            {
                return '';
            }
        };

        $file   = Files::path($layoutFile);
        $render = function () use ($file): void {
            include $file;
        };

        ob_start();

        try {
            Closure::bind($render, $layout)();
        } finally {
            $output = ob_get_clean();
        }

        return $output;
    }

    #[DataProvider('layouts')]
    public function testAPictureIsShownFromItsInlineAddress(string $layoutFile): void
    {
        $output = $this->render($layoutFile, true, false);

        $this->assertStringContainsString('src="' . self::INLINE . '"', $output);
        $this->assertStringContainsString('href="' . self::INLINE . '"', $output);
        $this->assertStringNotContainsString('download=1', $output);
    }

    public function testAFileWhichIsNotAPictureLinksToTheDownloadInAurelia(): void
    {
        $output = $this->render('src/site/template/aurelia/layouts/attachment/item/thumbnail.php', false, true);

        $this->assertStringContainsString('href="' . self::DOWNLOAD . '"', $output);
        $this->assertStringNotContainsString('<img', $output);
        $this->assertStringNotContainsString('data-fancybox', $output);
    }

    #[DataProvider('layouts')]
    public function testWithTheLightboxThePictureOpensAsAnImage(string $layoutFile): void
    {
        $output = $this->render($layoutFile, true, true);

        $this->assertStringContainsString(' data-fancybox="gallery" data-type="image"', $output);
    }

    #[DataProvider('layouts')]
    public function testWithoutTheLightboxThePictureLinkHasNoLightboxAttributes(string $layoutFile): void
    {
        $output = $this->render($layoutFile, true, false);

        $this->assertStringNotContainsString('data-fancybox', $output);
        $this->assertStringNotContainsString('data-type', $output);
    }
}
