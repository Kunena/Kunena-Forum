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

namespace Kunena\Tests\Unit\Features;

use Kunena\Forum\Libraries\Notification\KunenaPopupNotification;
use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;
use ReflectionClassConstant;

/**
 * Guards for the features of 7.2: the parts of a feature (the layout, the script, the task of the controller, the setting) are
 * connected. A feature which loses one of its parts fails here, before a visitor finds it.
 */
class FeatureWiringTest extends UnitTestCase
{
    private const AURELIA = 'src/site/template/aurelia';

    private function assertFileHas(string $file, string ...$needles): void
    {
        $content = Files::read($file);

        foreach ($needles as $needle) {
            $this->assertStringContainsString($needle, $content, "$file does not contain $needle.");
        }
    }

    public function testTheBanButtonOfAMessage(): void
    {
        $this->assertFileHas(self::AURELIA . '/layouts/message/item/ban/default.php', 'name="ban_days"', 'name="quick"', 'name="return"', 'name="reason_public"');
        $this->assertFileHas(self::AURELIA . '/layouts/message/item/actions/default.php', 'Message/Item/Ban', "get('ban_dropdown')");
        $this->assertFileHas('src/site/src/Controller/Message/Item/Actions/MessageItemActionsDisplay.php', "'ban' => false, 'ban_dropdown' => true", 'quickBan');
        // Only the administrators block a user from the whole site, and the way back is an address of the site.
        $this->assertFileHas('src/site/src/Controllers/UserController.php', "getInt('ban_days'", 'Uri::isInternal($return)', '!$this->me->isAdmin()');
    }

    public function testTheDefaultAnnouncement(): void
    {
        $this->assertFileHas('src/site/src/Controllers/AnnouncementController.php', 'public function banner()', 'isAdmin()', 'KunenaAnnouncementHelper::isVisible');
        $this->assertFileHas('src/libraries/kunena/src/Forum/Announcement/KunenaAnnouncementHelper.php', 'public static function getBanner()', 'public static function isVisible(');
        $this->assertFileHas('src/site/src/Controller/Widget/Announcement/WidgetAnnouncementDisplay.php', 'KunenaAnnouncementHelper::getBanner()');
        $this->assertFileHas('src/site/src/View/Common/HtmlView.php', 'KunenaAnnouncementHelper::getBanner()');
        $this->assertFileHas(self::AURELIA . '/layouts/announcement/listing/row/default.php', "'banner'", 'KunenaIcons::star()');
        $this->assertFileHas('src/site/src/Layout/Announcement/AnnouncementListing.php', "'banner'");
    }

    public function testTheHeaderOfACategoryHasAnIdAndALinkGoesToIt(): void
    {
        $layout = self::AURELIA . '/layouts/category/index/default.php';

        $this->assertFileHas($layout, 'id="category-header', 'assets/js/categoryanchor.js');
        $this->assertFileHas(self::AURELIA . '/assets/js/categoryanchor.js', '(category|section)?(\d+)', "'category-header'", "'shown.bs.collapse'");
        $this->assertFileExists(Files::path(self::AURELIA . '/assets/js/categoryanchor.js'));
    }

    public function testTheCardOfALink(): void
    {
        $this->assertFileHas(self::AURELIA . '/layouts/bbcode/linkcard/default.php', 'kbbcode-linkcard', 'referrerpolicy="no-referrer"', 'loading="lazy"', 'rel="nofollow noopener noreferrer"');
        $this->assertFileHas('src/site/src/Layout/Bbcode/BbcodeLinkcard.php', 'public mixed $url = null;', 'public mixed $title = null;', 'public mixed $description = null;', 'public mixed $image = null;', 'public mixed $site = null;');

        $parser = Files::read('src/libraries/kunena/src/BBCode/KunenaBBCode.php');

        $this->assertStringContainsString("KunenaLayout::factory('BBCode/LinkCard')", $parser);
        // Only a pasted link (parseUrl) gets a card: the url tag (DoURL) stays a normal link.
        $this->assertSame(1, substr_count($parser, "KunenaLayout::factory('BBCode/LinkCard')"));
        // The other use of the cache of the cards is the title of a link (get_title(), for the smart linking): no card.
        $this->assertSame(2, substr_count($parser, 'KunenaLinkPreview::get('));
        $this->assertStringContainsString('linkCards', $parser);
        $this->assertStringContainsString('KunenaMediaEmbed::spotify(', $parser);
        $this->assertStringContainsString('KunenaMediaEmbed::deezer(', $parser);
    }

    public function testThePopupsOfMentionsAndReplies(): void
    {
        $this->assertFileHas(self::AURELIA . '/template.php', 'assets/js/notifications.js', "'com_kunena.notifications'", 'KunenaPopupNotification::isEnabledFor');
        $this->assertFileHas('src/site/src/Controllers/UserController.php', 'public function notifications()', 'KunenaPopupNotification::get(');
        $this->assertFileHas('src/site/src/Controller/User/Edit/Settings/UserEditSettingsDisplay.php', "'popupnotifications'", 'kunena_popup_notifications');
        $this->assertFileHas('src/site/src/Controllers/UserController.php', 'kunena_popup_notifications');
    }

    public function testThePopupScriptPutsNoMarkupOfAPostInThePage(): void
    {
        $script = Files::read(self::AURELIA . '/assets/js/notifications.js');

        // The texts come from the posts of other users: they go in the page as text, never as html.
        $this->assertStringContainsString('.textContent =', $script);

        foreach (['innerHTML', 'outerHTML', 'insertAdjacentHTML', 'document.write', 'eval('] as $forbidden) {
            $this->assertStringNotContainsString($forbidden, $script, "notifications.js uses $forbidden.");
        }
    }

    public function testThePopupsShowAtMostThreeAtATime(): void
    {
        $this->assertSame(3, (new ReflectionClassConstant(KunenaPopupNotification::class, 'MAX_ITEMS'))->getValue());
    }

    public function testTheCategoriesOfTheFrontend(): void
    {
        $this->assertFileHas('src/site/src/Controllers/CategoryController.php', 'public function save(', 'public function delete(');
        $this->assertFileExists(Files::path(self::AURELIA . '/pages/category/create/default.php'));
        $this->assertFileHas(self::AURELIA . '/layouts/category/manage/default.php', 'name="alias"', 'inactiveLockDays', 'ownTopicsOnly');
    }

    public function testTheMapsOfOpenStreetMap(): void
    {
        $this->assertFileHas(self::AURELIA . '/layouts/bbcode/map/default.php', "KunenaMap::provider() === 'osm'", 'KunenaMap::osmEmbedUrl(', 'KunenaMap::geocode(');
        $this->assertFileHas('src/site/template/aurelia/layouts/user/item/summary/default.php', 'KunenaMap::');
    }

    public function testTheButtonMarkAsSolved(): void
    {
        $this->assertFileHas('src/site/src/Controllers/TopicController.php', 'public function solved()');
        $this->assertFileHas('src/site/src/Controllers/TopicsController.php', 'public function status()');
        $this->assertFileHas('src/site/src/Controller/Topic/Item/Actions/TopicItemActionsDisplay.php', 'KunenaTopic::ICON_SOLVED', "'solved_dropdown'");
        $this->assertFileHas(self::AURELIA . '/layouts/topic/item/actions/default.php', 'solved');
    }

    public function testTheGuestsDoNotSeeTheIpAddressAndTheLocationHasAPlugin(): void
    {
        $this->assertFileHas('src/site/src/Controller/Message/Item/Actions/MessageItemActionsDisplay.php', 'KunenaIpLocation::html(');
        $this->assertFileHas('src/site/src/Controller/Topic/Item/Message/TopicItemMessageDisplay.php', 'KunenaIpLocation::html(');
    }

    public function testTheMailsCanComeFromTheMailTemplatesOfJoomla(): void
    {
        $message = Files::read('src/libraries/kunena/src/Forum/Message/KunenaMessage.php');

        $this->assertStringContainsString("'com_kunena.subscription'", $message);
        $this->assertStringContainsString("'com_kunena.moderator'", $message);
        $this->assertStringContainsString("'com_kunena.mention'", $message);
        $this->assertFileHas('src/site/src/Controllers/TopicController.php', "'com_kunena.reportmessage'", 'KunenaMailTemplate::isUsed(');
        // The layouts stay as the way when the option is off.
        $this->assertStringContainsString("KunenaLayout::factory('Email/Subscription')", $message);
    }

    public function testTheAliasOfACategoryCanStartWithItsId(): void
    {
        $this->assertFileHas('src/libraries/kunena/src/Forum/Category/KunenaCategory.php', 'categoryAliasId', 'protected function withId(', 'autoAlias');
    }

    public function testTheReportsPluginHasItsParts(): void
    {
        $this->assertFileExists(Files::path('src/plugins/plg_kunena_reports/reports.xml'));
        $this->assertFileExists(Files::path('src/plugins/plg_kunena_reports/src/Extension/Reports.php'));
        $this->assertFileExists(Files::path('src/plugins/plg_kunena_reports/services/provider.php'));
    }
}
