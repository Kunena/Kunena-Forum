<?php

/**
 * Kunena Component
 *
 * Regression test for the text of the editor which is handed from the quick reply to the full editor. It was kept in
 * localStorage under one key for the whole site (copyKunenaeditor), without an end and without an owner: on a shared computer
 * the next member who opened an editor got the text of the previous member, also after a logout.
 *
 * assets/js/draft.js is run with node against in-memory storages.
 *
 * @package         Kunena.Tests
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 **/

namespace Kunena\Tests\Unit\Js;

use PHPUnit\Framework\TestCase;

class EditorDraftStorageTest extends TestCase
{
    private const ROOT    = __DIR__ . '/../../../src';
    private const DRAFT   = self::ROOT . '/site/template/aurelia/assets/js/draft.js';
    private const HARNESS = __DIR__ . '/harness/draft.js';

    private function run_steps(array $steps): array
    {
        $out = [];
        $rc  = 1;
        @exec('node --version 2>&1', $out, $rc);

        if ($rc !== 0) {
            $this->markTestSkipped('node is not available.');
        }

        $proc = proc_open(
            ['node', self::HARNESS, self::DRAFT],
            [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']],
            $pipes
        );
        $this->assertIsResource($proc);

        fwrite($pipes[0], json_encode($steps));
        fclose($pipes[0]);
        $stdout = stream_get_contents($pipes[1]);
        $stderr = stream_get_contents($pipes[2]);
        proc_close($proc);

        $record = json_decode($stdout, true);
        $this->assertIsArray($record, 'The harness failed: ' . $stderr);

        return $record;
    }

    public function testMemberGetsTheirOwnTextBackOnce(): void
    {
        $r = $this->run_steps([
            ['as' => 5, 'do' => 'save', 'text' => 'my reply'],
            ['as' => 5, 'do' => 'take'],
            ['as' => 5, 'do' => 'take'],
        ]);

        $this->assertSame('my reply', $r['results'][1]);
        $this->assertNull($r['results'][2], 'The text is removed when it is taken.');
        $this->assertSame([], $r['localKeys']);
    }

    public function testNextMemberOnTheSameBrowserDoesNotGetTheText(): void
    {
        $r = $this->run_steps([
            ['as' => 5, 'do' => 'save', 'text' => 'private words of member 5'],
            ['as' => 6, 'do' => 'take'],
            ['as' => 0, 'do' => 'take'],
            ['as' => 5, 'do' => 'take'],
        ]);

        $this->assertNull($r['results'][1], 'Another member must not get it.');
        $this->assertNull($r['results'][2], 'A guest must not get it.');
        $this->assertSame('private words of member 5', $r['results'][3], 'The writer still can.');
    }

    public function testTextLeftByAGuestIsNotHandedToAMember(): void
    {
        $r = $this->run_steps([
            ['as' => 0, 'do' => 'save', 'text' => 'guest words'],
            ['as' => 7, 'do' => 'take'],
        ]);

        $this->assertNull($r['results'][1]);
        $this->assertSame([], $r['localKeys'], 'The text of a guest never reaches localStorage.');
        $this->assertCount(1, $r['sessionKeys']);
    }

    public function testTextExpires(): void
    {
        $r = $this->run_steps([
            ['as' => 5, 'do' => 'save', 'text' => 'old'],
            ['as' => 5, 'do' => 'take', 'advance' => 16 * 60 * 1000],
        ]);

        $this->assertNull($r['results'][1]);
        $this->assertSame([], $r['localKeys']);
    }

    public function testKeyOfTheOldVersionIsNeverHandedOutAndIsRemoved(): void
    {
        $r = $this->run_steps([
            ['legacy' => 'text of somebody, from the old version'],
            ['as' => 5, 'do' => 'take'],
        ]);

        $this->assertNull($r['results'][1]);
        $this->assertSame([], $r['localKeys'], 'The global key is removed.');
    }

    public function testOldEntriesOfOtherMembersArePurged(): void
    {
        $r = $this->run_steps([
            ['as' => 5, 'do' => 'save', 'text' => 'left behind'],
            ['as' => 6, 'do' => 'save', 'text' => 'new', 'advance' => 16 * 60 * 1000],
        ]);

        $this->assertSame(['copyKunenaeditor:6'], $r['localKeys']);
    }

    public function testSavingNothingRemovesTheEntry(): void
    {
        $r = $this->run_steps([
            ['as' => 5, 'do' => 'save', 'text' => 'x'],
            ['as' => 5, 'do' => 'save', 'text' => ''],
        ]);

        $this->assertSame([], $r['localKeys']);
    }

    public function testNoScriptStillUsesTheGlobalKey(): void
    {
        $found = [];
        $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(self::ROOT, \FilesystemIterator::SKIP_DOTS));

        foreach ($files as $file) {
            $path = str_replace('\\', '/', $file->getPathname());

            if (!\in_array($file->getExtension(), ['js', 'php'], true) || str_ends_with($path, '/assets/js/draft.js')) {
                continue;
            }

            if (preg_match('/localStorage\.\w+\(\s*[\'"]copyKunenaeditor[\'"]/', file_get_contents($path))) {
                $found[] = $path;
            }
        }

        $this->assertSame([], $found, 'Use KunenaDraft, not the global key.');
    }

    public function testTemplateLoadsTheHelperAndTellsWhoTheOwnerIs(): void
    {
        $source = file_get_contents(self::ROOT . '/site/template/aurelia/template.php');

        $this->assertMatchesRegularExpression('/addScriptOptions\(\'com_kunena\.draftowner\',\s*\(int\)\s*KunenaUserHelper::getMyself\(\)->userid\)/', $source);
        $this->assertLessThan(
            strpos($source, "assets/js/main.js"),
            strpos($source, "assets/js/draft.js"),
            'draft.js is loaded before the scripts which use it.'
        );
    }
}
