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

use Kunena\Forum\Libraries\Html\KunenaMap;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The addresses of OpenStreetMap for the maps of the forum (the provider setting needs Joomla and is not tested here).
 */
class KunenaMapTest extends UnitTestCase
{
    public function testThePageHasTheMarkerAndTheZoom(): void
    {
        $url = KunenaMap::osmPageUrl(52.373, 4.892, 12);

        $this->assertSame('https://www.openstreetmap.org/?mlat=52.373&mlon=4.892#map=12/52.373/4.892', $url);
    }

    public function testTheZoomOfThePageStaysBetweenOneAndEighteen(): void
    {
        $this->assertStringContainsString('#map=18/', KunenaMap::osmPageUrl(1.0, 2.0, 99));
        $this->assertStringContainsString('#map=1/', KunenaMap::osmPageUrl(1.0, 2.0, -5));
    }

    public function testTheEmbeddedMapHasTheMarker(): void
    {
        $url = KunenaMap::osmEmbedUrl(52.373, 4.892, 10);

        $this->assertStringStartsWith('https://www.openstreetmap.org/export/embed.html?bbox=', $url);
        $this->assertStringContainsString('&layer=mapnik&marker=52.373,4.892', $url);
    }

    public function testTheMarkerIsInTheMiddleOfTheBox(): void
    {
        parse_str((string) parse_url(KunenaMap::osmEmbedUrl(10.0, 20.0, 8), PHP_URL_QUERY), $query);

        [$west, $south, $east, $north] = array_map('floatval', explode(',', $query['bbox']));

        $this->assertEqualsWithDelta(20.0, ($west + $east) / 2, 0.00001);
        $this->assertEqualsWithDelta(10.0, ($south + $north) / 2, 0.00001);
        $this->assertGreaterThan($west, $east);
        $this->assertGreaterThan($south, $north);
    }

    public function testAHigherZoomMakesASmallerBox(): void
    {
        $width = static function (string $url): float {
            parse_str((string) parse_url($url, PHP_URL_QUERY), $query);
            [$west, , $east] = array_map('floatval', explode(',', $query['bbox']));

            return $east - $west;
        };

        $this->assertGreaterThan($width(KunenaMap::osmEmbedUrl(0.0, 0.0, 14)), $width(KunenaMap::osmEmbedUrl(0.0, 0.0, 6)));
    }

    public function testTheZoomOfTheEmbeddedMapIsLimited(): void
    {
        $this->assertSame(KunenaMap::osmEmbedUrl(5.0, 5.0, 18), KunenaMap::osmEmbedUrl(5.0, 5.0, 99));
        $this->assertSame(KunenaMap::osmEmbedUrl(5.0, 5.0, 1), KunenaMap::osmEmbedUrl(5.0, 5.0, -3));
    }

    public function testTheNumbersHaveNoTrailingZeros(): void
    {
        $this->assertStringContainsString('marker=10,20', KunenaMap::osmEmbedUrl(10.0, 20.0, 10));
    }
}
