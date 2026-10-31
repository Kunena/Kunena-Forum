<?php

/**
 * Kunena Component
 *
 * Regression test: scripts of the template wrote data from the server (a topic subject, file names, addresses) into strings
 * of html, e.g. $('#kmod_topics').append('<option>' + subject + '</option>'). The subject is text typed by a member and is
 * stored as it was typed, so a moderator who opened the dialog to move a topic ran the script of the member.
 *
 * The scripts are run with node against a recording stand-in for jQuery (tests/Unit/Js/harness/dom-sinks.js) and hostile
 * data. They have to set the data as text, value or attribute, never hand it to jQuery as html.
 *
 * @package         Kunena.Tests
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 **/

namespace Kunena\Tests\Unit\Js;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class DomSinksTest extends TestCase
{
    private const SRC     = __DIR__ . '/../../../src';
    private const HARNESS = __DIR__ . '/harness/dom-sinks.js';

    private function node(): string
    {
        $out = [];
        $rc  = 1;
        @exec('node --version 2>&1', $out, $rc);

        if ($rc !== 0) {
            $this->markTestSkipped('node is not available.');
        }

        return 'node';
    }

    /**
     * @return array  The record of the harness.
     */
    private function harness(string $scenario, string $code): array
    {
        $this->node();

        $proc = proc_open(
            ['node', self::HARNESS, $scenario, '--inline'],
            [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']],
            $pipes
        );

        $this->assertIsResource($proc);

        fwrite($pipes[0], $code);
        fclose($pipes[0]);

        $out = stream_get_contents($pipes[1]);
        $err = stream_get_contents($pipes[2]);
        proc_close($proc);

        $record = json_decode($out, true);

        $this->assertIsArray($record, "The harness gave no result: $err");

        return $record;
    }

    private function script(string $file): string
    {
        return file_get_contents(self::SRC . '/' . $file);
    }

    private function assertNoHostileHtml(array $record): void
    {
        $this->assertArrayNotHasKey('error', $record, 'The script did not run: ' . ($record['error'] ?? ''));

        foreach ($record['htmlStrings'] as $html) {
            foreach ($record['sentinels'] as $name => $payload) {
                $this->assertStringNotContainsString($payload, $html, "$name was written into html: $html");
            }

            $this->assertStringNotContainsString('onerror', $html, "An event handler came into html: $html");
            $this->assertStringNotContainsString('onfocus', $html, "An event handler came into html: $html");
        }
    }

    // --- the harness itself has to see the old mistake ---

    public function testHarnessSeesDataWrittenAsHtml(): void
    {
        $bad = "jQuery(document).ready(function (\$) {\n"
            . "  \$('#kmod_categories').change(function () {\n"
            . "    \$.getJSON(kunena_url_ajax, {}).done(function (json) {\n"
            . "      \$.each(json, function (i, object) { \$.each(object, function (k, element) {\n"
            . "        \$('#kmod_topics').append('<option value=\"' + element['id'] + '\">' + element['subject'] + '</option>');\n"
            . "      }); });\n"
            . "    });\n"
            . "  });\n"
            . "});\n";

        $record = $this->harness('kmod', $bad);

        $this->assertNotEmpty($record['htmlStrings'], 'The harness must report the html string of the old code.');
        $this->assertStringContainsString($record['sentinels']['SUBJECT'], $record['htmlStrings'][0]);
    }

    public function testHarnessAcceptsSafeConstruction(): void
    {
        $good = "jQuery(document).ready(function (\$) {\n"
            . "  \$('#kmod_categories').change(function () {\n"
            . "    \$.getJSON(kunena_url_ajax, {}).done(function (json) {\n"
            . "      \$.each(json, function (i, object) { \$.each(object, function (k, element) {\n"
            . "        \$('#kmod_topics').append(\$('<option>').val(element['id']).text(element['subject']));\n"
            . "      }); });\n"
            . "    });\n"
            . "  });\n"
            . "});\n";

        $record = $this->harness('kmod', $good);

        $this->assertSame([], $record['htmlStrings']);
        $this->assertContains($record['sentinels']['SUBJECT'], $record['texts']);
    }

    // --- the real scripts ---

    public function testMoveTopicDropdownSetsTheSubjectAsText(): void
    {
        $record = $this->harness('kmod', $this->script('site/template/aurelia/assets/js/topic.js'));

        $this->assertNoHostileHtml($record);
        $this->assertContains($record['sentinels']['SUBJECT'], $record['texts'], 'The subject is set as text.');
        $this->assertContains('5', $record['vals'], 'The id is set as the value.');
    }

    public function testAvatarGalleryUsesAttributes(): void
    {
        $record = $this->harness('gallery', $this->script('site/template/aurelia/assets/js/main.js'));

        $this->assertNoHostileHtml($record);

        $attrs = implode("\n", $record['attrs']);

        $this->assertStringContainsString('src=' . $record['sentinels']['URL_VALUE'], $attrs, 'The address is set as the src attribute.');
        $this->assertStringContainsString($record['sentinels']['FILENAME'], $attrs . "\n" . implode("\n", $record['vals']), 'The file name is an attribute or a value.');
        $this->assertNotEmpty($record['vals'], 'The path of the picture is set as the value of the radio button.');
    }

    public function testAttachmentFieldsUseAttributes(): void
    {
        $record = $this->harness('upload', $this->script('media/kunena/core/js/upload.avatar.js'));

        $this->assertNoHostileHtml($record);
        $this->assertContains('placeholder=' . $record['sentinels']['NAME_ATTR'], $record['attrs'], 'The file name is set as the placeholder attribute.');
        $this->assertContains('id=kattachs-7', $record['attrs']);
        $this->assertContains('name=attachment[7]', $record['attrs']);
    }

    public function testNoScriptOfTheTemplateBuildsOptionsFromStringsAnyMore(): void
    {
        $problems = [];

        foreach (['site/template/aurelia/assets/js/topic.js', 'site/template/aurelia/assets/js/main.js', 'media/kunena/core/js/upload.avatar.js'] as $file) {
            foreach (explode("\n", $this->script($file)) as $number => $line) {
                if (preg_match("/\\.append\\(\\s*'<(option|li|input)[^']*'\\s*\\+/", $line)) {
                    $problems[] = "$file:" . ($number + 1) . ' ' . trim($line);
                }
            }
        }

        $this->assertSame([], $problems, "HTML is built by adding strings:\n" . implode("\n", $problems));
    }

    // --- the PHP side: the raw category list ---

    public function testRawCategoryControllerDeclaresItsProperty(): void
    {
        $source = $this->script('site/src/Controller/Application/Category/Raw/CategoryDisplay.php');

        $this->assertMatchesRegularExpression('/protected \$me\s*=\s*null;/', $source, 'PHP 8.2 reports the creation of an undeclared property in the middle of the json.');
        $this->assertSame(1, preg_match('/\$this->me\s*=[^=]/', $source, $m, PREG_OFFSET_CAPTURE), 'The controller still sets $this->me.');
        $this->assertLessThan($m[0][1], strpos($source, 'protected $me'), 'Declared before it is used.');
    }

    public function testNoApplicationControllerAssignsAnUndeclaredMe(): void
    {
        $problems = [];
        $files    = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(self::SRC . '/site/src/Controller/Application', \FilesystemIterator::SKIP_DOTS));

        foreach ($files as $file) {
            if ($file->getExtension() !== 'php') {
                continue;
            }

            $source = file_get_contents($file->getPathname());

            if (preg_match('/\$this->me\s*=[^=]/', $source) && !preg_match('/(public|protected|private)\s+(\??[\w\\\\|]+\s+)?\$me\b/', $source)) {
                $problems[] = str_replace('\\', '/', substr($file->getPathname(), \strlen(self::SRC)));
            }
        }

        $this->assertSame([], $problems, 'These classes create $this->me without declaring it: ' . implode(', ', $problems));
    }
}
