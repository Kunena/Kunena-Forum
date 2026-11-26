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

namespace Kunena\Tests\Unit\Plugin\Kunena\Community;

use Kunena\Forum\Plugin\Kunena\Community\Helper\KunenaActivityCommunity;
use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * The JomSocial activity of a message: its access (0 public, 20 registered, 40 private) was read from the ids 1 and 2 of a level
 * or a group. A view level is what its groups say, and a category of a group without the groups under it is not for those groups:
 * the activity of a category "Registered only" was shown to every registered user. And the activities of the replies of a deleted
 * topic were removed under a name which no activity has.
 */
class KunenaActivityCommunityAccessTest extends UnitTestCase
{
    protected function setUp(): void
    {
        $this->requireJoomla();

        require_once \dirname(__DIR__, 5) . '/src/plugins/plg_kunena_community/src/Helper/KunenaActivityCommunity.php';
    }

    public static function categories(): array
    {
        return [
            'a level with Public'                  => ['joomla.level', 0, false, 0, false, [1], 0],
            'a level 1 without Public'             => ['joomla.level', 0, false, 0, false, [6, 8], 40],
            'a level with Registered'              => ['joomla.level', 0, false, 0, false, [6, 2, 8], 20],
            'a level of other groups'              => ['joomla.level', 0, false, 0, false, [6, 3, 8], 40],
            'Public and the groups under it'       => ['joomla.group', 1, true, 0, false, [], 0],
            'Public only (the guests)'             => ['joomla.group', 1, false, 0, false, [], 40],
            'Registered and the groups under it'   => ['joomla.group', 2, true, 0, false, [], 20],
            'Registered only'                      => ['joomla.group', 2, false, 0, false, [], 40],
            'Registered as the admin group'        => ['joomla.group', 3, false, 2, true, [], 20],
            'the same group, admin with subgroups' => ['joomla.group', 2, false, 2, true, [], 20],
            'another extension'                    => ['communitybuilder', 1, true, 0, false, [1], 40],
        ];
    }

    #[DataProvider('categories')]
    public function testTheActivityIsOnlyForTheReadersOfTheCategory(string $type, int $pub, bool $pubRecurse, int $admin, bool $adminRecurse, array $levelGroups, int $access): void
    {
        $this->assertSame($access, KunenaActivityCommunity::activityAccess($type, $pub, $pubRecurse, $admin, $adminRecurse, $levelGroups));
    }

    public function testTheRepliesOfADeletedTopicAreRemovedUnderTheirName(): void
    {
        $source = Files::read('src/plugins/plg_kunena_community/src/Helper/KunenaActivityCommunity.php');

        $this->assertSame(1, preg_match('/public function onAfterDeleteTopic\(\$target\): void\s*\{(.*?)\n    \}/s', $source, $m));
        $this->assertStringContainsString("\$act->app     = 'kunena.thread.reply';", $source, 'The name of the activity of a reply.');
        $this->assertStringContainsString("CActivityStream::remove('kunena.thread.reply', \$target->id);", $m[1]);
        $this->assertStringNotContainsString("remove('kunena.thread.replied'", $source);
    }
}
