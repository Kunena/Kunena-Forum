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
 * [map] with Google Maps asks the geocoder for the place as it is written: the parser gives the text of the tag encoded, and the
 * geocoder got "L&#039;Aquila" (or "&amp;"), which found no place, or another one. The info window, which is HTML, keeps the encoded
 * text. (The layout needs a whole site: its code is read, and the encoding of the place is run.)
 */
class MapGeocoderPlaceTest extends UnitTestCase
{
    public function testTheGeocoderGetsThePlaceAsItIsWritten(): void
    {
        $source = Files::read('src/site/template/aurelia/layouts/bbcode/map/default.php');

        $this->assertStringContainsString('var address = {$address};', $source);
        $this->assertStringContainsString('$address       = json_encode(htmlspecialchars_decode((string) $this->content, ENT_QUOTES), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);', $source);

        // The text of the parser, and what the script gets: the place, with nothing which could leave the string of the script.
        $address = json_encode(htmlspecialchars_decode('L&#039;Aquila &amp; &lt;b&gt;', ENT_QUOTES), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
        $this->assertSame("L'Aquila & <b>", json_decode((string) $address));

        foreach (["'", '&', '<', '>'] as $character) {
            $this->assertStringNotContainsString($character, (string) $address);
        }
    }
}
