<?php

/**
 * Kunena Component
 *
 * @package        Kunena.Installer
 *
 * @copyright      Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
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

        $query = $db->createQuery();
        $query->insert($db->quoteName('#__kunena_ranks'))
            ->columns([$db->quoteName('rankId'), $db->quoteName('rankTitle'), $db->quoteName('rankMin'), $db->quoteName('rankSpecial'), $db->quoteName('rankImage')])
            ->values('1, ' . $db->quote('COM_KUNENA_SAMPLEDATA_RANK1') . ', 0, 0, ' . $db->quote('rank1.gif'))
            ->values('2, ' . $db->quote('COM_KUNENA_SAMPLEDATA_RANK2') . ', 20, 0, ' . $db->quote('rank2.gif'))
            ->values('3, ' . $db->quote('COM_KUNENA_SAMPLEDATA_RANK3') . ', 40, 0, ' . $db->quote('rank3.gif'))
            ->values('4, ' . $db->quote('COM_KUNENA_SAMPLEDATA_RANK4') . ', 80, 0, ' . $db->quote('rank4.gif'))
            ->values('5, ' . $db->quote('COM_KUNENA_SAMPLEDATA_RANK5') . ', 160, 0, ' . $db->quote('rank5.gif'))
            ->values('6, ' . $db->quote('COM_KUNENA_SAMPLEDATA_RANK6') . ', 320, 0, ' . $db->quote('rank6.gif'))
            ->values('7, ' . $db->quote('COM_KUNENA_SAMPLEDATA_RANK_ADMIN') . ', 0, 1, ' . $db->quote('rankadmin.gif'))
            ->values('8, ' . $db->quote('COM_KUNENA_SAMPLEDATA_RANK_MODERATOR') . ', 0, 1, ' . $db->quote('rankmod.gif'))
            ->values('9, ' . $db->quote('COM_KUNENA_SAMPLEDATA_RANK_SPAMMER') . ', 0, 1, ' . $db->quote('rankspammer.gif'))
            ->values('10, ' . $db->quote('COM_KUNENA_SAMPLEDATA_RANK_BANNED') . ', 0, 1, ' . $db->quote('rankbanned.gif'));

        $queries[] = ['kunena_ranks', $query];

        $query = $db->createQuery();
        $query->insert($db->quoteName('#__kunena_smileys'))
            ->columns([$db->quoteName('id'), $db->quoteName('code'), $db->quoteName('location'), $db->quoteName('emoticonbar')])
            ->values('1, ' . $db->quote('B)') . ', ' . $db->quote('1.png') . ', 1')
            ->values('2, ' . $db->quote('8)') . ', ' . $db->quote('2.png') . ', 1')
            ->values('3, ' . $db->quote('8-)') . ', ' . $db->quote('3.png') . ', 1')
            ->values('4, ' . $db->quote(':-(') . ', ' . $db->quote('4.png') . ', 1')
            ->values('5, ' . $db->quote(':(') . ', ' . $db->quote('5.png') . ', 1')
            ->values('6, ' . $db->quote(':sad:') . ', ' . $db->quote('6.png') . ', 1')
            ->values('7, ' . $db->quote(':cry:') . ', ' . $db->quote('7.png') . ', 1')
            ->values('8, ' . $db->quote(':)') . ', ' . $db->quote('8.png') . ', 1')
            ->values('9, ' . $db->quote(':-)') . ', ' . $db->quote('9.png') . ', 1')
            ->values('10, ' . $db->quote(':cheer:') . ', ' . $db->quote('10.png') . ', 1')
            ->values('11, ' . $db->quote(';)') . ', ' . $db->quote('11.png') . ', 1')
            ->values('12, ' . $db->quote(';-)') . ', ' . $db->quote('12.png') . ', 1')
            ->values('13, ' . $db->quote(':wink:') . ', ' . $db->quote('13.png') . ', 1')
            ->values('14, ' . $db->quote(';-)') . ', ' . $db->quote('14.png') . ', 1')
            ->values('15, ' . $db->quote(':P') . ', ' . $db->quote('15.png') . ', 1')
            ->values('16, ' . $db->quote(':p') . ', ' . $db->quote('16.png') . ', 1')
            ->values('17, ' . $db->quote(':-p') . ', ' . $db->quote('17.png') . ', 1')
            ->values('18, ' . $db->quote(':-P') . ', ' . $db->quote('18.png') . ', 1')
            ->values('19, ' . $db->quote(':razz:') . ', ' . $db->quote('19.png') . ', 1')
            ->values('20, ' . $db->quote(':angry:') . ', ' . $db->quote('20.png') . ', 1')
            ->values('21, ' . $db->quote(':mad:') . ', ' . $db->quote('21.png') . ', 1')
            ->values('22, ' . $db->quote(':unsure:') . ', ' . $db->quote('22.png') . ', 1')
            ->values('23, ' . $db->quote(':o') . ', ' . $db->quote('23.png') . ', 1');

        $queries[] = ['kunena_smileys', $query];

        $section       = Text::_('COM_KUNENA_SAMPLEDATA_SECTION_TITLE');
        $cat1          = Text::_('COM_KUNENA_SAMPLEDATA_CATEGORY1_TITLE');
        $cat2          = Text::_('COM_KUNENA_SAMPLEDATA_CATEGORY2_TITLE');
        $section_alias = KunenaRoute::stringURLSafe(Text::_('COM_KUNENA_SAMPLEDATA_SECTION_TITLE'), 'main-forum');
        $cat1_alias    = KunenaRoute::stringURLSafe(Text::_('COM_KUNENA_SAMPLEDATA_CATEGORY1_TITLE'), 'welcome-mat');
        $cat2_alias    = KunenaRoute::stringURLSafe(Text::_('COM_KUNENA_SAMPLEDATA_CATEGORY2_TITLE'), 'suggestion-box');

        $query = $db->createQuery();
        $query->insert($db->quoteName('#__kunena_aliases'))
            ->columns([$db->quoteName('alias'), $db->quoteName('type'), $db->quoteName('item'), $db->quoteName('state')])
            ->values($db->quote('announcement') . ', ' . $db->quote('view') . ', ' . $db->quote('announcement') . ', 1')
            ->values($db->quote('category') . ', ' . $db->quote('view') . ', ' . $db->quote('category') . ', 1')
            ->values($db->quote('common') . ', ' . $db->quote('view') . ', ' . $db->quote('common') . ', 1')
            ->values($db->quote('credits') . ', ' . $db->quote('view') . ', ' . $db->quote('credits') . ', 1')
            ->values($db->quote('home') . ', ' . $db->quote('view') . ', ' . $db->quote('home') . ', 1')
            ->values($db->quote('misc') . ', ' . $db->quote('view') . ', ' . $db->quote('misc') . ', 1')
            ->values($db->quote('search') . ', ' . $db->quote('view') . ', ' . $db->quote('search') . ', 1')
            ->values($db->quote('statistics') . ', ' . $db->quote('view') . ', ' . $db->quote('statistics') . ', 1')
            ->values($db->quote('topic') . ', ' . $db->quote('view') . ', ' . $db->quote('topic') . ', 1')
            ->values($db->quote('topics') . ', ' . $db->quote('view') . ', ' . $db->quote('topics') . ', 1')
            ->values($db->quote('user') . ', ' . $db->quote('view') . ', ' . $db->quote('user') . ', 1')
            ->values($db->quote('category/create') . ', ' . $db->quote('layout') . ', ' . $db->quote('category.create') . ', 1')
            ->values($db->quote('create') . ', ' . $db->quote('layout') . ', ' . $db->quote('category.create') . ', 0')
            ->values($db->quote('category/default') . ', ' . $db->quote('layout') . ', ' . $db->quote('category.default') . ', 1')
            ->values($db->quote('default') . ', ' . $db->quote('layout') . ', ' . $db->quote('category.default') . ', 0')
            ->values($db->quote('category/edit') . ', ' . $db->quote('layout') . ', ' . $db->quote('category.edit') . ', 1')
            ->values($db->quote('edit') . ', ' . $db->quote('layout') . ', ' . $db->quote('category.edit') . ', 0')
            ->values($db->quote('category/manage') . ', ' . $db->quote('layout') . ', ' . $db->quote('category.manage') . ', 1')
            ->values($db->quote('manage') . ', ' . $db->quote('layout') . ', ' . $db->quote('category.manage') . ', 0')
            ->values($db->quote('category/moderate') . ', ' . $db->quote('layout') . ', ' . $db->quote('category.moderate') . ', 1')
            ->values($db->quote('moderate') . ', ' . $db->quote('layout') . ', ' . $db->quote('category.moderate') . ', 0')
            ->values($db->quote('category/user') . ', ' . $db->quote('layout') . ', ' . $db->quote('category.user') . ', 1')
            ->values($db->quote($section_alias) . ', ' . $db->quote('catid') . ', ' . $db->quote('1') . ', 1')
            ->values($db->quote($cat1_alias) . ', ' . $db->quote('catid') . ', ' . $db->quote('2') . ', 1')
            ->values($db->quote($cat2_alias) . ', ' . $db->quote('catid') . ', ' . $db->quote('3') . ', 1');

        $queries[] = ['kunena_aliases', $query];

        $categoriesColumns = [
            $db->quoteName('id'), $db->quoteName('parentid'), $db->quoteName('name'), $db->quoteName('alias'),
            $db->quoteName('icon'), $db->quoteName('icon_id'), $db->quoteName('locked'), $db->quoteName('accesstype'),
            $db->quoteName('access'), $db->quoteName('pubAccess'), $db->quoteName('pubRecurse'), $db->quoteName('adminAccess'),
            $db->quoteName('adminRecurse'), $db->quoteName('ordering'), $db->quoteName('published'), $db->quoteName('channels'),
            $db->quoteName('checked_out'), $db->quoteName('checked_out_time'), $db->quoteName('review'),
            $db->quoteName('allowAnonymous'), $db->quoteName('postAnonymous'), $db->quoteName('hits'),
            $db->quoteName('description'), $db->quoteName('headerdesc'), $db->quoteName('topictemplate'),
            $db->quoteName('class_sfx'), $db->quoteName('allowPolls'), $db->quoteName('topicOrdering'),
            $db->quoteName('iconset'), $db->quoteName('numTopics'), $db->quoteName('numPosts'),
            $db->quoteName('last_topic_id'), $db->quoteName('last_post_id'), $db->quoteName('last_post_time'),
            $db->quoteName('params'), $db->quoteName('allowRatings')
        ];
        
        // Category 1 - Section
        $query = $db->createQuery();
        $query->insert($db->quoteName('#__kunena_categories'))
        ->columns($categoriesColumns)
            ->values(
                '1, 0, ' . $db->quote($section) . ', ' . $db->quote($section_alias) . ', ' . $db->quote('') . ', 0, 0, ' . $db->quote('joomla.group') . ', 1, 1, 1, 8, 1, 1, 1, ' . $db->quote('THIS') . ', 0, ' . $db->quote('0000-00-00 00:00:00') . ', 0, 0, 0, 0, ' . $db->quote(Text::_('COM_KUNENA_SAMPLEDATA_SECTION_DESC')) . ', ' . $db->quote(Text::_('COM_KUNENA_SAMPLEDATA_SECTION_HEADER')) . ', ' . $db->quote('') . ', ' . $db->quote('') . ', 0, ' . $db->quote('lastpost') . ', ' . $db->quote('default') . ', 0, 0, 0, 0, 0, ' . $db->quote('{"display":{"index":{"parent":"3","children":"3"}}}') . ', 0'
            );

        $queries[] = ['kunena_categories', $query];

        // Category 2
        $query = $db->createQuery();
        $query->insert($db->quoteName('#__kunena_categories'))
        ->columns($categoriesColumns)
            ->values(
                '2, 1, ' . $db->quote($cat1) . ', ' . $db->quote($cat1_alias) . ', ' . $db->quote('') . ', 0, 0, ' . $db->quote('joomla.group') . ', 1, 1, 1, 8, 1, 1, 1, ' . $db->quote('THIS') . ', 0, ' . $db->quote('0000-00-00 00:00:00') . ', 0, 0, 0, 0, ' . $db->quote(Text::_('COM_KUNENA_SAMPLEDATA_CATEGORY1_DESC')) . ', ' . $db->quote(Text::_('COM_KUNENA_SAMPLEDATA_CATEGORY1_HEADER')) . ', ' . $db->quote('') . ', ' . $db->quote('') . ', 0, ' . $db->quote('lastpost') . ', ' . $db->quote('default') . ', 1, 1, 1, 1, ' . $posttime->toUnix() . ', ' . $db->quote('{"display":{"index":{"parent":"3","children":"3"}}}') . ', 0'
            );

        $queries[] = ['kunena_categories', $query];
        
        // Category 3
        $query = $db->createQuery();
        $query->insert($db->quoteName('#__kunena_categories'))
        ->columns($categoriesColumns)
            ->values(
                '3, 1, ' . $db->quote($cat2) . ', ' . $db->quote($cat2_alias) . ', ' . $db->quote('') . ', 0, 0, ' . $db->quote('joomla.group') . ', 1, 1, 1, 8, 1, 2, 1, ' . $db->quote('THIS') . ', 0, ' . $db->quote('0000-00-00 00:00:00') . ', 0, 0, 0, 0, ' . $db->quote(Text::_('COM_KUNENA_SAMPLEDATA_CATEGORY2_DESC')) . ', ' . $db->quote(Text::_('COM_KUNENA_SAMPLEDATA_CATEGORY2_HEADER')) . ', ' . $db->quote('') . ', ' . $db->quote('') . ', 1, ' . $db->quote('lastpost') . ', ' . $db->quote('default') . ', 0, 0, 0, 0, 0, ' . $db->quote('{"display":{"index":{"parent":"3","children":"3"}}}') . ', 0'
            );

        $queries[] = ['kunena_categories', $query];

        $query = $db->createQuery();
        $query->insert($db->quoteName('#__kunena_messages'))
            ->columns([
                $db->quoteName('id'), 
                $db->quoteName('parent'), 
                $db->quoteName('thread'), 
                $db->quoteName('catid'), 
                $db->quoteName('userid'), 
                $db->quoteName('name'), 
                $db->quoteName('subject'), 
                $db->quoteName('time'), 
                $db->quoteName('ip')
            ])
            ->values(
                '1, 0, 1, 2, ' . $db->quote($my->id) . ', ' . $db->quote('Kunena') . ', ' . $db->quote(Text::_('COM_KUNENA_SAMPLEDATA_POST_WELCOME_SUBJECT')) . ', ' . $posttime->toUnix() . ', ' . $db->quote('127.0.0.1')
            );

        $queries[] = ['kunena_messages', $query];

        $query = $db->createQuery();
        $query->insert($db->quoteName('#__kunena_messages_text'))
            ->columns([$db->quoteName('mesid'), $db->quoteName('message')])
            ->values('1, ' . $db->quote(Text::_('COM_KUNENA_SAMPLEDATA_POST_WELCOME_TEXT_CONTENT')));

        $queries[] = ['kunena_messages_text', $query];

        $query = $db->createQuery();
        $query->insert($db->quoteName('#__kunena_topics'))
            ->columns([
                $db->quoteName('id'), 
                $db->quoteName('category_id'), 
                $db->quoteName('subject'), 
                $db->quoteName('posts'), 
                $db->quoteName('first_post_id'), 
                $db->quoteName('first_post_time'), 
                $db->quoteName('first_post_userid'), 
                $db->quoteName('first_post_message'), 
                $db->quoteName('first_post_guest_name'), 
                $db->quoteName('last_post_id'), 
                $db->quoteName('last_post_time'), 
                $db->quoteName('last_post_userid'), 
                $db->quoteName('last_post_message'), 
                $db->quoteName('last_post_guest_name'), 
                $db->quoteName('rating'), 
                $db->quoteName('params')
            ])
            ->values(
                '1, 2, ' . $db->quote(Text::_('COM_KUNENA_SAMPLEDATA_POST_WELCOME_SUBJECT')) . ', 1, 1, ' . $posttime->toUnix() . ', ' . $db->quote($my->id) . ', ' . $db->quote(Text::_('COM_KUNENA_SAMPLEDATA_POST_WELCOME_TEXT_CONTENT')) . ', ' . $db->quote('Kunena') . ', 1, ' . $posttime->toUnix() . ', ' . $db->quote($my->id) . ', ' . $db->quote(Text::_('COM_KUNENA_SAMPLEDATA_POST_WELCOME_TEXT_CONTENT')) . ', ' . $db->quote('Kunena') . ', 1, ' . $db->quote('')
            );

        $queries[] = ['kunena_topics', $query];

        foreach ($queries as $query) {
            // Only insert sample/default data if table is empty
            $checkQuery = $db->createQuery();
            $checkQuery->select('*')->from($db->quoteName($db->getPrefix() . $query[0]));
            $db->setQuery($checkQuery, 0, 1);
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
        $query = $db->createQuery();
        $query->insert($db->quoteName('#__kunena_users'))
            ->columns([$db->quoteName('userid'), $db->quoteName('showOnline')])
            ->select($db->quoteName('a.id') . ' AS ' . $db->quoteName('userid'))
            ->select('1 AS ' . $db->quoteName('showOnline'))
            ->from($db->quoteName('#__users', 'a'))
            ->join('LEFT', $db->quoteName('#__kunena_users', 'b') . ' ON ' . $db->quoteName('b.userid') . '=' . $db->quoteName('a.id'))
            ->where($db->quoteName('b.userid') . ' IS NULL');

        $db->setQuery($query);

        try {
            $db->execute();
        } catch (ExecutionFailureException $e) {
            throw new KunenaException($e->getMessage(), $e->getCode());
        }
        
        // Update number of posts of the user linked to the welcome message
        $query = $db->createQuery();
        $query->update($db->quoteName('#__kunena_users'))
            ->set($db->quoteName('posts') . ' = 1')
            ->where($db->quoteName('userid') . ' = ' . $db->quote($my->id));
        
        $db->setQuery($query);
        
        try {
            $db->execute();
        } catch (ExecutionFailureException $e) {
            throw new KunenaException($e->getMessage(), $e->getCode());
        }

        $query = $db->createQuery();
        $query->update($db->quoteName('#__kunena_version'))->set('sampleData = 1')->setLimit(1);
        $db->setQuery($query);

        try {
            $db->execute();
        } catch (Exception $e) {
            throw new KunenaInstallerException($e->getMessage(), $e->getCode());
        }
    }
}
