<?php

/**
 * Kunena Component
 *
 * The attachments of a user returned the exception of the missing access from before(), after the attachments were counted.
 * Only false or a thrown exception stops a page, so the page went on and showed an empty list. No page returns an exception
 * any more.
 *
 * @package         Kunena.Tests
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 **/

namespace Kunena\Tests\Unit\Security;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class AttachmentManagerAccessTest extends TestCase
{
    private const SRC = __DIR__ . '/../../../src/site/src';

    /**
     * The body of before() of a display controller.
     */
    private function before(string $file): string
    {
        $source = file_get_contents(self::SRC . '/' . $file);

        $this->assertSame(1, preg_match('/protected function before\(\)[^{]*\{(.*?)\r?\n    \}\r?\n/s', $source, $m), $file);

        return $m[1];
    }

    public function testAttachmentsOfAUserThrowBeforeTheyAreCounted(): void
    {
        $body = $this->before('Controller/User/Attachments/UserAttachmentsDisplay.php');

        $throw = strpos($body, 'throw new KunenaExceptionAuthorise(');

        $this->assertIsInt($throw);
        $this->assertStringContainsString('!$this->config->showImgFilesManageProfile ||', $body);
        $this->assertLessThan(strpos($body, 'new KunenaAttachmentFinder()'), $throw, 'Nothing is read before the check.');
        $this->assertSame(1, substr_count($body, 'showImgFilesManageProfile'), 'One check.');
    }

    public static function folders(): array
    {
        return [['Controller'], ['Controllers'], ['Layout'], ['View']];
    }

    /**
     * An exception that is returned is an exception that is lost: nowhere in the site.
     */
    #[DataProvider('folders')]
    public function testNoExceptionIsReturned(string $folder): void
    {
        $found = [];
        $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(self::SRC . '/' . $folder, \FilesystemIterator::SKIP_DOTS));

        foreach ($files as $file) {
            if (substr((string) $file, -4) !== '.php') {
                continue;
            }

            if (preg_match('/\breturn\s+new\s+\\\\?[\w\\\\]*Exception\w*\s*\(/', file_get_contents((string) $file))) {
                $found[] = str_replace('\\', '/', substr((string) $file, \strlen(self::SRC) + 1));
            }
        }

        $this->assertSame([], $found);
    }

    /**
     * What stops a page: the reason these checks have to throw.
     */
    public function testOnlyFalseOrAThrownExceptionStopsAPage(): void
    {
        $source = file_get_contents(self::SRC . '/../../libraries/kunena/src/Controller/KunenaControllerDisplay.php');

        $this->assertStringContainsString('if ($result === false) {', $source);
        $this->assertStringContainsString('} catch (KunenaExceptionAuthorise $e) {', $source);
    }
}
