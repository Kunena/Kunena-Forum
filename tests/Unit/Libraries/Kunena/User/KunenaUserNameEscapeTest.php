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

namespace Kunena\Tests\Unit\Libraries\Kunena\User;

use Joomla\Registry\Registry;
use Kunena\Forum\Libraries\Integration\KunenaProfile;
use Kunena\Forum\Libraries\User\KunenaUser;
use Kunena\Forum\Plugin\Kunena\Kunena\Helper\KunenaProfileKunena;
use Kunena\Tests\Unit\UnitTestCase;
use ReflectionProperty;

/**
 * The name of a user as text (getName(..., false)): the e-mails, the popups, the structured data and the lists of names escape it
 * themselves. With the profile of the forum (the plugin Kunena - Kunena, on by default) the name came escaped anyway: the request
 * not to escape was not given to the profile, so "Tom & Jerry" read "Tom &amp; Jerry" (and "&amp;amp;" in the HTML of a mail).
 */
class KunenaUserNameEscapeTest extends UnitTestCase
{
    private mixed $savedProfile = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->requireJoomla();

        require_once \dirname(__DIR__, 5) . '/src/plugins/plg_kunena_kunena/src/Helper/KunenaProfileKunena.php';

        $instance           = new ReflectionProperty(KunenaProfile::class, 'instance');
        $this->savedProfile = $instance->getValue();
        $instance->setValue(null, new KunenaProfileKunena(new Registry()));
    }

    protected function tearDown(): void
    {
        (new ReflectionProperty(KunenaProfile::class, 'instance'))->setValue(null, $this->savedProfile);

        parent::tearDown();
    }

    public function testTheNameAsTextIsNotEscaped(): void
    {
        // A guest: the name of the message.
        $guest = self::bare(KunenaUser::class, ['userid' => 0, 'name' => '']);

        $this->assertSame('Tom & Jerry <3', $guest->getName('Tom & Jerry <3', false));
    }

    public function testTheNameForHtmlIsEscaped(): void
    {
        $guest = self::bare(KunenaUser::class, ['userid' => 0, 'name' => '']);

        $this->assertSame('Tom &amp; Jerry &lt;3', $guest->getName('Tom & Jerry <3'));
    }
}
