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
 * The notification of JomSocial of a reply (and its e-mail) has the text without its [confidential], [hide] and [private] parts: the
 * text is parsed as for an e-mail (a context, for which the parser leaves them out). It was parsed for the page, as the poster (or the
 * moderator who approved) sees it, and every subscriber of the topic got those parts. (The plugin needs JomSocial: its code is read.)
 */
class KunenaActivityCommunityNotificationHiddenTest extends UnitTestCase
{
    public function testTheTextOfTheNotificationIsParsedAsForAnEmail(): void
    {
        $plugin = Files::read('src/plugins/plg_kunena_community/src/Helper/KunenaActivityCommunity.php');

        $this->assertStringContainsString("\$params->set('message', \$message->displayField('message', true, 'email'));", $plugin);

        // The parser leaves the hidden parts out for a context.
        $bbcode = str_replace("\r\n", "\n", Files::read('src/libraries/kunena/src/BBCode/KunenaBBCode.php'));

        foreach (['DoConfidential', 'DoHide', 'DoPrivate'] as $handler) {
            $this->assertSame(1, preg_match('/public function ' . $handler . '\(.*?\n    \}\n/s', $bbcode, $m), $handler);
            $this->assertMatchesRegularExpression("/if \(!empty\(\\\$bbcode->context\)\) \{\n\s*return '';/", $m[0], $handler);
        }
    }
}
