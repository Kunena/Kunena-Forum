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

namespace Kunena\Tests\Unit\Libraries\Kunena\Attachment;

use Kunena\Forum\Libraries\Attachment\KunenaAttachmentHelper;
use Kunena\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * The name under which an uploaded file is stored (KunenaAttachmentHelper::getAvailableFilename): a safe name with only the
 * last dot before the extension, a new name when the file is there already, and a random name for protected files. The
 * folder is an empty folder of the test under the root of Joomla, removed again.
 */
class KunenaAttachmentFilenameTest extends UnitTestCase
{
    private string $folder = '';

    protected function setUp(): void
    {
        parent::setUp();
        $this->requireJoomla();

        $this->folder = 'tmp/kunena-phpunit-' . bin2hex(random_bytes(4));
        mkdir(JPATH_ROOT . '/' . $this->folder, 0755, true);
    }

    protected function tearDown(): void
    {
        if ($this->folder !== '') {
            array_map('unlink', glob(JPATH_ROOT . '/' . $this->folder . '/*') ?: []);
            rmdir(JPATH_ROOT . '/' . $this->folder);
        }

        parent::tearDown();
    }

    public static function names(): array
    {
        return [
            'a plain name'                    => ['photo', 'jpg', 'photo.jpg'],
            'spaces go'                       => ['my holiday photo', 'jpg', 'myholidayphoto.jpg'],
            'only the last dot: a script'     => ['shell.php', 'txt', 'shell_php.txt'],
            'dots around the extension go'    => ['photo', '.jpg.', 'photo.jpg'],
            'a path is no part of the name'   => ['../../evil', 'txt', 'evil.txt'],
        ];
    }

    #[DataProvider('names')]
    public function testSafeName(string $basename, string $extension, string $expected): void
    {
        $this->assertSame($expected, KunenaAttachmentHelper::getAvailableFilename($this->folder, $basename, $extension, false));
    }

    public function testAnEmptyNameGetsARandomOne(): void
    {
        $this->assertMatchesRegularExpression('/^file_[0-9a-f]{8}\.txt$/', KunenaAttachmentHelper::getAvailableFilename($this->folder, '', 'txt', false));
    }

    public function testALongNameIsCut(): void
    {
        $name = KunenaAttachmentHelper::getAvailableFilename($this->folder, str_repeat('a', 300), 'txt', false);

        $this->assertSame(str_repeat('a', 100) . '.txt', $name);
    }

    public function testAnExistingFileGetsANewName(): void
    {
        $date = date('Y-m-d');
        touch(JPATH_ROOT . "/{$this->folder}/photo.jpg");
        $this->assertSame("photo_{$date}.jpg", KunenaAttachmentHelper::getAvailableFilename($this->folder, 'photo', 'jpg', false));

        touch(JPATH_ROOT . "/{$this->folder}/photo_{$date}.jpg");
        $this->assertSame("photo_{$date}-2.jpg", KunenaAttachmentHelper::getAvailableFilename($this->folder, 'photo', 'jpg', false));
    }

    public function testAProtectedFileGetsARandomNameWithoutExtension(): void
    {
        $first  = KunenaAttachmentHelper::getAvailableFilename($this->folder, 'photo', 'jpg', true);
        $second = KunenaAttachmentHelper::getAvailableFilename($this->folder, 'photo', 'jpg', true);

        $this->assertMatchesRegularExpression('/^[0-9a-f]{32}$/', $first);
        $this->assertNotSame($first, $second);
    }
}
