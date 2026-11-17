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

namespace Kunena\Tests\Unit\Libraries\Kunena\Html;

use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\Html\KunenaLightbox;
use Kunena\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use ReflectionProperty;

/**
 * The attributes of the link of an image for each choice of "Enable Lightbox for Images": the lightbox of Kunena, the lightbox of the
 * site (Fancybox) or none.
 */
class KunenaLightboxTest extends UnitTestCase
{
    private mixed $savedConfig;

    protected function setUp(): void
    {
        parent::setUp();
        $this->requireJoomla();
        $this->savedConfig = (new ReflectionProperty(KunenaConfig::class, 'instance'))->getValue();
    }

    protected function tearDown(): void
    {
        (new ReflectionProperty(KunenaConfig::class, 'instance'))->setValue(null, $this->savedConfig);
        parent::tearDown();
    }

    private function lightbox(mixed $value): void
    {
        (new ReflectionProperty(KunenaConfig::class, 'instance'))->setValue(null, $this->bare(KunenaConfig::class, ['config' => ['lightbox' => $value]]));
    }

    public function testTheLightboxOfKunenaOpensTheLinksOfItsGroup(): void
    {
        $this->lightbox(1);

        $this->assertSame(' data-kunena-lightbox="gallery"', KunenaLightbox::attributes());
        $this->assertSame(' data-kunena-lightbox="none"', KunenaLightbox::attributes('none'));
        $this->assertTrue(KunenaLightbox::isKunena());
    }

    public function testTheLightboxOfTheSiteGetsTheAttributesOfFancybox(): void
    {
        $this->lightbox(2);

        // data-type: the address of an attachment has no extension of an image.
        $this->assertSame(' data-fancybox="gallery" data-type="image"', KunenaLightbox::attributes());
        $this->assertFalse(KunenaLightbox::isKunena());
    }

    public static function noLightbox(): array
    {
        return [
            'no'           => [0],
            'not set'      => [null],
            'unknown'      => [7],
            'not a number' => ['yes'],
        ];
    }

    #[DataProvider('noLightbox')]
    public function testWithoutALightboxTheLinkHasNoAttributes(mixed $value): void
    {
        $this->lightbox($value);

        $this->assertSame('', KunenaLightbox::attributes());
        $this->assertSame(KunenaLightbox::NONE, KunenaLightbox::mode());
        $this->assertFalse(KunenaLightbox::isKunena());
    }

    public function testTheDownloadAddressAndTheNameGoToTheLightboxOfKunena(): void
    {
        $this->lightbox(1);

        $this->assertSame(
            ' data-kunena-lightbox="gallery" data-kunena-download="/index.php?view=attachment&amp;id=7&amp;download=1" data-kunena-filename="my &quot;photo&quot;.png"',
            KunenaLightbox::attributes('gallery', '/index.php?view=attachment&id=7&download=1', 'my "photo".png')
        );
    }

    public function testTheLightboxOfTheSiteGetsNoDownloadAttributes(): void
    {
        $this->lightbox(2);

        $this->assertSame(' data-fancybox="gallery" data-type="image"', KunenaLightbox::attributes('gallery', '/a.png', 'a.png'));
    }

    public function testTheGroupIsAnAttributeValueOnly(): void
    {
        $this->lightbox(1);

        $this->assertSame(' data-kunena-lightbox="a&quot; onclick=&quot;x()"', KunenaLightbox::attributes('a" onclick="x()'));
    }
}
