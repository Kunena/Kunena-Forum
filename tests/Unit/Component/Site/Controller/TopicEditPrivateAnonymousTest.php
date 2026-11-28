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
 * The private part of a message is found by its message, also when the message was made anonymous: it was looked up by the user
 * of the message, which an edit that makes it anonymous sets to 0, so a second private part was made (the first one stayed, the
 * recipient saw both, and every later edit made another one). The form of the edit finds it the same way. (The task and the form
 * need a whole site: their code is read.)
 */
class TopicEditPrivateAnonymousTest extends UnitTestCase
{
    private static function method(string $file, string $signature): string
    {
        $source = str_replace("\r\n", "\n", Files::read($file));
        $start  = strpos($source, $signature);

        return $start === false ? '' : substr($source, $start, (int) strpos($source, "\n    }\n", $start) - $start);
    }

    public function testThePrivatePartIsFoundByItsMessage(): void
    {
        $save = self::method('src/site/src/Controllers/TopicController.php', '    protected function editPrivate(KunenaMessage $message)');
        $form = self::method('src/site/src/Controller/Topic/Form/Edit/TopicFormEditDisplay.php', '    protected function before()');

        foreach (['save' => $save, 'form' => $form] as $what => $body) {
            $this->assertNotSame('', $body, $what);
            $this->assertStringContainsString('->filterByMessage(', $body, $what);
            $this->assertStringNotContainsString("->where('author_id', '=',", $body, $what);
        }
    }
}
