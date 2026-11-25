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

use Kunena\Forum\Libraries\Tools\KunenaTopicEditor as E;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The edit of a topic in the administration saves only the messages which the form changes, of this topic, with a subject
 * and a text.
 */
class KunenaTopicEditorTest extends UnitTestCase
{
    private const SAVED = [
        10 => ['subject' => 'Welcome', 'message' => "Hello\nworld"],
        11 => ['subject' => 'Re: Welcome', 'message' => 'Thanks'],
    ];

    public function testAnUnchangedFormChangesNothing(): void
    {
        // A browser sends the line ends as CRLF.
        $posted = [10 => ['subject' => 'Welcome', 'message' => "Hello\r\nworld"], 11 => ['subject' => ' Re: Welcome ', 'message' => 'Thanks']];

        $this->assertSame(['changes' => [], 'empty' => [], 'unknown' => []], E::changes(self::SAVED, $posted));
    }

    public function testOnlyTheChangedMessages(): void
    {
        $posted = [10 => ['subject' => 'Welcome', 'message' => "Hello\r\nworld!"], 11 => ['subject' => 'Re: Welcome', 'message' => 'Thanks']];

        $this->assertSame([10 => ['subject' => 'Welcome', 'message' => "Hello\nworld!"]], E::changes(self::SAVED, $posted)['changes']);
    }

    public function testAMessageWithoutSubjectOrTextIsNotSaved(): void
    {
        $result = E::changes(self::SAVED, [10 => ['subject' => '  ', 'message' => 'x'], 11 => ['subject' => 'Re', 'message' => " \r\n "]]);

        $this->assertSame([], $result['changes']);
        $this->assertSame([10, 11], $result['empty']);
    }

    public function testAMessageOfAnotherTopicIsRefused(): void
    {
        $result = E::changes(self::SAVED, [99 => ['subject' => 'Spam', 'message' => 'Spam']]);

        $this->assertSame([], $result['changes']);
        $this->assertSame([99], $result['unknown']);
    }

    public function testTheSubjectIsOneLineNotLongerThanTheColumn(): void
    {
        $this->assertSame('One two', E::subject("  One\r\ntwo "));
        $this->assertSame(E::SUBJECT_LENGTH, mb_strlen(E::subject(str_repeat('é', 300))));
    }
}
