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

namespace Kunena\Tests\Unit\Libraries\Kunena\User;

use Kunena\Forum\Libraries\User\KunenaUser;
use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * The rank images of Kunena are SVG images, which the reader of Joomla (getimagesize) does not read: their size comes from
 * their attributes. The images which the code and the sample data name are the ones in media/kunena/ranks.
 */
class KunenaUserRankImageSizeTest extends UnitTestCase
{
    private const NAMES = ['rank0', 'rank1', 'rank2', 'rank3', 'rank4', 'rank5', 'rank6', 'rankadmin', 'rankmod', 'rankbanned', 'rankdisabled', 'rankspammer'];

    private array $temporary = [];

    protected function tearDown(): void
    {
        foreach ($this->temporary as $file) {
            @unlink($file);
        }

        parent::tearDown();
    }

    private function file(string $content, string $extension = 'svg'): string
    {
        $file              = tempnam(sys_get_temp_dir(), 'krank') . '.' . $extension;
        $this->temporary[] = $file;
        file_put_contents($file, $content);

        return $file;
    }

    public static function images(): array
    {
        return array_combine(self::NAMES, array_map(static fn ($name) => [$name . '.svg'], self::NAMES));
    }

    #[DataProvider('images')]
    public function testTheImagesOfKunenaHaveTheSizeOfTheFormerImages(string $image): void
    {
        $this->assertSame([70, 16], KunenaUser::rankImageSize(Files::path('src/media/kunena/ranks/' . $image)));
    }

    public function testTheViewBoxGivesTheSizeWhenTheAttributesAreMissing(): void
    {
        $this->assertSame([48, 12], KunenaUser::rankImageSize($this->file('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 12"/>')));
    }

    public function testAMissingOrBrokenImageHasNoSize(): void
    {
        $this->assertSame([0, 0], KunenaUser::rankImageSize(sys_get_temp_dir() . '/no-such-rank-image.svg'));
        $this->assertSame([0, 0], KunenaUser::rankImageSize($this->file('<svg not xml')));
    }

    public function testAnImageWhichIsNotSvgIsReadByJoomla(): void
    {
        $this->requireJoomla();

        // A GIF of 3 x 2 pixels.
        $gif = base64_decode('R0lGODlhAwACAIAAAAAAAP///yH5BAEAAAEALAAAAAADAAIAAAIChF8AOw==');

        $this->assertSame([3, 2], KunenaUser::rankImageSize($this->file($gif, 'gif')));
    }

    public function testEveryImageWhichTheCodeNamesIsThere(): void
    {
        $code = Files::read('src/libraries/kunena/src/User/KunenaUser.php')
            . Files::read('src/libraries/kunena/src/Install/KunenaSampleData.php')
            . Files::read('src/script.php');

        preg_match_all("/'(rank[a-z0-9]*\\.[a-z]+)'/", $code, $matches);

        $this->assertNotEmpty($matches[1]);

        foreach (array_unique($matches[1]) as $image) {
            $this->assertFileExists(Files::path('src/media/kunena/ranks/' . $image), "The rank image $image is named in the code.");
        }
    }
}
