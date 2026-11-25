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

use Kunena\Forum\Libraries\Tools\KunenaMailPreview as P;
use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The tool "E-mail templates" previews the templates which Kunena sends, their tags filled with samples.
 */
class KunenaMailPreviewTest extends UnitTestCase
{
    public function testTheTagsAreFilled(): void
    {
        $values = P::samples('My site', 'My forum', 'https://example.org/');

        $this->assertSame('New post on My forum by jane.doe', P::fill('New post on {BOARDTITLE} by {author}', $values, []));
        $this->assertSame('Hello [name]', P::fill('Hello {NAME}', $values, ['name']));
        $this->assertStringStartsWith('https://example.org/index.php', $values['url']);
    }

    /**
     * Each template of the preview is one which Kunena sends, and is created by the install.
     */
    public function testTheTemplatesAreThoseOfKunena(): void
    {
        $code = Files::read('src/libraries/kunena/src/Forum/Message/KunenaMessage.php') . Files::read('src/site/src/Controllers/TopicController.php');
        $sql  = Files::read('src/admin/sql/updates/mysql/8.0.0-DEV.sql') . Files::read('src/admin/sql/install/mysql/create_tables.utf8.sql');

        foreach (array_keys(P::TEMPLATES) as $id) {
            $name = substr($id, \strlen('com_kunena.'));

            $this->assertMatchesRegularExpression("/'{$id}'|'com_kunena\\.' \\. \\\$|{$name}/", $code, $id);
            $this->assertStringContainsString("'{$id}'", $sql, $id);
        }
    }

    /**
     * The preview and the test mail have the values of a real notification: in the HTML the link to unsubscribe is a link
     * which can be clicked, in the text the address on its own line; the text tags are escaped by Joomla.
     */
    public function testTheLinkToUnsubscribeIsALink(): void
    {
        $samples                = P::samples('My site', 'My forum', 'https://example.org/');
        $samples['unsubscribe'] = 'https://example.org/index.php?option=com_kunena&view=topic&layout=unsubscribe&type=topic&id=1&userid=2&hash=abc';
        $values                 = P::mailValues($samples, 'Stop the e-mails:', 'Unsubscribe', 'Message');

        $this->assertStringContainsString(
            '<a href="https://example.org/index.php?option=com_kunena&amp;view=topic&amp;layout=unsubscribe&amp;type=topic&amp;id=1&amp;userid=2&amp;hash=abc"',
            $values['html']['unsubscribe']
        );
        $this->assertStringContainsString('>Unsubscribe</a>', $values['html']['unsubscribe']);
        $this->assertSame("Stop the e-mails:\n" . $samples['unsubscribe'], $values['plain']['unsubscribe']);

        // The message keeps its lines in the HTML; the text tags are left to the escaping of Joomla.
        $this->assertStringContainsString('Thank you for the welcome!<br>', $values['html']['message']);
        $this->assertContains('subject', $values['unsafe']);
        $this->assertNotContains('unsubscribe', $values['unsafe']);
    }

    public function testNoLinkToUnsubscribeWithoutAnAddress(): void
    {
        $values = P::mailValues(['unsubscribe' => ''] + P::samples('a', 'b', 'https://c.example/'), 'Stop:', 'Unsubscribe', 'Message');

        $this->assertSame('', $values['html']['unsubscribe']);
        $this->assertSame('', $values['plain']['unsubscribe']);
    }

    /**
     * An edited text with a tag which the template does not know gets a warning: the tag would be sent as it is written.
     */
    public function testTheTagsWhichTheTemplateDoesNotKnow(): void
    {
        $tags = ['sitename', 'author', 'url'];

        $this->assertSame([], P::unknownTags('{SITENAME}: {author} wrote {URL}', $tags));
        $this->assertSame(['AUTOR', 'LINK'], P::unknownTags('{AUTOR} wrote {LINK} {autor}', $tags));
        // Braces which are no tag (a space, a sign) are left alone.
        $this->assertSame([], P::unknownTags('a {b c} {} {x-y}', $tags));
    }
}
