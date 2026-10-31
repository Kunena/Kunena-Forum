<?php

namespace Kunena\Forum\Tests\Unit\Forum\Message;

use PHPUnit\Framework\TestCase;

/**
 * Regression: onDeleteKunenaPost must be built with a string context and an object subject
 * (Joomla DeleteEvent), not a nested list of ids which raised a TypeError in onSetContext().
 */
class KunenaMessageDeleteEventTest extends TestCase
{
    public function testDeleteEventUsesNamedArguments(): void
    {
        $source = file_get_contents(__DIR__ . '/../../../../src/libraries/kunena/src/Forum/Message/KunenaMessage.php');

        $this->assertStringNotContainsString("'onDeleteKunenaPost', [[", $source);
        $this->assertMatchesRegularExpression(
            "/new KunenaDeleteEvent\('onDeleteKunenaPost', \[\s*'context' => 'com_kunena\.message',\s*'subject' => \\\$this,\s*'pks'\s*=> \[\\\$this->id\],/",
            $source
        );
    }
}
