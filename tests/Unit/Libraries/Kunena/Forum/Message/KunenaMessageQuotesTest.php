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

use Kunena\Forum\Libraries\Forum\Message\KunenaMessageQuotes;
use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Multi-quote: the ids which the reply form takes from the request, the [quote] block of a message (the same for Quote and
 * Multi-quote), and where the reply form and the buttons use them.
 */
class KunenaMessageQuotesTest extends UnitTestCase
{
    public static function ids(): array
    {
        return [
            'a list'                     => ['12,15,18', [12, 15, 18]],
            'with spaces'                => [' 12 , 15 ', [12, 15]],
            'the duplicates once'        => ['12,15,12', [12, 15]],
            'not numbers are left out'   => ['12,abc,-3,1.5,,0,15', [12, 15]],
            'one id as a number'         => [7, [7]],
            'empty'                      => ['', []],
            'an array is not a list'     => [['12'], []],
            'nothing'                    => [null, []],
        ];
    }

    #[DataProvider('ids')]
    public function testTheIdsOfTheRequest(mixed $value, array $ids): void
    {
        $this->assertSame($ids, KunenaMessageQuotes::parseIds($value));
    }

    public function testAtMostTenMessagesAreQuoted(): void
    {
        $this->assertSame(range(1, KunenaMessageQuotes::MAX_QUOTES), KunenaMessageQuotes::parseIds(implode(',', range(1, 15))));
    }

    public function testTheBlockOfAMessage(): void
    {
        $this->assertSame(
            '[quote="Alice post=12 userid=7"]Hello[/quote]',
            KunenaMessageQuotes::block(12, 7, 'Alice', 'Hello')
        );
    }

    public function testAMessageWithoutAuthorNameIsQuotedAsAnonymous(): void
    {
        $this->assertSame('[quote="anonymous post=12 userid=0"]Hi[/quote]', KunenaMessageQuotes::block(12, 0, '  ', 'Hi'));
    }

    public function testANameCannotBreakTheTag(): void
    {
        // The quote marks and the brackets go: what remains is only text in the tag.
        $this->assertSame('[quote="Bob x/quote post=3 userid=4"]t[/quote]', KunenaMessageQuotes::block(3, 4, 'Bob" x[/quote]', 't'));
    }

    public function testTheConfidentialAndPrivatePartsAreNotQuoted(): void
    {
        $block = KunenaMessageQuotes::block(3, 4, 'Bob', 'Seen [confidential]secret[/confidential] and [private]hidden[/private] end');

        $this->assertStringNotContainsString('secret', $block);
        $this->assertStringNotContainsString('hidden', $block);
        $this->assertStringContainsString('Seen ', $block);
        $this->assertStringContainsString(' end', $block);
    }

    public function testTheQuoteButtonUsesTheSameBlock(): void
    {
        $source = Files::read('src/libraries/kunena/src/Forum/Message/KunenaMessage.php');

        $this->assertSame(1, preg_match('/public function newReply\(.*?\): array\s*\{(.*?)\n    \}\n/s', $source, $m));
        $this->assertStringContainsString('KunenaMessageQuotes::block(', $m[1]);
    }

    public function testTheReplyFormQuotesTheChosenMessages(): void
    {
        $source = Files::read('src/site/src/Controller/Topic/Form/Reply/TopicFormReplyDisplay.php');

        $this->assertStringContainsString("KunenaMessageQuotes::parseIds(\$this->input->getString('quotes', ''))", $source);
        $this->assertStringContainsString('KunenaMessageQuotes::build(', $source);
    }

    public function testTheButtonsAndTheBarAreOnlyForWhoMayReply(): void
    {
        $topic   = Files::read('src/site/template/aurelia/layouts/topic/item/default.php');
        $actions = Files::read('src/site/template/aurelia/layouts/message/item/actions/default.php');

        $this->assertMatchesRegularExpression("/if \(\\\$this->topic->isAuthorised\('reply'\)\) \{\s*\\\$this->addScript\('assets\/js\/multiquote\.js'\);/", $topic);
        $this->assertStringContainsString("if (\$this->messageButtons->get('quote')) {", $actions);
        $this->assertFileExists(Files::path('src/site/template/aurelia/assets/js/multiquote.js'));
    }
}
