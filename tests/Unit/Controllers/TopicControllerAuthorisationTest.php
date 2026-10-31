<?php

/**
 * Kunena Component
 *
 * Regression test: attachment and poll tasks of the topic controller must authorise against the stored data,
 * never against what the request says.
 *
 * @package         Kunena.Tests
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 **/

namespace Kunena\Tests\Unit\Controllers;

use PHPUnit\Framework\TestCase;

class TopicControllerAuthorisationTest extends TestCase
{
    private const CONTROLLER = __DIR__ . '/../../../src/site/src/Controllers/TopicController.php';

    private static string $source;

    public static function setUpBeforeClass(): void
    {
        self::$source = file_get_contents(self::CONTROLLER);
    }

    /**
     * The source of one method, from its signature to the next method (docblock or function).
     */
    private function method(string $name): string
    {
        $found = preg_match(
            '/function ' . preg_quote($name, '/') . '\([^)]*\)[^{]*\{(.*?)(?=\n    (?:\/\*\*|public function|protected function|private function))/s',
            self::$source,
            $m
        );

        $this->assertSame(1, $found, "Method $name not found in the topic controller.");

        return $m[1];
    }

    public function testRemoveAttachmentsDoesNotTrustRequestUserId(): void
    {
        $body = $this->method('removeattachments');

        $this->assertStringNotContainsString("getInt('userid'", $body, 'The owner must not come from the request.');
        $this->assertStringNotContainsString('$this->me->userid == $userid', $body);
        $this->assertStringContainsString('canManageAttachment($instance->userid)', $body);
    }

    public function testRemoveAttachmentsRejectsMissingAttachment(): void
    {
        $this->assertStringContainsString('!$instance->exists()', $this->method('removeattachments'));
    }

    public function testGuestsCanNeverManageAttachments(): void
    {
        $body = $this->method('canManageAttachment');

        // A guest has user id 0, which must not match an attachment with owner 0.
        $this->assertMatchesRegularExpression('/\$this->me->exists\(\)\s*&&/', $body);
        $this->assertStringContainsString('isAdmin()', $body);
        $this->assertStringContainsString('isModerator()', $body);
    }

    public function testCheckPermissionsUsesTheSharedRule(): void
    {
        $this->assertStringContainsString('canManageAttachment(', $this->method('checkpermissions'));
    }

    public function testSetInlineChecksEveryAttachment(): void
    {
        $body = $this->method('setinline');

        $this->assertMatchesRegularExpression('/foreach\s*\(\$instances as \$instance\)\s*\{\s*\$this->checkpermissions\(\$instance->userid\);/s', $body);

        // The check has to run before anything is changed.
        $this->assertLessThan(strpos($body, 'changeinline('), strpos($body, 'checkpermissions('));
    }

    public function testResetVotesNeedsCategoryModerator(): void
    {
        $body = $this->method('resetvotes');

        $this->assertStringContainsString('isModerator($topic->getCategory())', $body);
        $this->assertLessThan(strpos($body, '$topic->resetvotes()'), strpos($body, 'isModerator('), 'Authorise before resetting.');
    }
}
