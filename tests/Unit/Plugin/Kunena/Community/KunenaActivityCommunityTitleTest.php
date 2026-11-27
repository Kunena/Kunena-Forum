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

namespace Kunena\Tests\Unit\Plugin\Kunena\Community;

use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The activity of a reply in the stream of JomSocial: its title is HTML (a link to the topic), and the subject of the reply is
 * stored as it was typed. It was put into the title as it was: a subject with a tag ran its script for every reader of the stream.
 * The activity of a new topic escaped it already. (The plugin needs JomSocial: its code is read.)
 */
class KunenaActivityCommunityTitleTest extends UnitTestCase
{
    public function testTheSubjectOfAReplyIsEscapedInTheTitle(): void
    {
        $source = Files::read('src/plugins/plg_kunena_community/src/Helper/KunenaActivityCommunity.php');

        $this->assertStringNotContainsString("\$message->subject . '</a>'", $source);
        $this->assertSame(2, substr_count($source, "\$message->displayField('subject') . '</a>'"), 'The new topic and the reply.');
    }
}
