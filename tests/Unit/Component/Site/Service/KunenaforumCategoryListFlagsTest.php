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

namespace Kunena\Tests\Unit\Component\Site\Service;

use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;
use SimpleXMLElement;

/**
 * The flags of the list of categories are read as their text: a field of a form gives its attributes as XML, and (bool) of an
 * attribute is true for "0" and "false" too, so the list of the menu item "New topic" (sections="0") offered the sections. (The
 * list needs the categories of a site: the reading of the flags is tested, and its code.)
 */
class KunenaforumCategoryListFlagsTest extends UnitTestCase
{
    public function testAnXmlAttributeZeroIsFalse(): void
    {
        $field = new SimpleXMLElement('<field sections="0" unpublished="false" return_options="true"/>');

        // What the old code did, and what the code does now.
        $this->assertTrue((bool) $field['sections'], 'an attribute is an object, always true');
        $this->assertFalse(filter_var((string) $field['sections'], FILTER_VALIDATE_BOOLEAN));
        $this->assertFalse(filter_var((string) $field['unpublished'], FILTER_VALIDATE_BOOLEAN));
        $this->assertTrue(filter_var((string) $field['return_options'], FILTER_VALIDATE_BOOLEAN));
    }

    public function testTheListReadsItsFlagsAsText(): void
    {
        $source = Files::read('src/site/src/Service/Html/Kunenaforum.php');

        $this->assertStringContainsString('$flag = static fn (string $name): bool => isset($params[$name]) && filter_var((string) $params[$name], FILTER_VALIDATE_BOOLEAN);', $source);

        foreach (['unpublished', 'sections', 'hide_lonely', 'return_options'] as $name) {
            $this->assertStringContainsString("\$flag('" . $name . "')", $source, $name);
            $this->assertStringNotContainsString("(bool) \$params['" . $name . "']", $source, $name);
        }
    }
}
