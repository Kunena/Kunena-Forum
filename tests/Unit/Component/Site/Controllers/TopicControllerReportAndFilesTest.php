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

namespace Kunena\Tests\Unit\Component\Site\Controllers;

use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * Two tasks of the topic controller which asked too little (they need a session and a request: their code is read):
 * - report: a banned user may read, not write, but his report still made a topic or an e-mail for every moderator;
 * - removeattachments: the file of a message was removed with the rule of the attachment alone (its uploader), so the author took
 *   the files out of a post which he may edit no more (after the edit time, in a locked topic, in read only mode).
 */
class TopicControllerReportAndFilesTest extends UnitTestCase
{
    private function task(string $name): string
    {
        $source = str_replace("\r\n", "\n", Files::read('src/site/src/Controllers/TopicController.php'));

        $this->assertSame(1, preg_match('/\n    public function ' . $name . '\(\)\s*\{(.*?)\n    \}\n/s', $source, $m), $name);

        return $m[1];
    }

    public function testABannedUserCannotReport(): void
    {
        $body   = $this->task('report');
        $banned = strpos($body, 'if (!$this->me->exists() || $this->me->isBanned() || $this->config->reportMsg == 0) {');

        $this->assertIsInt($banned);
        $this->assertLessThan(strpos($body, "\$target->tryAuthorise('read');"), $banned, 'Before anything of the report is made.');
    }

    public function testTheFileOfAMessageIsRemovedByWhoMayEditTheMessage(): void
    {
        $body = $this->task('removeattachments');

        $this->assertStringContainsString('$message  = (int) $instance->mesid ? $instance->getMessage() : null;', $body);
        $this->assertStringContainsString("\$editable = !\$message || !\$message->exists() || \$message->isAuthorised('edit');", $body);
        $this->assertStringContainsString("if (\$editable && \$instance->isAuthorised('delete')) {", $body);
        $this->assertSame(1, substr_count($body, '$instance->delete()'), 'One place deletes the file.');
    }
}
