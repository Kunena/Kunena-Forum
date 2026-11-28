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
 * The access of the forum takes the access types of every plugin of Kunena (the ranks of plg_kunena_acl), not only the one of the
 * plugin of Joomla: the site imports the group of the plugins only on its pages, so on the console (the scheduled tasks), in the API,
 * in the backend, and before that import on a page, a category of the ranks had no access type: nobody but the moderators could read
 * it, its e-mails of the queue were dropped and its scheduled replies never published. (The access needs a whole site: the
 * constructor is read.)
 */
class KunenaAccessImportsPluginsTest extends UnitTestCase
{
    public function testThePluginsOfKunenaAreImported(): void
    {
        $source = str_replace("\r\n", "\n", Files::read('src/libraries/kunena/src/Access/KunenaAccess.php'));

        $start = strpos($source, '    public function __construct()');
        $this->assertNotFalse($start);
        $constructor = substr($source, $start, (int) strpos($source, "\n    }\n", $start) - $start);

        $this->assertStringContainsString("PluginHelper::importPlugin('kunena');", $constructor);
        $this->assertStringNotContainsString("PluginHelper::importPlugin('kunena', 'joomla');", $constructor);
        $this->assertLessThan(strpos($constructor, "dispatch('onKunenaGetAccessControl'"), strpos($constructor, "importPlugin('kunena')"));
    }
}
