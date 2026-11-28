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

namespace Kunena\Tests\Unit\Component\Site\Controller;

use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * Every ordering of the topics of a category ends with the id of the topic: the topics of the same number of views or posts (most
 * have one post), of the same subject or of the same second were in an order which the database chose for each page, so a topic was
 * shown on two pages and another one on none. (The page needs a whole site: its code is read.)
 */
class CategoryTopicsOrderTieTest extends UnitTestCase
{
    public function testEveryOrderingEndsWithTheId(): void
    {
        $source = Files::read('src/site/src/Controller/Category/Topics/CategoryTopicsDisplay.php');

        preg_match_all("/\\\$params\\['orderby'\\] = ([^;]+);/", $source, $orderings);

        $this->assertCount(5, $orderings[1]);

        foreach ($orderings[1] as $ordering) {
            $this->assertMatchesRegularExpression("/tt\\.id (ASC'|' \\. \\\$direction)$/", trim($ordering), $ordering);
        }
    }
}
