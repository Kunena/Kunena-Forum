<?php

/**
 * Kunena Component
 *
 * @package        Kunena.Installer
 *
 * @copyright      Copyright (C) 2008 - 2023 Kunena Team. All rights reserved.
 * @license        https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link           https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Install;

use Exception;
use Joomla\CMS\Date\Date;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\Database\Exception\ExecutionFailureException;
use Kunena\Forum\Libraries\Exception\KunenaException;
use Kunena\Forum\Libraries\Route\KunenaRoute;

\defined('_JEXEC') or die();

/**
 * Install Sample Data for Kunena
 *
 * @since  K6.0
 */
class KunenaSampleData
{
    /**
     * @return  void
     *
     * @throws  Exception
     * @since   Kunena 6.0
     */
    public static function installSampleData(): void
    {
        $lang = Factory::getApplication()->getLanguage();
        $lang->load('com_kunena.install', JPATH_ADMINISTRATOR . '/components/com_kunena', 'en-GB');
        $lang->load('com_kunena.install', JPATH_ADMINISTRATOR . '/components/com_kunena');

        $db       = Factory::getContainer()->get('DatabaseDriver');
        $posttime = new Date();
        $my       = Factory::getApplication()->getIdentity();
        $queries  = [];

        $query = "INSERT INTO `#__kunena_ranks`
		(`rankId`, `rankTitle`, `rankMin`, `rankSpecial`, `rankImage`) VALUES
		(1, {$db->quote('COM_KUNENA_SAMPLEDATA_RANK1')}, 0, 0, 'rank1.gif'),
		(2, {$db->quote('COM_KUNENA_SAMPLEDATA_RANK2')}, 20, 0, 'rank2.gif'),
		(3, {$db->quote('COM_KUNENA_SAMPLEDATA_RANK3')}, 40, 0, 'rank3.gif'),
		(4, {$db->quote('COM_KUNENA_SAMPLEDATA_RANK4')}, 80, 0, 'rank4.gif'),
		(5, {$db->quote('COM_KUNENA_SAMPLEDATA_RANK5')}, 160, 0, 'rank5.gif'),
		(6, {$db->quote('COM_KUNENA_SAMPLEDATA_RANK6')}, 320, 0, 'rank6.gif'),
		(7, {$db->quote('COM_KUNENA_SAMPLEDATA_RANK_ADMIN')}, 0, 1, 'rankadmin.gif'),
		(8, {$db->quote('COM_KUNENA_SAMPLEDATA_RANK_MODERATOR')}, 0, 1, 'rankmod.gif'),
		(9, {$db->quote('COM_KUNENA_SAMPLEDATA_RANK_SPAMMER')}, 0, 1, 'rankspammer.gif'),
		(10, {$db->quote('COM_KUNENA_SAMPLEDATA_RANK_BANNED')}, 0, 1, 'rankbanned.gif');";

        $queries[] = ['kunena_ranks', $query];

        $query = "INSERT INTO `#__kunena_smileys`
		(`id`,`code`,`location`,`greylocation`,`emoticonbar`) VALUES
		(1, 'B)', '1.png', 'cool-grey.png', 1),
		(2, '8)', '2.png', 'cool-grey.png', 1),
		(3, '8-)', '3.png', 'cool-grey.png', 1),
		(4, ':-(', '4.png', 'sad-grey.png', 1),
		(5, ':(', '5.png', 'sad-grey.png', 1),
		(6, ':sad:', '6.png', 'sad-grey.png', 1),
		(7, ':cry:', '7.png', 'sad-grey.png', 1),
		(8, ':)', '8.png', 'smile-grey.png', 1),
		(9, ':-)', '9.png', 'smile-grey.png', 1),
		(10, ':cheer:', '10.png', 'cheerful-grey.png', 1),
		(11, ';)', '11.png', 'wink-grey.png', 1),
		(12, ';-)', '12.png', 'wink-grey.png', 1),
		(13, ':wink:', '13.png', 'wink-grey.png', 1),
		(14, ';-)', '14.png', 'wink-grey.png', 1),
		(15, ':P', '15.png', 'tongue-grey.png', 1),
		(16, ':p', '16.png', 'tongue-grey.png', 1),
		(17, ':-p', '17.png', 'tongue-grey.png', 1),
		(18, ':-P', '18.png', 'tongue-grey.png', 1),
		(19, ':razz:', '19.png', 'tongue-grey.png', 1),
		(20, ':angry:', '20.png', 'angry-grey.png', 1),
		(21, ':mad:', '21.png', 'angry-grey.png', 1),
		(22, ':unsure:', '22.png', 'unsure-grey.png', 1),
		(23, ':o', '23.png', 'shocked-grey.png', 1);";

        $queries[] = ['kunena_smileys', $query];

        $section       = Text::_('COM_KUNENA_SAMPLEDATA_SECTION_TITLE');
        $cat1          = Text::_('COM_KUNENA_SAMPLEDATA_CATEGORY1_TITLE');
        $cat2          = Text::_('COM_KUNENA_SAMPLEDATA_CATEGORY2_TITLE');
        $section_alias = KunenaRoute::stringURLSafe(Text::_('COM_KUNENA_SAMPLEDATA_SECTION_TITLE'), 'main-forum');
        $cat1_alias    = KunenaRoute::stringURLSafe(Text::_('COM_KUNENA_SAMPLEDATA_CATEGORY1_TITLE'), 'welcome-mat');
        $cat2_alias    = KunenaRoute::stringURLSafe(Text::_('COM_KUNENA_SAMPLEDATA_CATEGORY2_TITLE'), 'suggestion-box');

        $query = "INSERT INTO `#__kunena_aliases` (`alias`, `type`, `item`, `state`) VALUES
		('announcement', 'view', 'announcement', 1),
		('category', 'view', 'category', 1),
		('common', 'view', 'common', 1),
		('credits', 'view', 'credits', 1),
		('home', 'view', 'home', 1),
		('misc', 'view', 'misc', 1),
		('search', 'view', 'search', 1),
		('statistics', 'view', 'statistics', 1),
		('topic', 'view', 'topic', 1),
		('topics', 'view', 'topics', 1),
		('user', 'view', 'user', 1),
		('category/create', 'layout', 'category.create', 1),
		('create', 'layout', 'category.create', 0),
		('category/default', 'layout', 'category.default', 1),
		('default', 'layout', 'category.default', 0),
		('category/edit', 'layout', 'category.edit', 1),
		('edit', 'layout', 'category.edit', 0),
		('category/manage', 'layout', 'category.manage', 1),
		('manage', 'layout', 'category.manage', 0),
		('category/moderate', 'layout', 'category.moderate', 1),
		('moderate', 'layout', 'category.moderate', 0),
		('category/user', 'layout', 'category.user', 1),
		({$db->quote($section_alias)}, 'catid', '1', 1),
		({$db->quote($cat1_alias)}, 'catid', '2', 1),
		({$db->quote($cat2_alias)}, 'catid', '3', 1);";

        $queries[] = ['kunena_aliases', $query];

        $query = "INSERT INTO `#__kunena_categories`
			(`id`, `parentid`, `name`, `alias`, `icon`, `icon_id`, `locked`, `accesstype`, `access`, `pubAccess`, `pubRecurse`, `adminAccess`, `adminRecurse`, `ordering`, `published`, `channels`, `checked_out`, `checked_out_time`, `review`, `allowAnonymous`, `postAnonymous`, `hits`, `description`, `headerdesc`, `topictemplate`, `class_sfx`, `allowPolls`, `topicOrdering`, `iconset`, `numTopics`, `numPosts`, `last_topic_id`, `last_post_id`, `last_post_time`, `params`, `allowRatings`) VALUES 
			(1, 0, {$db->quote($section)}, {$db->quote($section_alias)}, '', 0, 0, 'joomla.group', 1, 1, 1, 8, 1, 1, 1, 'THIS', 0, '0000-00-00 00:00:00', 0, 0, 0, 0, " . $db->quote(Text::_('COM_KUNENA_SAMPLEDATA_SECTION_DESC')) . ", " . $db->quote(Text::_('COM_KUNENA_SAMPLEDATA_SECTION_HEADER')) . ", '', '', 0, 'lastpost', 'default', 0, 0, 0, 0, 0, '{\"display\":{\"index\":{\"parent\":\"3\",\"children\":\"3\"}}}', 0),
			(2, 1, {$db->quote($cat1)}, {$db->quote($cat1_alias)}, '', 0, 0, 'joomla.group', 1, 1, 1, 8, 1, 1, 1, 'THIS', 0, '0000-00-00 00:00:00', 0, 0, 0, 0, " . $db->quote(Text::_('COM_KUNENA_SAMPLEDATA_CATEGORY1_DESC')) . ", " . $db->quote(Text::_('COM_KUNENA_SAMPLEDATA_CATEGORY1_HEADER')) . ", '', '', 0, 'lastpost', 'default', 1, 1, 1, 1, {$posttime->toUnix()}, '{\"display\":{\"index\":{\"parent\":\"3\",\"children\":\"3\"}}}', 0),
			(3, 1, {$db->quote($cat2)}, {$db->quote($cat2_alias)}, '', 0, 0, 'joomla.group', 1, 1, 1, 8, 1, 2, 1, 'THIS', 0, '0000-00-00 00:00:00', 0, 0, 0, 0, " . $db->quote(Text::_('COM_KUNENA_SAMPLEDATA_CATEGORY2_DESC')) . ", " . $db->quote(Text::_('COM_KUNENA_SAMPLEDATA_CATEGORY2_HEADER')) . ", '', '', 1, 'lastpost', 'default', 0, 0, 0, 0, 0, '{\"display\":{\"index\":{\"parent\":\"3\",\"children\":\"3\"}}}', 0);";

        $queries[] = ['kunena_categories', $query];

        $query = "INSERT INTO `#__kunena_messages`
		(`id`, `parent`, `thread`, `catid`, `userid`, `name`, `subject`, `time`, `ip`) VALUES
		(1, 0, 1, 2, " . $db->quote($my->id) . ", 'Kunena', " . $db->quote(Text::_('COM_KUNENA_SAMPLEDATA_POST_WELCOME_SUBJECT')) . ", " . $posttime->toUnix() . ", '127.0.0.1');";

        $queries[] = ['kunena_messages', $query];

        $query = "INSERT INTO `#__kunena_messages_text`
		(`mesid`, `message`) VALUES
		(1, " . $db->quote(Text::_('COM_KUNENA_SAMPLEDATA_POST_WELCOME_TEXT_CONTENT')) . ");";

        $queries[] = ['kunena_messages_text', $query];

        $query = "INSERT INTO `#__kunena_topics`
		(`id`, `category_id`, `subject`, `posts`, `first_post_id`, `first_post_time`, `first_post_userid`, `first_post_message`, `first_post_guest_name`, `last_post_id`, `last_post_time`, `last_post_userid`, `last_post_message`, `last_post_guest_name`, `rating`, `params`) VALUES
		(1, 2, " . $db->quote(Text::_('COM_KUNENA_SAMPLEDATA_POST_WELCOME_SUBJECT')) . ", 1, 1, " . $posttime->toUnix() . ", " . $db->quote($my->id) . ", " . $db->quote(Text::_('COM_KUNENA_SAMPLEDATA_POST_WELCOME_TEXT_CONTENT')) . ", 'Kunena', 1, " . $posttime->toUnix() . ", " . $db->quote($my->id) . ", " . $db->quote(Text::_('COM_KUNENA_SAMPLEDATA_POST_WELCOME_TEXT_CONTENT')) . ", 'Kunena', 1, '');";

        $queries[] = ['kunena_topics', $query];

        foreach ($queries as $query) {
            // Only insert sample/default data if table is empty
            $db->setQuery("SELECT * FROM " . $db->quoteName($db->getPrefix() . $query[0]), 0, 1);
            $filled = $db->loadObject();

            if (!$filled) {
                $db->setQuery($query[1]);

                try {
                    $db->execute();
                } catch (ExecutionFailureException $e) {
                    throw new KunenaException($e->getMessage(), $e->getCode());
                }
            }
        }

        // Insert missing users
        $query = "INSERT INTO #__kunena_users (userid, showOnline) SELECT a.id AS userid, 1 AS showOnline
		FROM #__users AS a LEFT JOIN #__kunena_users AS b ON b.userid=a.id WHERE b.userid IS NULL";

        $db->setQuery($query);

        try {
            $db->execute();
        } catch (ExecutionFailureException $e) {
            throw new KunenaException($e->getMessage(), $e->getCode());
        }

        $query = $db->getQuery(true);
        $query->update($db->quoteName('#__kunena_version'))->set('sampleData = 1')->setLimit(1);
        $db->setQuery($query);

        try {
            $db->execute();
        } catch (Exception $e) {
            throw new KunenaInstallerException($e->getMessage(), $e->getCode());
        }
    }
}
