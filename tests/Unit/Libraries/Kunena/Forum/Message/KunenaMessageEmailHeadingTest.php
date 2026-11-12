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

namespace Kunena\Tests\Unit\Libraries\Kunena\Forum\Message;

use Kunena\Forum\Libraries\Forum\Message\KunenaMessage;
use Kunena\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * The heading of a notification email: the forum name (%1$s) and the author (%2$s) go where the translation puts them.
 */
class KunenaMessageEmailHeadingTest extends UnitTestCase
{
    public static function texts(): array
    {
        return [
            'numbered placeholder' => ['A new reply has been posted on the %1$s', 'A new reply has been posted on the My Forum'],
            'plain placeholder'    => ['A new reply has been posted on the %s', 'A new reply has been posted on the My Forum'],
            'no placeholder'       => ['A new reply has been posted on the', 'A new reply has been posted on the My Forum'],
            'reordered'            => ['%2$s at %1$s posted a new reply', 'Anna at My Forum posted a new reply'],
            'only a literal %'     => ['100% new on the', '100% new on the My Forum'],
            'broken placeholder'   => ['A new reply by %3$s on the', 'A new reply by %3$s on the My Forum'],
        ];
    }

    #[DataProvider('texts')]
    public function testFormatEmailHeading(string $text, string $expected): void
    {
        $this->assertSame($expected, KunenaMessage::formatEmailHeading($text, 'My Forum', 'Anna'));
    }

    public static function fallbacks(): array
    {
        return [
            'no placeholder'     => ['A new reply has been posted on the'],
            'only a literal %'   => ['100% new on the'],
            'broken placeholder' => ['A new reply by %3$s on the'],
        ];
    }

    #[DataProvider('fallbacks')]
    public function testAFallbackHasTheForumNameOnce(string $text): void
    {
        $this->assertSame(1, substr_count(KunenaMessage::formatEmailHeading($text, 'My Forum', 'Anna'), 'My Forum'));
    }
}
