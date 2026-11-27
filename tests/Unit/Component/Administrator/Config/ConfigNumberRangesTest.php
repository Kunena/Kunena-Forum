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

namespace Kunena\Tests\Unit\Component\Administrator\Config;

use Joomla\CMS\Form\Rule\NumberRule;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The numbers of the settings have a range (min, max), which only the browser applied: Joomla checks a field with the rule of its
 * "validate" attribute only. A request saved 0 messages per page (all on one page, and a division by zero in the old views) or
 * a batch of 0 mails (the whole queue at once).
 */
class ConfigNumberRangesTest extends UnitTestCase
{
    public function testEveryBoundedNumberIsValidated(): void
    {
        $xml = simplexml_load_file(\dirname(__DIR__, 5) . '/src/admin/config.xml');

        $this->assertNotFalse($xml);

        $fields = $xml->xpath('//field[@type="number"][@min or @max]');

        $this->assertNotEmpty($fields);

        foreach ($fields as $field) {
            $this->assertSame('number', (string) $field['validate'], (string) $field['name']);
        }
    }

    public function testTheSaveRefusesANumberOutOfItsRange(): void
    {
        $this->requireJoomla();

        // The rule which the attribute names, with the range of the field (the form needs a whole application to run).
        $element = simplexml_load_file(\dirname(__DIR__, 5) . '/src/admin/config.xml')->xpath('//field[@name="messagesPerPage"]')[0];
        $rule    = new NumberRule();

        $this->assertSame('number', (string) $element['validate']);
        $this->assertFalse($rule->test($element, 0));
        $this->assertTrue($rule->test($element, 6));
    }
}
