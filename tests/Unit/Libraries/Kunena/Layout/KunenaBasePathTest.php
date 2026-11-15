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

namespace Kunena\Tests\Unit\Libraries\Kunena\Layout;

use Kunena\Forum\Libraries\Layout\KunenaBase;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The file of a layout (Kunena 8.0): a page renders the same layouts many times (a row and its pagination for every topic), and
 * looking for the file in every path of the templates cost about a quarter of a page. It is looked for once in a request.
 */
class KunenaBasePathTest extends UnitTestCase
{
    private string $folder = '';

    protected function setUp(): void
    {
        $this->requireJoomla();

        $this->folder = sys_get_temp_dir() . '/kunena-layout-' . bin2hex(random_bytes(4));
        mkdir($this->folder . '/first', 0777, true);
        mkdir($this->folder . '/second', 0777, true);
    }

    protected function tearDown(): void
    {
        foreach (glob($this->folder . '/*/*.php') ?: [] as $file) {
            unlink($file);
        }

        @rmdir($this->folder . '/first');
        @rmdir($this->folder . '/second');
        @rmdir($this->folder);
    }

    private function layout(array $paths): KunenaBase
    {
        return self::bare(KunenaBase::class, ['_name' => 'Test/Layout', 'includePaths' => $paths, 'layout' => 'default']);
    }

    private function file(string $path): string
    {
        file_put_contents($this->folder . '/' . $path, '<?php');

        return realpath($this->folder . '/' . $path);
    }

    public function testAFileWhichWasFoundIsNotLookedForAgain(): void
    {
        $file   = $this->file('second/row.php');
        $layout = $this->layout([$this->folder . '/first', $this->folder . '/second']);

        $this->assertSame($file, $layout->getPath('row'));

        // A file of the first path would win when it was looked for again: the answer of the request is kept.
        $this->file('first/row.php');

        $this->assertSame($file, $layout->getPath('row'));
    }

    public function testTheDefaultFileIsTheFallback(): void
    {
        $file = $this->file('first/default.php');

        $this->assertSame($file, $this->layout([$this->folder . '/first'])->getPath('missing'));
    }

    public function testOtherPathsAreLookedForApart(): void
    {
        $first  = $this->file('first/row.php');
        $second = $this->file('second/row.php');

        $this->assertSame($first, $this->layout([$this->folder . '/first'])->getPath('row'));
        $this->assertSame($second, $this->layout([$this->folder . '/second'])->getPath('row'));
        $this->assertSame($second, $this->layout([$this->folder . '/second', $this->folder . '/first'])->getPath('row'));
    }

    public function testALayoutWithoutAFileHasNoPath(): void
    {
        $this->assertFalse($this->layout([$this->folder . '/first'])->getPath('nothing'));
    }
}
