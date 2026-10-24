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

namespace Kunena\Tests\Unit\Codebase;

use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Every page of the template (pages/<view>/<layout>) is opened by at least one of the Cypress specs of the site: a new page which
 * has no system test fails here.
 */
class PageTestCoverageTest extends UnitTestCase
{
    private const SPECS = [
        'tests/System/integration/site/components/com_kunena/all-pages.cy.js',
        'tests/System/integration/site/components/com_kunena/all-pages-user.cy.js',
        'tests/System/integration/site/components/com_kunena/all-pages-admin.cy.js',
        'tests/System/integration/site/components/com_kunena/forum-browsing.cy.js',
    ];

    /**
     * Pages which the specs do not open, with the reason. A page which gets a test is taken out of the list.
     */
    private const KNOWN_UNCOVERED = [
        'statistics/whoisonline' => 'The page has no controller (see PageControllersTest::KNOWN_MISSING).',
        'topic/poll'             => 'The page is only the poll of a topic (it is opened by the vote, with a token).',
        'topic/vote'             => 'The page is only the vote of a poll (it needs a token).',
        'topic/unsubscribe'      => 'The page needs the signed address of an e-mail of the forum.',
    ];

    /**
     * The pages (view/layout) which the specs open.
     *
     * @return  string[]
     */
    private static function opened(): array
    {
        $opened = [];

        foreach (self::SPECS as $spec) {
            preg_match_all('/view=(\w+)((?:&(?:layout|mode)=\w+)*)/', Files::read($spec), $matches, PREG_SET_ORDER);

            foreach ($matches as $match) {
                preg_match('/layout=(\w+)/', $match[2], $layout);

                $opened[] = $match[1] . '/' . ($layout[1] ?? 'default');
            }
        }

        return array_values(array_unique($opened));
    }

    public static function pages(): array
    {
        $cases = [];

        foreach (glob(Files::path('src/site/template/aurelia/pages/*/*/default.php')) ?: [] as $path) {
            $page         = basename(\dirname($path, 2)) . '/' . basename(\dirname($path));
            $cases[$page] = [$page];
        }

        return $cases;
    }

    #[DataProvider('pages')]
    public function testThePageIsOpenedByASpec(string $page): void
    {
        if (isset(self::KNOWN_UNCOVERED[$page])) {
            $this->assertNotContains($page, self::opened(), "$page is opened by a spec now: take it out of KNOWN_UNCOVERED.");

            return;
        }

        $this->assertContains($page, self::opened(), "No Cypress spec opens the page $page.");
    }

    public function testTheSpecsExist(): void
    {
        foreach (self::SPECS as $spec) {
            $this->assertFileExists(Files::path($spec));
        }
    }
}
