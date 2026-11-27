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

namespace Kunena\Tests\Unit\Plugin\Kunena;

use Joomla\Registry\Registry;
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\User\KunenaUser;
use Kunena\Forum\Plugin\Kunena\Easyblog\Helper\KunenaProfileEasyblog;
use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;
use ReflectionProperty;

/**
 * The name of a user which a profile integration gives (KunenaUser::getName() trusts it when an integration is on):
 * - EasyBlog gave none (the base class gives an empty name): every author had an empty name;
 * - JomSocial and EasySocial did not escape it although it was asked, and the forum puts it into its links and attributes: the
 *   name of a guest with a quote broke out of the attribute;
 * - EasySocial gave the visitor name to a guest, empty for the author of a post: the posts of guests had no author.
 * (JomSocial and EasySocial need their components: their code is read.)
 */
class ProfileNamesTest extends UnitTestCase
{
    public function testEasyblogGivesTheNameOfTheForumEscaped(): void
    {
        $this->requireJoomla();

        require_once \dirname(__DIR__, 4) . '/src/plugins/plg_kunena_easyblog/src/Helper/KunenaProfileEasyblog.php';

        $instance = new ReflectionProperty(KunenaConfig::class, 'instance');
        $saved    = $instance->getValue();
        // The forum shows the usernames, a user cannot choose.
        $instance->setValue(null, self::bare(KunenaConfig::class, ['config' => ['username' => 1, 'userCanChooseName' => 0]]));

        try {
            $profile = new KunenaProfileEasyblog(new Registry());
            $user    = self::bare(KunenaUser::class, ['userid' => 5, 'username' => 'Tom & "Jerry"', 'name' => 'Thomas']);

            $this->assertSame('Tom &amp; &quot;Jerry&quot;', $profile->getProfileName($user));
            $this->assertSame('Tom & "Jerry"', $profile->getProfileName($user, '', false));
        } finally {
            $instance->setValue(null, $saved);
        }
    }

    public function testJomsocialAndEasysocialEscapeTheName(): void
    {
        foreach (
            [
                'src/plugins/plg_kunena_community/src/Helper/KunenaProfileCommunity.php',
                'src/plugins/plg_kunena_easysocial/src/Helper/KunenaProfileEasySocial.php',
            ] as $file
        ) {
            $source = str_replace("\r\n", "\n", Files::read($file));

            $this->assertSame(1, preg_match('/public function getProfileName\(.*?\n    \}\n/s', $source, $m), $file);
            $this->assertStringContainsString("return \$escape ? htmlspecialchars((string) \$name, \\ENT_COMPAT, 'UTF-8') : (string) \$name;", $m[0], $file);
            $this->assertStringContainsString('$name = $user->name ?: $visitorname;', $m[0], $file . ': the name of a guest');
        }
    }
}
