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

namespace Kunena\Tests\Unit\Libraries\Kunena\BBCode;

use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * A link with a backslash is no link of the site. The browsers read [url=/\evil.example]x[/url] as //evil.example, another site;
 * the address has no host for the URI, so it got the link of the site: no rel="nofollow noopener noreferrer", no target. (The tag
 * needs the router and the layouts of a site: its code is read.)
 */
class KunenaBBCodeUrlBackslashTest extends UnitTestCase
{
    public function testABackslashIsNoInternalLink(): void
    {
        $source = Files::read('src/libraries/kunena/src/BBCode/KunenaBBCode.php');

        $this->assertStringContainsString("if (!str_contains(\$url, '\\\\') && (empty(\$host) || KunenaRoute::isInternal(\$url))) {", $source);
    }
}
