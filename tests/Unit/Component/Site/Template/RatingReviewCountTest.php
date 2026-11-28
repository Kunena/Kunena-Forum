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

namespace Kunena\Tests\Unit\Component\Site\Template;

use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * Every layout which shows the rating of a topic gives it the number of the ratings: the rows of a category, of the topics of a user
 * and of the posts did not, so the stars said "4 stars rating from:  User(s)" (with the debug on, the page ended in an error: the
 * property is not defined). (The layouts need a whole site: their code is read.)
 */
class RatingReviewCountTest extends UnitTestCase
{
    public function testEveryRatingHasItsCount(): void
    {
        $calls = 0;
        $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(Files::path('src/site/template'), \FilesystemIterator::SKIP_DOTS));

        foreach ($files as $file) {
            if (!str_ends_with((string) $file, '.php')) {
                continue;
            }

            $source = (string) file_get_contents((string) $file);

            if (!preg_match_all("/subLayout\\('Widget\\/Rating'\\)(.{0,400})/s", $source, $m)) {
                continue;
            }

            foreach ($m[1] as $call) {
                $calls++;
                $this->assertStringContainsString("->set('reviewCount',", strstr($call, "->setLayout(", true) ?: $call, (string) $file);
            }
        }

        $this->assertGreaterThan(4, $calls);
    }
}
