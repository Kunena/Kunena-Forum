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

/**
 * The folder images of the "Legacy" topic icon type: the url()s of icons.scss are written unchanged into
 * media/kunena/cache/<template>/css/kunena.css, so they must be relative to that folder (they pointed to
 * media/kunena/cache/<template>/css/media/kunena/..., so the index showed no folder images).
 */
class LegacyFolderIconsTest extends UnitTestCase
{
    private const ICONS = 'src/site/template/aurelia/assets/scss/icons.scss';

    /**
     * The folders of the compiled CSS of the templates which import icons.scss.
     */
    private const CSS_FOLDERS = ['src/media/kunena/cache/aurelia/css', 'src/media/kunena/cache/agora/css'];

    /**
     * Class => image which it shows.
     */
    private const IMAGES = [
        '.kicon-folder'          => 'folder_nonew.png',
        '.kicon-folder.knewchar' => 'folder.png',
        '.kicon-folder-sm'       => 'folder_nonew_sm.png',
        '.kicon-folder-sm-new'   => 'folder_sm.png',
    ];

    private static function url(string $class): string
    {
        $pattern = '/^' . preg_quote($class, '/') . '\s*\{[^}]*url\(\s*["\']?([^"\')]+)["\']?\s*\)/m';

        if (!preg_match($pattern, Files::read(self::ICONS), $match)) {
            self::fail("icons.scss has no url() for $class.");
        }

        return $match[1];
    }

    /**
     * Resolves a relative path against a folder by string handling of '..' (the folder need not exist).
     */
    private static function resolve(string $folder, string $path): string
    {
        $parts = explode('/', $folder);

        foreach (explode('/', $path) as $part) {
            if ($part === '..') {
                array_pop($parts);
            } elseif ($part !== '.' && $part !== '') {
                $parts[] = $part;
            }
        }

        return implode('/', $parts);
    }

    public function testTheUrlsAreRelative(): void
    {
        foreach (array_keys(self::IMAGES) as $class) {
            $url = self::url($class);

            $this->assertStringStartsNotWith('/', $url, "The url() of $class starts at the root of the domain.");
            $this->assertDoesNotMatchRegularExpression('#^[a-z][a-z0-9+.-]*:#i', $url, "The url() of $class has a scheme.");
        }
    }

    public function testTheUrlsPointToTheImagesFromTheCompiledCss(): void
    {
        foreach (self::CSS_FOLDERS as $folder) {
            foreach (self::IMAGES as $class => $image) {
                $resolved = self::resolve($folder, self::url($class));

                $this->assertSame('src/media/kunena/core/images/icons/' . $image, $resolved, "The url() of $class does not point to $image from $folder.");
                $this->assertFileExists(Files::path($resolved));
            }
        }
    }

    public function testAgoraUsesTheSameRules(): void
    {
        $this->assertStringContainsString('@import "../../../aurelia/assets/scss/icons";', Files::read('src/site/template/agora/assets/scss/agora.scss'));
    }
}
