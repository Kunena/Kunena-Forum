<?php
/**
* Kunena Component
* @package Kunena.Template.Crypsis
*
* @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.org
**/
defined( '_JEXEC' ) or die();

$map = array(
	'common/announcement' => array('page/announcement', 'default'),
	'common/breadcrumb' => array('page/breadcrumb', 'default'),
	'common/default' => array('page/custom', 'default'),
	'common/footer' => array('page/footer', 'default'),
	'common/forumjump' => array('page/forumjump', 'default'),
	'common/login' => array('page/menubar/login', 'default'),
	'common/logout' => array('page/menubar/logout', 'default'),
	'common/menu' => array('page/menubar', 'default'),
	'common/menu_menu' => array('page/menubar/menu', 'default'),
	'common/statistics' => array('page/statistics', 'default'),
	'common/whosonline' => array('page/whoisonline', 'default'),

	'credits/default' => array('credits', 'default'),

	'statistics/default' => array('statistics/general', 'default'),

	'search/default' => array('search', 'default'),
	'search/default_row' => array('search/row', 'default'),

	'announcement/create' => array('announcement/edit', 'create'),
	'announcement/default' => array('announcement/item', 'default'),
	'announcement/default_actions' => array('announcement/item/actions', 'default'),
	'announcement/edit' => array('announcement/edit', 'default'),
	'announcement/list' => array('announcement/list', 'default'),
	'announcement/list_item' => array('announcement/row', 'default'),

	'category/create' => array('category/edit', 'create'),
	'category/default' => array('category/item', 'default'),
	'category/default_actions' => array('category/item/actions', 'default'),
	'category/default_row' => array('category/item/row', 'default'),
	'category/default_subcategories' => array('category/index', 'subcategories'),
	'category/edit' => array('category/edit', 'default'),
	'category/list' => array('category/index', 'default'),
	'category/list_embed' => array('category/index', 'embed'),
	'category/manage' => array('category/manage', 'default'),
	'category/user' => array('category/user', 'default'),
	'category/user_embed' => array('category/user', 'embed'),
	'category/user_row' => array('category/user/row', 'default'),

	'topic/create' => array('topic/edit', 'create'),
	'topic/indented' => array('topic/item', 'indented'),
	'topic/default' => array('topic/item', 'default'),
	'topic/default_left' => array('topic/item/left', 'default'),
	'topic/default_list' => array('topic/item/list', 'default'),
	'topic/default_message' => array('topic/item/message', 'default'),
	'topic/default_message_actions' => array('topic/item/message_actions', 'default'),
	'topic/default_poll' => array('topic/item/poll', 'default'),
	'topic/default_profile' => array('topic/item/profile', 'default'),
	'topic/edit' => array('topic/edit', 'default'),
	'topic/edit_attachments' => array('topic/edit/attachments', 'default'),
	'topic/edit_editor' => array('topic/edit/editor', 'default'),
	'topic/edit_history' => array('topic/edit/history', 'default'),
	'topic/moderate' => array('topic/moderate', 'default'),
	'topic/report' => array('topic/report', 'default'),
	'topic/threaded' => array('topic/item', 'threaded'),
	'topic/threaded_row' => array('topic/item/row', 'threaded'),
	'topic/vote_embed' => array('topic/item/vote', 'default'),

	'topics/default' => array('topic/list', 'default'),
	'topics/default_embed' => array('topic/list', 'embed'),
	'topics/default_row' => array('topic/list/row', 'default'),
	'topics/posts' => array('message/list', 'default'),
	'topics/posts_embed' => array('message/list', 'embed'),
	'topics/posts_row' => array('message/list/row', 'default'),
	'topics/user' => array('topic/list', 'user'),
	'topics/user_embed' => array('topic/list', 'user_embed'),
	'topics/user_row' => array('topic/list/row','user'),

	'user/default' => array('user/item', 'default'),
	'user/default_attachments' => array('user/item/attachments', 'default'),
	'user/default_ban' => array('user/item/ban', 'default'),
	'user/default_banmanager' => array('user/item/banmanager', 'default'),
	'user/default_history' => array('user/item/history', 'default'),
	'user/default_social' => array('user/item/social', 'default'),
	'user/default_tab' => array('user/item/tab', 'default'),
	'user/edit' => array('user/edit', 'default'),
	'user/edit_avatar' => array('user/edit/avatar', 'default'),
	'user/edit_profile' => array('user/edit/profile', 'default'),
	'user/edit_settings' => array('user/edit/settings', 'default'),
	'user/edit_summary' => array('user/edit/summary', 'default'),
	'user/edit_tab' => array('user/edit/tab', 'default'),
	'user/edit_user' => array('user/edit/user', 'default'),
	'user/list' => array('user/list', 'default'),
);
