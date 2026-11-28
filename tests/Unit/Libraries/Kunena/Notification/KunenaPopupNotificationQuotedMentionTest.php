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

namespace Kunena\Tests\Unit\Libraries\Kunena\Notification;

use Kunena\Forum\Libraries\Forum\Message\KunenaMessage;
use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * A mention inside a quote is no mention for the popups, as for the e-mails of the mentions (it is the mention of the quoted post):
 * every reply which quoted a post with the name of a member gave him "X mentioned you", also in a topic he does not follow. Such a
 * message is shown for another reason only (his topic, a topic or a category he follows), or not at all.
 */
class KunenaPopupNotificationQuotedMentionTest extends UnitTestCase
{
    public function testAMentionInAQuoteIsNotTheMentionOfTheReply(): void
    {
        $this->requireJoomla();

        $reply = '[quote="Bob"]Thanks [mention userid=7]Anna[/mention][/quote] I agree. [mention userid=9]Carl[/mention]';

        $this->assertSame([9], KunenaMessage::getMentionedUserIds(KunenaMessage::withoutQuotes($reply)));
    }

    public function testThePopupsDropAMentionInAQuote(): void
    {
        $source = Files::read('src/libraries/kunena/src/Notification/KunenaPopupNotification.php');

        $this->assertStringContainsString('KunenaMessage::getMentionedUserIds(KunenaMessage::withoutQuotes((string) $message->message))', $source);
        $this->assertStringContainsString(" AS ' . \$db->quoteName('catsub')", $source);
        $this->assertStringContainsString('if (!$row->own && !($row->topicsub > 0) && !(!$row->parent && (int) ($row->catsub ?? 0) === 1)) {', $source);
    }
}
