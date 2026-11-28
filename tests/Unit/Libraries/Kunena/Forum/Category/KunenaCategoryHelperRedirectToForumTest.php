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

namespace Kunena\Tests\Unit\Libraries\Kunena\Forum\Category;

use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * A page of a category of another forum of the site goes to that forum with its page and its options (as the redirects of a missing
 * Itemid keep them, KunenaRoute::keepQuery()): the address was rebuilt from the category and the topic only, so a link to the page 2
 * of a topic of another forum opened its first page. (The redirect needs the menu of a site: its code is read.)
 */
class KunenaCategoryHelperRedirectToForumTest extends UnitTestCase
{
    public function testThePageIsKept(): void
    {
        $source = str_replace("\r\n", "\n", Files::read('src/libraries/kunena/src/Forum/Category/KunenaCategoryHelper.php'));
        $start  = strpos($source, '    public static function redirectToForum(');
        $body   = substr($source, $start, (int) strpos($source, "\n    }\n", $start) - $start);

        $this->assertStringContainsString('$vars           = KunenaRoute::keepQuery($vars);', $body);
        $this->assertStringContainsString("\$app->redirect(KunenaRoute::_('index.php?' . http_build_query(\$vars), false), 301);", $body);
        $this->assertContains('limitstart', $this->keptNames());
    }

    /**
     * The names which KunenaRoute::keepQuery() keeps.
     */
    private function keptNames(): array
    {
        preg_match("/static \\\$names = \\[([^\\]]*)\\]/s", Files::read('src/libraries/kunena/src/Route/KunenaRoute.php'), $m);
        preg_match_all("/'([a-z_A-Z]+)'/", $m[1] ?? '', $names);

        return $names[1];
    }
}
