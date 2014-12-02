<?php
/**
 * Kunena Component
 * @package Kunena.Framework
 * @subpackage Integration
 *
 * @copyright (C) 2008 - 2014 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined('_JEXEC') or die;

if (!class_exists('JLoader')) return;

// Define Kunena framework path.
define('KPATH_FRAMEWORK', __DIR__);

// Register the library base path for Kunena Framework.
JLoader::registerPrefix('Kunena', KPATH_FRAMEWORK);

// Give access to all Kunena tables.
JTable::addIncludePath(KPATH_FRAMEWORK . '/tables');

// Give access to all Kunena JHtml functions.
JHtml::addIncludePath(KPATH_FRAMEWORK . '/html/html');

// Give access to all Kunena form fields.
JForm::addFieldPath(KPATH_FRAMEWORK . '/form/fields');

// Register classes where the names have been changed to fit the autoloader rules.
// @deprecated
JLoader::register('KunenaAccess', KPATH_FRAMEWORK . '/access.php');
JLoader::register('KunenaConfig', KPATH_FRAMEWORK . '/config.php');
JLoader::register('KunenaController', KPATH_FRAMEWORK . '/controller.php');
JLoader::register('KunenaDate', KPATH_FRAMEWORK . '/date.php');
JLoader::register('KunenaError', KPATH_FRAMEWORK . '/error.php');
JLoader::register('KunenaException', KPATH_FRAMEWORK . '/exception.php');
JLoader::register('KunenaFactory', KPATH_FRAMEWORK . '/factory.php');
JLoader::register('KunenaInstaller', KPATH_FRAMEWORK . '/installer.php');
JLoader::register('KunenaLogin', KPATH_FRAMEWORK . '/login.php');
JLoader::register('KunenaModel', KPATH_FRAMEWORK . '/model.php');
JLoader::register('KunenaProfiler', KPATH_FRAMEWORK . '/profiler.php');
JLoader::register('KunenaSession', KPATH_FRAMEWORK . '/session.php');
JLoader::register('KunenaTree', KPATH_FRAMEWORK . '/tree.php');
JLoader::register('KunenaView', KPATH_FRAMEWORK . '/view.php');
JLoader::register('KunenaAvatar', KPATH_FRAMEWORK . '/integration/avatar.php');
JLoader::register('KunenaPrivate', KPATH_FRAMEWORK . '/integration/private.php');
JLoader::register('KunenaProfile', KPATH_FRAMEWORK . '/integration/profile.php');
JLoader::register('KunenaForumAnnouncement', KPATH_FRAMEWORK . '/forum/announcement/announcement.php');
JLoader::register('KunenaForumCategory', KPATH_FRAMEWORK . '/forum/category/category.php');
JLoader::register('KunenaForumCategoryUser', KPATH_FRAMEWORK . '/forum/category/user/user.php');
JLoader::register('KunenaForumMessage', KPATH_FRAMEWORK . '/forum/message/message.php');
JLoader::register('KunenaForumMessageAttachment', KPATH_FRAMEWORK . '/forum/message/attachment/attachment.php');
JLoader::register('KunenaForumMessageThankyou', KPATH_FRAMEWORK . '/forum/message/thankyou/thankyou.php');
JLoader::register('KunenaForumTopic', KPATH_FRAMEWORK . '/forum/topic/topic.php');
JLoader::register('KunenaForumTopicPoll', KPATH_FRAMEWORK . '/forum/topic/poll/poll.php');
JLoader::register('KunenaForumTopicUser', KPATH_FRAMEWORK . '/forum/topic/user/user.php');
JLoader::register('KunenaForumTopicUserRead', KPATH_FRAMEWORK . '/forum/topic/user/read/read.php');

// Register CKunenaLink class in order to allow old templates to work
JLoader::register('CKunenaLink', KPATH_SITE . '/lib/kunena.link.class.php');
