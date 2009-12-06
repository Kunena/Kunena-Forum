<?php
/**
 * @version		$Id$
 * @package		Kunena
 * @subpackage	com_kunena
 * @copyright	Copyright (C) 2008 - 2009 Kunena Team. All rights reserved.
 * @license		GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
 * @link		http://www.kunena.com
 */

defined('_JEXEC') or die;

$config = $this->options;

$settings  = JHtml::_('kconfig.setting', $config->get('forum_offline'), 'forum_offline', 'Forum Offline', '...', 'yes/no');
$settings .= JHtml::_('kconfig.setting', $config->get('forum_title'), 'forum_title', 'Forum Title', 'The name of your forum.', 'text', 30);
$settings .= JHtml::_('kconfig.setting', $config->get('forum_email'), 'forum_email', 'Forum eMail', 'The forum\'s email address. Make this a valid email address', 'text', 30);
$settings .= JHtml::_('kconfig.setting', $config->get('forum_offline_msg'), 'forum_offline_msg', 'Forum Offline Message', 'The message displayed instead of the forum when it is set offline', 'editor', 40, 20);

echo JHtml::_('kconfig.section', 'Global', $settings );

$settings  = JHtml::_('kconfig.setting', $config->get('enable_rss_feed'), 'enable_rss_feeds', 'Enable RSS Feeds', 'Set to yes to enable RSS feeds for the forum.', 'yes/no');
$settings .= JHtml::_('kconfig.setting', $config->get('rss_feed_type'), 'rss_feed_type', 'RSS Feed Type', 'Select between different feed types: By Thread or By Post.', 'list');
$settings .= JHtml::_('kconfig.setting', $config->get('rss_history'), 'rss_history', 'RSS History', 'Select between different amounts of history processed for RSS feeds', 'list');

echo JHtml::_('kconfig.section', 'RSS', $settings );

