<?php

/**
 * Kunena Component
 *
 * Regression test for the autocomplete of the user field (search form and list of users). The dropdown of Tribute writes each name
 * into the page as html, so a name with markup ran script in the browser of whoever typed the start of that name. The term was put
 * into the address as typed, and requests did not use a routed URL with a menu item. The page of the list of users
 * has no Tribute script at all, so the script stopped there with an error.
 *
 * @package         Kunena.Tests
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 **/

namespace Kunena\Tests\Unit\Js;

use PHPUnit\Framework\TestCase;

class UserSearchDropdownTest extends TestCase
{
    private const SCRIPT  = __DIR__ . '/../../../src/site/template/aurelia/assets/js/search.js';
    private const HARNESS = __DIR__ . '/harness/search.js';

    private function run_script(array $input): array
    {
        $out = [];
        $rc  = 1;
        @exec('node --version 2>&1', $out, $rc);

        if ($rc !== 0) {
            $this->markTestSkipped('node is not available.');
        }

        $proc = proc_open(['node', self::HARNESS, self::SCRIPT], [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $pipes);
        $this->assertIsResource($proc);

        fwrite($pipes[0], json_encode($input));
        fclose($pipes[0]);
        $stdout = stream_get_contents($pipes[1]);
        $stderr = stream_get_contents($pipes[2]);
        proc_close($proc);

        $record = json_decode($stdout, true);
        $this->assertIsArray($record, 'The harness failed: ' . $stderr);

        return $record;
    }

    public function testNamesAreEscapedInTheDropdown(): void
    {
        $r = $this->run_script([
            'base'  => '',
            'terms' => ['x'],
            'items' => [
                ['original' => ['name' => 'Jane <img src=x onerror=alert(1)>'], 'string' => 'raw'],
                ['original' => ['name' => '"><script>alert(1)</script>'], 'string' => 'raw'],
                ['original' => ['name' => "R&D 'team'"], 'string' => 'raw'],
                ['original' => ['name' => 'Plain Name'], 'string' => 'raw'],
            ],
        ]);

        $this->assertTrue($r['hasHelpers']);

        foreach ($r['lines'] as $line) {
            $this->assertStringNotContainsString('<', $line);
            $this->assertStringNotContainsString('>', $line);
            $this->assertStringNotContainsString('"', $line);
        }

        $this->assertSame('Jane &lt;img src=x onerror=alert(1)&gt;', $r['lines'][0]);
        $this->assertSame('R&amp;D &#39;team&#39;', $r['lines'][2]);
        $this->assertSame('Plain Name', $r['lines'][3]);
    }

    public function testNothingToShowIsEmptyAndNotBroken(): void
    {
        $r = $this->run_script(['base' => '', 'terms' => [], 'items' => [[], ['original' => []], ['string' => '<b>x</b>'], null]]);

        $this->assertSame('', $r['lines'][0]);
        $this->assertSame('', $r['lines'][1]);
        $this->assertSame('&lt;b&gt;x&lt;/b&gt;', $r['lines'][2], 'Even the highlighted string of Tribute is escaped.');
        $this->assertSame('', $r['lines'][3]);
    }

    public function testTermIsEncodedInTheAddress(): void
    {
        $r = $this->run_script(['base' => '/index.php?option=com_kunena&Itemid=123', 'terms' => ['jan', 'a&usersearch=b', 'x#y', 'é ü', '<script>', '../../x'], 'items' => []]);

        $this->assertStringEndsWith('&usersearch=jan', $r['urls'][0]);
        $this->assertStringEndsWith('&usersearch=a%26usersearch%3Db', $r['urls'][1], 'An & in the term does not add a parameter.');
        $this->assertStringEndsWith('&usersearch=x%23y', $r['urls'][2]);
        $this->assertStringEndsWith('&usersearch=%C3%A9%20%C3%BC', $r['urls'][3]);
        $this->assertStringEndsWith('&usersearch=%3Cscript%3E', $r['urls'][4]);
        $this->assertStringEndsWith('&usersearch=..%2F..%2Fx', $r['urls'][5]);
    }

    public function testRoutedAddressIsPreserved(): void
    {
        $plain = $this->run_script(['base' => '/7.1/index.php?option=com_kunena&view=user&task=getusersmentionssearch&format=json&Itemid=123', 'terms' => ['a'], 'items' => []]);
        $this->assertSame('/7.1/index.php?option=com_kunena&view=user&task=getusersmentionssearch&format=json&Itemid=123&usersearch=a', $plain['urls'][0]);

        $sef = $this->run_script(['base' => '/forum/members.json', 'terms' => ['a'], 'items' => []]);
        $this->assertSame('/forum/members.json?usersearch=a', $sef['urls'][0]);
    }

    public function testAutocompleteUsesTheUrlFromTheField(): void
    {
        $base = '/forum/index.php?option=com_kunena&view=user&task=getusersmentionssearch&format=json&Itemid=123';
        $r = $this->run_script(['base' => $base, 'terms' => ['a&b'], 'items' => [], 'exerciseRequest' => true]);

        $this->assertTrue($r['autocompleteAttached']);
        $this->assertSame(['method' => 'GET', 'url' => $base . '&usersearch=a%26b'], $r['request']);

        $missing = $this->run_script(['base' => '', 'terms' => ['a'], 'items' => [], 'exerciseRequest' => true]);
        $this->assertFalse($missing['autocompleteAttached']);
        $this->assertArrayNotHasKey('request', $missing);
    }

    public function testSearchFieldsExposeRoutedAutocompleteUrl(): void
    {
        foreach (['search/form/default.php', 'widget/search/user.php'] as $layout) {
            $source = file_get_contents(__DIR__ . '/../../../src/site/template/aurelia/layouts/' . $layout);
            $this->assertStringContainsString('data-search-url="<?php echo $this->escape(KunenaRoute::_(\'index.php?option=com_kunena&view=user&task=getusersmentionssearch&format=json\', false)); ?>"', $source);
        }
    }

    public function testPageWithoutTributeDoesNotStopTheScript(): void
    {
        $r = $this->run_script(['base' => '', 'terms' => [], 'items' => []]);

        $this->assertTrue($r['readyRegistered']);
        $this->assertSame('ok', $r['noTribute']);
    }

    public function testScriptUsesTheEscapingTemplateAndNoLongerBuildsTheAddressByHand(): void
    {
        $source = file_get_contents(self::SCRIPT);

        $this->assertStringContainsString('menuItemTemplate: KunenaUserSearch.menuItem', $source);
        $this->assertStringNotContainsString('"&usersearch=" + text', $source);
        $this->assertStringNotContainsString("var URL = '/index.php", $source);
        $this->assertStringContainsString('encodeURIComponent(term)', $source);
        $this->assertStringContainsString("typeof Tribute !== 'undefined'", $source);
    }
}
