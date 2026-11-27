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

namespace Kunena\Tests\Unit\Libraries\Kunena\Access;

use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The categories which a user may read are kept for the request, on the access object, which clearCache() empties. They were kept in
 * the session of the visitor (a note: "handle guests/bots with no userstate"): the constructor cleared that copy on every request,
 * so it was only read back in the same request, and every visitor, a guest or a bot too, had it written to his session. A static
 * variable kept them for the request, which clearCache() could not empty: a change of the rights in the request kept the old list.
 * The list of another user (the name search asks whether a found member may read a category) is never kept beyond the request either.
 */
class KunenaAccessOtherUserSessionTest extends UnitTestCase
{
    public function testTheListsAreKeptOnTheObjectAndNotInTheSession(): void
    {
        $source = Files::read('src/libraries/kunena/src/Access/KunenaAccess.php');

        $this->assertSame(1, preg_match('/public function getAllowedCategories\(.*?\n    \}\n/s', $source, $m));

        $this->assertStringContainsString('$read = &$this->allowedCategories;', $m[0]);
        $this->assertStringContainsString('$byLanguage = &$this->allowedCategoriesByLanguage;', $m[0]);
        $this->assertStringNotContainsString('static $', $m[0], 'No list in a static variable, which clearCache() cannot empty.');
        $this->assertStringNotContainsString('UserState(', $source, 'No list in the session.');
    }

    /**
     * A plugin of an access type gives the categories of the user among the ones which it is given: the categories which are not
     * published, or in an unpublished section or category, are taken out before. A plugin which reads them from the database
     * (JomSocial, Community Builder) gave back such a category, and the user read what is hidden with its section.
     */
    public function testThePluginsGiveOnlyCategoriesOfTheList(): void
    {
        $source = Files::read('src/libraries/kunena/src/Access/KunenaAccess.php');

        $this->assertSame(1, preg_match('/public function getAllowedCategories\(.*?\n    \}\n/s', $source, $m));
        $this->assertStringContainsString('$visible = $categories;', $m[0]);
        $this->assertStringContainsString('$list += array_intersect_key($access->authoriseCategories($id, $categories), $visible);', $m[0]);
    }

    public function testClearCacheEmptiesTheLists(): void
    {
        $source = Files::read('src/libraries/kunena/src/Access/KunenaAccess.php');

        $this->assertSame(1, preg_match('/public function clearCache\(\)\s*\{(.*?)\n    \}\n/s', $source, $m));
        $this->assertStringContainsString('$this->allowedCategories           = [];', $m[1]);
        $this->assertStringContainsString('$this->allowedCategoriesByLanguage = [];', $m[1]);
    }
}
