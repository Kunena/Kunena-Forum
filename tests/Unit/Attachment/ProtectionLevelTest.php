<?php

/**
 * Kunena Component
 *
 * Regression test for KunenaAttachment::tryAuthorise(). The protection level is a bitmask, but the checks were written as
 * "$this->protected && self::PROTECTION_PRIVATE": a constant is always true, so every check ran for every protected attachment
 * (the check of the message could open an attachment of a private message; the author was let in whatever the level was).
 * And the exception of a check which failed was created and thrown away, which let the user in.
 *
 * @package         Kunena.Tests
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 **/

namespace Kunena\Tests\Unit\Attachment;

use Kunena\Forum\Libraries\Attachment\KunenaAttachmentProtection as P;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ProtectionLevelTest extends TestCase
{
    private const ATTACHMENT = __DIR__ . '/../../../src/libraries/kunena/src/Attachment/KunenaAttachment.php';

    public static function setUpBeforeClass(): void
    {
        if (!\defined('_JEXEC')) {
            \define('_JEXEC', 1);
        }

        require_once __DIR__ . '/../../../src/libraries/kunena/src/Attachment/KunenaAttachmentProtection.php';
    }

    public static function levels(): array
    {
        //                   level  message  private  author
        return [
            'none'             => [0,   false, false, false],
            'public'           => [1,   true,  false, true],
            'acl'              => [2,   true,  false, true],
            'friends'          => [4,   false, false, true],
            'private'          => [32,  false, true,  true],
            'private and acl'  => [34,  false, true,  true],
            'author only'      => [64,  false, false, true],
            'unpublished'      => [128, false, false, false],
        ];
    }

    #[DataProvider('levels')]
    public function testChecksOfTheLevel(int $level, bool $message, bool $private, bool $author): void
    {
        $this->assertSame($message, P::usesMessageAccess($level), 'message access');
        $this->assertSame($private, P::isPrivate($level), 'private');
        $this->assertSame($author, P::authorMaySee($level), 'author');
    }

    public function testAMessageNeverOpensAPrivateAttachment(): void
    {
        $this->assertFalse(P::usesMessageAccess(P::PRIVATE));
        $this->assertFalse(P::usesMessageAccess(P::PRIVATE | P::ACL));
    }

    public function testConstantsAreTheOnesOfKunenaAttachment(): void
    {
        $source = file_get_contents(self::ATTACHMENT);

        foreach (['PUBLIC' => P::PUBLIC, 'ACL' => P::ACL, 'PRIVATE' => P::PRIVATE, 'AUTHOR' => P::AUTHOR, 'UNPUBLISHED' => P::UNPUBLISHED] as $name => $value) {
            $this->assertSame(1, preg_match('/const PROTECTION_' . $name . ' = (\d+);/', $source, $m), $name);
            $this->assertSame($value, (int) $m[1], $name);
        }
    }

    private function body(): string
    {
        $source = file_get_contents(self::ATTACHMENT);

        $this->assertSame(1, preg_match('/public function tryAuthorise\([^)]*\)\s*\{(.*?)\n    \}\n/s', $source, $m));

        return $m[1];
    }

    public function testNoCheckTestsAConstantInsteadOfTheLevel(): void
    {
        $body = $this->body();

        $this->assertDoesNotMatchRegularExpression('/&&\s*\(?\s*self::PROTECTION_/', $body, 'A constant is always true.');
        $this->assertDoesNotMatchRegularExpression('/PROTECTION_\w+\s*\+\s*self::PROTECTION_/', $body);
        $this->assertStringContainsString('KunenaAttachmentProtection::usesMessageAccess(', $body);
        $this->assertStringContainsString('KunenaAttachmentProtection::isPrivate(', $body);
        $this->assertStringContainsString('KunenaAttachmentProtection::authorMaySee(', $body);
    }

    public function testFailingCheckIsARefusal(): void
    {
        $body = $this->body();

        $this->assertSame(1, preg_match('/catch \(Exception \$e\) \{(.*?)\n\s{16}\}/s', $body, $m));
        $this->assertMatchesRegularExpression('/\$exception\s*=\s*new KunenaExceptionAuthorise\(/', $m[1], 'The exception has to be kept.');
        $this->assertStringContainsString('break;', $m[1]);
    }
}
