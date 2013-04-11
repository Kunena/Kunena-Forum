<?php
/**
 * Kunena Component
 * @package Kunena.Framework
 * @subpackage Integration
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined('_JEXEC') or die;

if (!class_exists('JLoader')) return;

// Define Kunena framework path.
define('KPATH_FRAMEWORK', __DIR__);

// Register the library base path for Kunena Framework.
JLoader::registerPrefix('Kunena', __DIR__);

// Give access to all Kunena tables.
jimport('joomla.database.table');
JTable::addIncludePath(__DIR__ . '/tables');

// Give access to all Kunena JHtml functions.
jimport('joomla.html.html');
JHtml::addIncludePath(__DIR__ . '/html/html');

// Give access to all Kunena form fields.
JForm::addFieldPath(__DIR__ . '/form/fields');

// Register classes where the names have been changed to fit the autoloader rules.
// @deprecated
JLoader::register('KunenaAccess', __DIR__ . '/access.php');
JLoader::register('KunenaConfig', __DIR__ . '/config.php');
JLoader::register('KunenaController', __DIR__ . '/controller.php');
JLoader::register('KunenaDate', __DIR__ . '/date.php');
JLoader::register('KunenaError', __DIR__ . '/error.php');
JLoader::register('KunenaException', __DIR__ . '/exception.php');
JLoader::register('KunenaFactory', __DIR__ . '/factory.php');
JLoader::register('KunenaInstaller', __DIR__ . '/installer.php');
JLoader::register('KunenaLogin', __DIR__ . '/login.php');
JLoader::register('KunenaModel', __DIR__ . '/model.php');
JLoader::register('KunenaProfiler', __DIR__ . '/profiler.php');
JLoader::register('KunenaSession', __DIR__ . '/session.php');
JLoader::register('KunenaTree', __DIR__ . '/tree.php');
JLoader::register('KunenaView', __DIR__ . '/view.php');
JLoader::register('KunenaActivity', __DIR__ . '/integration/activity.php');
JLoader::register('KunenaAvatar', __DIR__ . '/integration/avatar.php');
JLoader::register('KunenaPrivate', __DIR__ . '/integration/private.php');
JLoader::register('KunenaProfile', __DIR__ . '/integration/profile.php');
JLoader::register('KunenaForumAnnouncement', __DIR__ . '/forum/announcement/announcement.php');
JLoader::register('KunenaForumCategory', __DIR__ . '/forum/category/category.php');
JLoader::register('KunenaForumCategoryUser', __DIR__ . '/forum/category/user/user.php');
JLoader::register('KunenaForumMessage', __DIR__ . '/forum/message/message.php');
JLoader::register('KunenaForumMessageAttachment', __DIR__ . '/forum/message/attachment/attachment.php');
JLoader::register('KunenaForumMessageThankyou', __DIR__ . '/forum/message/thankyou/thankyou.php');
JLoader::register('KunenaForumTopic', __DIR__ . '/forum/topic/topic.php');
JLoader::register('KunenaForumTopicPoll', __DIR__ . '/forum/topic/poll/poll.php');
JLoader::register('KunenaForumTopicUser', __DIR__ . '/forum/topic/user/user.php');
JLoader::register('KunenaForumTopicUserRead', __DIR__ . '/forum/topic/user/read/read.php');
