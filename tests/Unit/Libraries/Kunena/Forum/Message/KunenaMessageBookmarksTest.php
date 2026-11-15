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

namespace Kunena\Tests\Unit\Libraries\Kunena\Forum\Message;

use Kunena\Forum\Libraries\Forum\Message\KunenaMessageBookmarks;
use Kunena\Forum\Libraries\User\KunenaUser;
use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * The bookmarks of the messages: who may bookmark, the list My bookmarks which only shows the bookmarks of the user who is logged in,
 * the table in every way a site gets its tables, and the clean-ups.
 */
class KunenaMessageBookmarksTest extends UnitTestCase
{
    private function user(array $properties = []): KunenaUser
    {
        // The class of the user needs the framework of Joomla.
        $this->requireJoomla();

        return self::bare(KunenaUser::class, $properties + ['userid' => 42, '_exists' => true, 'banned' => null, 'blocked' => 0]);
    }

    public function testALoggedInUserMayBookmark(): void
    {
        $this->assertTrue(KunenaMessageBookmarks::mayBookmark($this->user(), true));
    }

    public function testNobodyWhenTheSettingIsOff(): void
    {
        $this->assertFalse(KunenaMessageBookmarks::mayBookmark($this->user(), false));
    }

    public function testNotAGuest(): void
    {
        $this->assertFalse(KunenaMessageBookmarks::mayBookmark($this->user(['userid' => 0, '_exists' => false]), true));
        $this->assertFalse(KunenaMessageBookmarks::mayBookmark($this->user(['userid' => 0]), true));
    }

    public function testNothingIsBookmarkedWithoutUserOrMessage(): void
    {
        $this->assertFalse(KunenaMessageBookmarks::isBookmarked(5, 0));
        $this->assertFalse(KunenaMessageBookmarks::isBookmarked(0, 42));
    }

    public function testTheListShowsOnlyTheBookmarksOfTheUserWhoIsLoggedIn(): void
    {
        $source = Files::read('src/site/src/Controller/Message/Listing/Recent/MessageListingRecentDisplay.php');

        $this->assertSame(1, preg_match("/case 'bookmarks':(.*?)break;/s", $source, $m));
        $this->assertStringContainsString('$me = KunenaUserHelper::getMyself();', $m[1]);
        $this->assertStringContainsString("->filterByUser(\$me, 'bookmarker')", $m[1]);
        $this->assertStringNotContainsString('$user', $m[1], 'The list must not use the user of the request.');
    }

    public function testTheFinderJoinsTheBookmarksOfTheUser(): void
    {
        $this->assertMatchesRegularExpression(
            "/case 'bookmarker':.*?#__kunena_bookmarks.*?bm\.user_id.*?\(int\) \\\$user->userid/s",
            Files::read('src/libraries/kunena/src/Forum/Message/KunenaMessageFinder.php')
        );
    }

    public function testTheTaskChecksTheTokenAndTheReading(): void
    {
        $source = Files::read('src/site/src/Controllers/TopicController.php');

        $this->assertSame(1, preg_match('/public function bookmark\(\)\s*\{(.*?)\n    \}\n/s', $source, $m));

        $toggle = strpos($m[1], 'KunenaMessageBookmarks::toggle(');

        $this->assertLessThan($toggle, strpos($m[1], "Session::checkToken('get')"));
        $this->assertLessThan($toggle, strpos($m[1], "\$message->tryAuthorise('read')"));
        $this->assertLessThan($toggle, strpos($m[1], 'KunenaMessageBookmarks::mayBookmark('));
    }

    public static function menus(): array
    {
        return [
            ['src/site/tmpl/topics/posts.xml'],
            ['src/site/tmpl/topics/tmpl/posts.xml'],
            ['src/site/src/View/Topics/tmpl/posts.xml'],
        ];
    }

    #[DataProvider('menus')]
    public function testAMenuItemCanShowMyBookmarks(string $file): void
    {
        $xml = simplexml_load_file(Files::path($file));

        $this->assertCount(1, $xml->xpath('//field[@name="mode"]/option[@value="bookmarks"]'));
    }

    public function testTheSettingIsOnByDefault(): void
    {
        $xml    = simplexml_load_file(Files::path('src/admin/config.xml'));
        $fields = $xml->xpath('//field[@name="bookmarks"]');

        $this->assertCount(1, $fields);
        $this->assertSame('1', (string) $fields[0]['default']);
    }

    public function testANewInstallCreatesTheTable(): void
    {
        $this->assertMatchesRegularExpression(
            '/CREATE TABLE IF NOT EXISTS `#__kunena_bookmarks`[^;]*PRIMARY KEY \(`user_id`, `message_id`\)/i',
            Files::read('src/admin/sql/install/mysql/create_tables.utf8.sql')
        );

        $xml = simplexml_load_file(Files::path('src/admin/sql/install/schema/install.xml'));

        $this->assertCount(1, $xml->xpath('//table[@name="kunena_bookmarks"]/key[@name="PRIMARY"][@columns="user_id,message_id"]'));
        $this->assertStringContainsString('CREATE TABLE IF NOT EXISTS `#__kunena_bookmarks`', Files::read('src/admin/sql/updates/mysql/8.0.0-DEV.sql'));
        $this->assertStringContainsString('DROP TABLE IF EXISTS `#__kunena_bookmarks`;', Files::read('src/admin/sql/uninstall/mysql/uninstall.mysql.utf8.sql'));
    }

    public static function cleanUps(): array
    {
        return [
            'a deleted message'             => ['src/libraries/kunena/src/Forum/Message/KunenaMessage.php'],
            'a deleted topic'               => ['src/libraries/kunena/src/Forum/Topic/KunenaTopic.php'],
            'the topics deleted at once'    => ['src/libraries/kunena/src/Forum/Topic/KunenaTopicHelper.php'],
            'a purged category'             => ['src/libraries/kunena/src/Forum/Category/KunenaCategory.php'],
            'a deleted Joomla user'         => ['src/plugins/plg_system_kunena/src/Extension/Kunena.php'],
            'the privacy export and remove' => ['src/plugins/plg_privacy_kunena/src/Extension/Kunena.php'],
            'the diagnostics'               => ['src/libraries/kunena/src/Forum/KunenaDiagnostics.php'],
        ];
    }

    #[DataProvider('cleanUps')]
    public function testTheBookmarksAreRemovedWith(string $file): void
    {
        $this->assertStringContainsString('#__kunena_bookmarks', Files::read($file));
    }
}
