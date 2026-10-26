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

use Kunena\Forum\Libraries\Html\KunenaParser;
use Kunena\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * The text for some readers only ([confidential], [hide]) is taken out of the texts which everybody sees (the description of
 * the page, the tooltips, the feeds, the index of the search), like the parser hides it on the page.
 */
class KunenaParserRemoveBlocksTest extends UnitTestCase
{
    public static function texts(): array
    {
        return [
            'a block'                          => ['open [confidential]secret[/confidential] end', 'open  end'],
            'the name in capitals'             => ['open [CONFIDENTIAL]secret[/Confidential] end', 'open  end'],
            'a block in a block'               => ['open [confidential]a[confidential]b[/confidential]secret[/confidential] end', 'open  end'],
            'three levels'                     => ['[hide]a[hide]b[hide]c[/hide]d[/hide]secret[/hide]open', 'open'],
            'two tags in each other'           => ['[hide]a[confidential]b[/confidential]secret[/hide]open', 'open'],
            'over several lines'               => ["open [hide]line 1\nsecret[/hide] end", 'open  end'],
            'two blocks'                       => ['[hide]x[/hide] open [hide]secret[/hide]', ' open '],
            'a space in the closing tag'       => ['open [hide]secret[/hide ] end', 'open  end'],
            'another tag is kept'              => ['open [b]bold[/b] [hide]secret[/hide]', 'open [b]bold[/b] '],
            'a tag with a similar name stays'  => ['open [hidden]text[/hidden]', 'open [hidden]text[/hidden]'],
            'an unclosed tag stays as text'    => ['open [hide]text', 'open [hide]text'],
        ];
    }

    #[DataProvider('texts')]
    public function testTheBlocksAreTakenOut(string $text, string $expected): void
    {
        $this->assertSame($expected, KunenaParser::removeBlocks($text, ['confidential', 'hide']));
    }

    public function testOnlyTheNamedTags(): void
    {
        $this->assertSame('a [hide]b[/hide] ', KunenaParser::removeBlocks('a [hide]b[/hide] [confidential]c[/confidential]', ['confidential']));
    }
}
