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

namespace Kunena\Tests\Unit\Libraries\Kunena\Tools;

use Kunena\Forum\Libraries\Tools\KunenaTranslationCheck as T;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The tool "Translation check" finds the texts which are missing in a language, and those whose placeholders differ from
 * English or are glued to a word.
 */
class KunenaTranslationCheckTest extends UnitTestCase
{
    public function testThePlaceholdersOfAText(): void
    {
        $this->assertSame(['d', 's'], T::signature('%s posted %d messages, 100%% sure'));
        $this->assertSame(['s', 's'], T::signature('%2$s and %1$s'));
        $this->assertSame(['f'], T::signature('%05.2f'));
        $this->assertSame([], T::signature('No placeholder, 50%% off'));
    }

    public function testAMissingText(): void
    {
        $result = T::compare(['A' => 'One', 'B' => 'Two'], ['A' => 'Een'], 'nl-NL');

        $this->assertSame(['B'], $result['missing']);
        $this->assertSame([], $result['problems']);
    }

    public function testPlaceholdersWhichDifferFromEnglish(): void
    {
        $result = T::compare(
            ['A' => 'Last edit: %s by %s.', 'B' => '%d posts', 'C' => 'The file %s'],
            ['A' => 'Laatst bewerkt %s.', 'B' => '%s berichten', 'C' => "Het bestand '\$file'"],
            'nl-NL'
        );

        // A placeholder more makes sprintf() fail: it comes first. A placeholder less only drops the value.
        $this->assertSame(['B', 'A', 'C'], array_column($result['problems'], 'key'));
        $this->assertSame([T::EXTRA, T::DROPPED, T::DROPPED], array_column($result['problems'], 'kind'));
    }

    /**
     * "%time%" is a token of Kunena, not a placeholder of sprintf; "% s" (with a space) is a broken placeholder.
     */
    public function testNoPlaceholderWithASpace(): void
    {
        $this->assertSame([], T::compare(['A' => '%time% ago'], ['A' => '%time% geleden'], 'nl-NL')['problems']);
        $this->assertSame(T::DROPPED, T::compare(['A' => 'Same IP address: %s'], ['A' => 'Hetzelfde IP-adres:% s'], 'nl-NL')['problems'][0]['kind']);
    }

    /**
     * The mistakes which were in the Kunena translations: the name glued to "door" (Dutch) and to "Düzenleyen:" (Turkish).
     */
    public function testAPlaceholderGluedToAWord(): void
    {
        $english = ['A' => 'Last edit: %s by %s.'];

        $this->assertSame(T::GLUED, T::compare($english, ['A' => 'Laatst bewerkt %s door%s.'], 'nl-NL')['problems'][0]['kind']);
        $this->assertSame(T::GLUED, T::compare($english, ['A' => 'Son düzenleme: %s Düzenleyen:%s'], 'tr-TR')['problems'][0]['kind']);
        $this->assertSame([], T::compare($english, ['A' => 'Laatst bewerkt %s door %s.'], 'nl-NL')['problems']);
    }

    public function testNoGluedPlaceholderWhereEnglishHasOneOrTheLanguageHasNoSpaces(): void
    {
        $this->assertSame([], T::compare(['A' => 'Page:%d'], ['A' => 'Pagina:%d'], 'nl-NL')['problems']);
        $this->assertSame([], T::compare(['A' => 'Posted by %s'], ['A' => '由%s发布'], 'zh-CN')['problems']);
    }
}
