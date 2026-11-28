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
 * The unpublish of announcements leaves the unpublished ones as they are. Its test compared the end of the announcement (a string)
 * with a date object, twice: an unpublished announcement with an end was skipped or saved again by chance, and one without an end was
 * saved again and reported as "unpublished". (The task needs a whole site: its code is read.)
 */
class AnnouncementUnpublishTest extends UnitTestCase
{
    public function testAnUnpublishedAnnouncementIsSkipped(): void
    {
        $source = str_replace("\r\n", "\n", Files::read('src/site/src/Controllers/AnnouncementController.php'));

        $this->assertSame(1, preg_match('/public function unpublish\(\)\s*\{(.*?)\n    \}\n/s', $source, $m));
        $this->assertMatchesRegularExpression("/if \(\(int\) \\\$announcement->published === 0\) \{\n\s*continue;/", $m[1]);
        $this->assertStringNotContainsString('$announcement->publish_down > $date_today', $m[1]);
    }
}
