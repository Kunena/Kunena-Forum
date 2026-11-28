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

namespace Kunena\Tests\Unit\Component\Site\Controller;

use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The fields of a form which is refused come back in that form, and only in it (one place of the session keeps them):
 * - an edit refused by a rule of the text (a link in the subject, too many links) went back to the form with the saved text: what
 *   the user typed was lost (the fields were kept only for two other refusals);
 * - the form of an edit took the fields of the last refused form, a reply (a quick reply refused by the flood protection) or the edit
 *   of another message, in place of the text of its message, and its save wrote them over that message.
 * (The tasks and the forms need a whole site: their code is read.)
 */
class TopicEditKeepsFieldsTest extends UnitTestCase
{
    private static function method(string $file, string $signature): string
    {
        $source = str_replace("\r\n", "\n", Files::read($file));
        $start  = strpos($source, $signature);

        return $start === false ? '' : substr($source, $start, (int) strpos($source, "\n    }\n", $start) - $start);
    }

    public function testARefusedEditKeepsWhatWasTyped(): void
    {
        $edit = self::method('src/site/src/Controllers/TopicController.php', '    public function edit()');
        $this->assertNotSame('', $edit);

        $kept = strpos($edit, "\$this->app->setUserState('com_kunena.postfields', \$fields);");
        $this->assertNotFalse($kept);

        // Before every refusal of the text, after the check of the token.
        foreach (['COM_KUNENA_TOPIC_MESSAGES_ERROR_URL_IN_SUBJECT', 'COM_KUNENA_TOPIC_SPAM_LINK_PROTECTION'] as $refusal) {
            $this->assertLessThan(strpos($edit, $refusal), $kept, $refusal);
        }

        $this->assertLessThan($kept, strpos($edit, "Session::checkToken('post')"));

        // And forgotten once the message is saved.
        $this->assertGreaterThan(strpos($edit, '$message->save();'), strrpos($edit, "\$this->app->setUserState('com_kunena.postfields', null);"));
    }

    public function testTheFormOfAnEditTakesTheFieldsOfItsMessageOnly(): void
    {
        $edit = self::method('src/site/src/Controllers/TopicController.php', '    public function edit()');
        $this->assertStringContainsString("'mesid'             => \$this->id,", $edit);

        $form = self::method('src/site/src/Controller/Topic/Form/Edit/TopicFormEditDisplay.php', '    protected function before()');
        $this->assertMatchesRegularExpression("/if \(\\\$saved && \(int\) \(\\\$saved\['mesid'\] \?\? 0\) !== \\\$mesid\) \{\n\s*\\\$saved = null;/", $form);
        $this->assertLessThan(strpos($form, '$this->message->edit($saved);'), strpos($form, "\$saved['mesid']"));

        // A new topic and a reply do not take the fields of an edit.
        foreach (['Create/TopicFormCreateDisplay.php', 'Reply/TopicFormReplyDisplay.php'] as $file) {
            $this->assertStringContainsString("if (!empty(\$saved['mesid'])) {", Files::read('src/site/src/Controller/Topic/Form/' . $file), $file);
        }

        // A draft of an edit names its message.
        $this->assertStringContainsString("['mesid' => (int) \$draft->message_id] + (array) \$draft->fields", Files::read('src/site/src/Controllers/TopicController.php'));
    }
}
