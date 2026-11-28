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
 * On a multilingual site the number of new topics of a category counts the topics of the language of the page and of all languages,
 * as the lists of topics show them: a topic of another language was "new" in the index, and no list of the page gave it. (The count
 * needs the session and the read marks of a user: its code is read.)
 */
class KunenaCategoryHelperNewLanguageTest extends UnitTestCase
{
    public function testTheTopicsOfAnotherLanguageAreNotNew(): void
    {
        $source = str_replace("\r\n", "\n", Files::read('src/libraries/kunena/src/Forum/Category/KunenaCategoryHelper.php'));
        $start  = strpos($source, '    public static function getNewTopics(');
        $body   = substr($source, $start, (int) strpos($source, "\n    }\n", $start) - $start);
        $filter = strpos($body, "\$query->where(\$db->quoteName('t.language') . ' IN (' . \$db->quote('*') . ', ' . \$db->quote(KunenaMultilanguage::getCurrent()) . ')');");

        $this->assertNotFalse($filter);
        $this->assertLessThan(strpos($body, '$db->setQuery($query);'), $filter);
        $this->assertStringContainsString('if (KunenaMultilanguage::isEnabled()) {', substr($body, $filter - 80, 80));
    }
}
