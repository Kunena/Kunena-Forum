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

namespace Kunena\Tests\Unit\Libraries\Kunena\Api;

use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * A category of the API gives its last post only when the user may read its topic, as the index shows it: in a category where the
 * users see their own topics only, the id and the time of the last post of another user were given. (The category needs the router
 * of the site: its code is read.)
 */
class KunenaApiCategoryLastPostTest extends UnitTestCase
{
    public function testTheLastPostNeedsTheReadOfItsTopic(): void
    {
        $source = str_replace("\r\n", "\n", Files::read('src/libraries/kunena/src/Api/KunenaApi.php'));
        $start  = strpos($source, '    public static function category(KunenaCategory $category): object');
        $body   = substr($source, $start, (int) strpos($source, "\n    }\n", $start) - $start);

        $this->assertStringContainsString('$shown = !$last->exists() || $last->isAuthorised(\'read\');', $body);
        $this->assertStringContainsString("'last_post_id'   => \$shown ? (int) \$category->last_post_id : 0,", $body);
        $this->assertStringContainsString("'last_post_time' => self::date(\$shown ? \$category->last_post_time : 0),", $body);
    }
}
