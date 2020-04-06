<?php
/**
 * Kunena Package
 *
 * @package        Kunena.Package
 *
 * @copyright      Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license        https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link           https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Installer\InstallerScript;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Date\Date;
use Joomla\Database\Exception\ExecutionFailureException;
use Kunena\Forum\Libraries\Route\KunenaRoute;

/**
 * Kunena package installer script.
 *
 * @since Kunena
 */
class Pkg_KunenaInstallerScript extends InstallerScript
{
	/**
	 * The extension name. This should be set in the installer script.
	 *
	 * @var    string
	 * @since  5.4.0
	 */
	protected $extension = 'com_kunena';

	/**
	 * Minimum PHP version required to install the extension
	 *
	 * @var    string
	 * @since  5.4.0
	 */
	protected $minimumPhp = '7.2.5';

	/**
	 * Minimum Joomla! version required to install the extension
	 *
	 * @var    string
	 * @since  6.0.0
	 */
	protected $minimumJoomla = '4.0.0-dev';

	/**
	 * List of required PHP extensions.
	 *
	 * @var array
	 * @since Kunena
	 */
	protected $extensions = array('dom', 'gd', 'json', 'pcre', 'SimpleXML');

	/**
	 * @var  Joomla\CMS\Application\CMSApplication  Holds the application object
	 *
	 * @since   Kunena 6.0
	 */
	private $app;

	/**
	 * @var  string  During an update, it will be populated with the old release version
	 *
	 * @since   Kunena 6.0
	 */
	private $oldRelease;

	/**
	 *  Constructor
	 *
	 * @since   Kunena 6.0
	 */
	public function __construct()
	{
		$this->app = Factory::getApplication();
	}

	/**
	 * method to run before an install/update/uninstall method
	 *
	 * @param   string                                        $type   'install', 'update' or 'discover_install'
	 * @param   Joomla\CMS\Installer\Adapter\ComponentAdapter $parent Installerobject
	 *
	 * @return  boolean  false will terminate the installation
	 *
	 * @since   Kunena 6.0
	 */
	public function preflight($type, $parent)
	{
		// Storing old release number for process in postflight
		if (strtolower($type) == 'update')
		{
			$manifest         = $this->getItemArray('manifest_cache', '#__extensions', 'element', $this->extension);
			$this->oldRelease = $manifest['version'];

			// Check if update is allowed (only update from 5.1.0 and higher)
			if (version_compare($this->oldRelease, '5.1.0', '<'))
			{
				$this->app->enqueueMessage(Text::sprintf('COM_kunena_UPDATE_UNSUPPORTED', $this->oldRelease, '5.1.0'), 'error');

				return false;
			}
		}

		return parent::preflight($type, $parent);
	}

	/**
	 * Method to install the component
	 *
	 * @param   Joomla\CMS\Installer\Adapter\ComponentAdapter $parent Installerobject
	 *
	 * @return void
	 *
	 * @since   Kunena 6.0
	 */
	public function install($parent)
	{
		// Notice $parent->getParent() returns JInstaller object
		$parent->getParent()->setRedirectUrl('index.php?option=com_kunena');
	}

	/**
	 * Method to uninstall the component
	 *
	 * @param   Joomla\CMS\Installer\Adapter\ComponentAdapter $parent Installerobject
	 *
	 * @return void
	 *
	 * @since   Kunena 6.0
	 */
	public function uninstall($parent)
	{
	}

	/**
	 * method to update the component
	 *
	 * @param   Joomla\CMS\Installer\Adapter\ComponentAdapter $parent Installerobject
	 *
	 * @return void
	 *
	 * @since   Kunena 6.0
	 */
	public function update($parent)
	{
		if (version_compare($this->oldRelease, '6.0.0', '<'))
		{
			// Remove integrated player classes
			$this->deleteFiles[]   = '/administrator/components/com_kunena/models/fields/player.php';
			$this->deleteFolders[] = '/components/com_kunena/helpers/player';

			// Remove old SQL files
			$this->deleteFiles[]   = '/administrator/components/com_kunena/sql/updates/mysql/4.5.0.sql';
			$this->deleteFiles[]   = '/administrator/components/com_kunena/sql/updates/mysql/4.5.1.sql';
			$this->deleteFiles[]   = '/administrator/components/com_kunena/sql/updates/mysql/4.5.2.sql';
			$this->deleteFiles[]   = '/administrator/components/com_kunena/sql/updates/mysql/4.5.3.sql';
			$this->deleteFiles[]   = '/administrator/components/com_kunena/sql/updates/mysql/4.5.4.sql';
			$this->deleteFiles[]   = '/administrator/components/com_kunena/sql/updates/mysql/5.0.0.sql';
			$this->deleteFiles[]   = '/administrator/components/com_kunena/sql/updates/mysql/5.0.1.sql';
			$this->deleteFiles[]   = '/administrator/components/com_kunena/sql/updates/mysql/5.0.2.sql';
			$this->deleteFiles[]   = '/administrator/components/com_kunena/sql/updates/mysql/5.0.3.sql';
			$this->deleteFiles[]   = '/administrator/components/com_kunena/sql/updates/mysql/5.0.4.sql';
			$this->deleteFiles[]   = '/administrator/components/com_kunena/sql/updates/mysql/5.4.0.sql';
			$this->deleteFiles[]   = '/administrator/components/com_kunena/sql/updates/mysql/5.5.0.sql';
		}
	}

	/**
	 * method to run after an install/update/uninstall method
	 *
	 * @param   string                                         $type    'install', 'update' or 'discover_install'
	 * @param   Joomla\CMS\Installer\Adapter\ComponentAdapter  $parent  Installerobject
	 *
	 * @return void
	 *
	 * @since   Kunena 6.0
	 * @throws Exception
	 */
	public function postflight($type, $parent)
	{
		$type = strtolower($type);

		$this->enablePlugin('system', 'kunena');
		$this->enablePlugin('quickicon', 'kunena');

		if ($type == 'install' || $type == 'discover_install')
		{
			$this->installSampleData();
			$this->addDashboardMenu('kunena', 'kunena');
		}

		$this->app->enqueueMessage(Text::_('COM_KUNENA_POSTFLIGHT'), 'warning');
	}

	/**
	 * @param   string $group   group
	 * @param   string $element element
	 *
	 * @return boolean
	 *
	 * @since version
	 */
	public function enablePlugin($group, $element)
	{
		$plugin = Table::getInstance('extension', 'Joomla\\CMS\\Table\\');
		$id    = $plugin->find(array('type' => 'plugin', 'folder' => $group, 'element' => $element));

		// Load.
		if (!$plugin->load($id))
		{
			$this->setError($plugin->getError());

			return false;
		}

		$plugin->enabled = 1;

		return $plugin->store();
	}

	/**
	 * @return  integer
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	function installSampleData()
	{
		$lang = Factory::getLanguage();

		// $debug = $lang->setDebug(false);

		$db       = Factory::getDBO();
		$posttime = new Date;
		$my       = Factory::getApplication()->getIdentity();
		$queries  = [];

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
		('category/user', 'layout', 'category.user', 1);";

		$queries[] = ['kunena_aliases', $query];

		$query = "INSERT INTO `#__kunena_ranks`
		(`rank_id`, `rank_title`, `rank_min`, `rank_special`, `rank_image`) VALUES
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
		(1, 'B)', 'cool.svg', 'cool-grey.png', 1),
		(2, '8)', 'cool.svg', 'cool-grey.png', 0),
		(3, '8-)', 'cool.svg', 'cool-grey.png', 0),
		(4, ':-(', 'sad.svg', 'sad-grey.png', 0),
		(5, ':(', 'sad.svg', 'sad-grey.png', 1),
		(6, ':sad:', 'sad.svg', 'sad-grey.png', 0),
		(7, ':cry:', 'sad.svg', 'sad-grey.png', 0),
		(8, ':)', 'smile.svg', 'smile-grey.png', 1),
		(9, ':-)', 'smile.svg', 'smile-grey.png', 0),
		(10, ':cheer:', 'cheerful.svg', 'cheerful-grey.png', 1),
		(11, ';)', 'wink.svg', 'wink-grey.png', 1),
		(12, ';-)', 'wink.svg', 'wink-grey.png', 0),
		(13, ':wink:', 'wink.svg', 'wink-grey.png', 0),
		(14, ';-)', 'wink.svg', 'wink-grey.png', 0),
		(15, ':P', 'tongue.svg', 'tongue-grey.png', 1),
		(16, ':p', 'tongue.svg', 'tongue-grey.png', 0),
		(17, ':-p', 'tongue.svg', 'tongue-grey.png', 0),
		(18, ':-P', 'tongue.svg', 'tongue-grey.png', 0),
		(19, ':razz:', 'tongue.svg', 'tongue-grey.png', 0),
		(20, ':angry:', 'angry.svg', 'angry-grey.png', 1),
		(21, ':mad:', 'angry.svg', 'angry-grey.png', 0),
		(22, ':unsure:', 'unsure.svg', 'unsure-grey.png', 1),
		(23, ':o', 'shocked.svg', 'shocked-grey.png', 0),
		(24, ':-o', 'shocked.svg', 'shocked-grey.png', 0),
		(25, ':O', 'shocked.svg', 'shocked-grey.png', 0),
		(26, ':-O', 'shocked.svg', 'shocked-grey.png', 0),
		(27, ':eek:', 'shocked.svg', 'shocked-grey.png', 0),
		(28, ':ohmy:', 'shocked.svg', 'shocked-grey.png', 1),
		(29, ':huh:', 'wassat.svg', 'wassat-grey.png', 1),
		(30, ':?', 'confused.svg', 'confused-grey.png', 0),
		(31, ':-?', 'confused.svg', 'confused-grey.png', 0),
		(32, ':???', 'confused.svg', 'confused-grey.png', 0),
		(33, ':dry:', 'ermm.svg', 'ermm-grey.png', 1),
		(34, ':ermm:', 'ermm.svg', 'ermm-grey.png', 0),
		(35, ':lol:', 'grin.svg', 'grin-grey.png', 1),
		(36, ':X', 'sick.svg', 'sick-grey.png', 0),
		(37, ':x', 'sick.svg', 'sick-grey.png', 0),
		(38, ':sick:', 'sick.svg', 'sick-grey.png', 1),
		(39, ':silly:', 'silly.svg', 'silly-grey.png', 1),
		(40, ':y32b4:', 'silly.svg', 'silly-grey.png', 0),
		(41, ':blink:', 'blink.svg', 'blink-grey.png', 1),
		(42, ':blush:', 'blush.svg', 'blush-grey.png', 1),
		(43, ':oops:', 'blush.svg', 'blush-grey.png', 1),
		(44, ':kiss:', 'kissing.svg', 'kissing-grey.png', 1),
		(45, ':rolleyes:', 'blink.svg', 'blink-grey.png', 0),
		(46, ':roll:', 'blink.svg', 'blink-grey.png', 0),
		(47, ':woohoo:', 'w00t.svg', 'w00t-grey.png', 1),
		(48, ':side:', 'sideways.svg', 'sideways-grey.png', 1),
		(49, ':S', 'dizzy.svg', 'dizzy-grey.png', 1),
		(50, ':s', 'dizzy.svg', 'dizzy-grey.png', 0),
		(51, ':evil:', 'devil.svg', 'devil-grey.png', 1),
		(52, ':twisted:', 'devil.svg', 'devil-grey.png', 0),
		(53, ':whistle:', 'whistling.svg', 'whistling-grey.png', 1),
		(54, ':pinch:', 'pinch.svg', 'pinch-grey.png', 1),
		(55, ':D', 'laughing.svg', 'laughing-grey.png', 0),
		(56, ':-D', 'laughing.svg', 'laughing-grey.png', 0),
		(57, ':grin:', 'laughing.svg', 'laughing-grey.png', 0),
		(58, ':laugh:', 'laughing.svg', 'laughing-grey.png', 0),
		(59, ':|', 'neutral.svg', 'neutral-grey.png', 0),
		(60, ':-|', 'neutral.svg', 'neutral-grey.png', 0),
		(61, ':neutral:', 'neutral.svg', 'neutral-grey.png', 0),
		(62, ':mrgreen:', 'mrgreen.svg', 'mrgreen-grey.png', 0),
		(63, ':?:', 'question.svg', 'question-grey.png', 0),
		(64, ':!:', 'exclamation.svg', 'exclamation-grey.png', 0),
		(65, ':arrow:', 'arrow.svg', 'arrow-grey.png', 0),
		(66, ':idea:', 'idea.svg', 'idea-grey.png', 0)";

		$queries[] = ['kunena_smileys', $query];

		$section       = KText::_('COM_KUNENA_SAMPLEDATA_SECTION_TITLE');
		$cat1          = KText::_('COM_KUNENA_SAMPLEDATA_CATEGORY1_TITLE');
		$cat2          = KText::_('COM_KUNENA_SAMPLEDATA_CATEGORY2_TITLE');
		$section_alias = KunenaRoute::stringURLSafe(KText::_('COM_KUNENA_SAMPLEDATA_SECTION_TITLE'), 'main-forum');
		$cat1_alias    = KunenaRoute::stringURLSafe(KText::_('COM_KUNENA_SAMPLEDATA_CATEGORY1_TITLE'), 'welcome-mat');
		$cat2_alias    = KunenaRoute::stringURLSafe(KText::_('COM_KUNENA_SAMPLEDATA_CATEGORY2_TITLE'), 'suggestion-box');

		$aliasquery = "INSERT INTO `#__kunena_aliases` (`alias`, `type`, `item`, `state`) VALUES
			({$db->quote($section_alias)}, 'catid', '1', 1),
			({$db->quote($cat1_alias)}, 'catid', '2', 1),
			({$db->quote($cat2_alias)}, 'catid', '3', 1);";

		$query = "INSERT INTO `#__kunena_categories`
			(`id`, `parent_id`, `name`, `alias`, `icon`, `pub_access`, `ordering`, `published`,`channels`, `description`, `headerdesc`, `numTopics`, `numPosts`, `allow_polls`, `last_topic_id`, `last_post_id`, `last_post_time`, `accesstype`, `topictemplate`, `class_sfx`, `params`) VALUES
			(1, 0, {$db->quote($section)}, {$db->quote($section_alias)}, ' ' , 1, 1, 1, 'THIS', " . $db->quote(KText::_('COM_KUNENA_SAMPLEDATA_SECTION_DESC')) . ", " . $db->quote(KText::_('COM_KUNENA_SAMPLEDATA_SECTION_HEADER')) . ", 0, 0, 0, 0, 0, 0, 'joomla.group', '', '', ''),
			(2, 1, {$db->quote($cat1)}, {$db->quote($cat1_alias)}, ' ', 1, 1, 1, 'THIS', " . $db->quote(KText::_('COM_KUNENA_SAMPLEDATA_CATEGORY1_DESC')) . ", " . $db->quote(KText::_('COM_KUNENA_SAMPLEDATA_CATEGORY1_HEADER')) . ", 1 , 1, 0, 1, 1, {$posttime->toUnix()}, 'joomla.group', '', '', ''),
			(3, 1, {$db->quote($cat2)}, {$db->quote($cat2_alias)}, ' ', 1, 2, 1, 'THIS', " . $db->quote(KText::_('COM_KUNENA_SAMPLEDATA_CATEGORY2_DESC')) . ", " . $db->quote(KText::_('COM_KUNENA_SAMPLEDATA_CATEGORY2_HEADER')) . ", 0 , 0, 1, 0, 0, 0, 'joomla.group', '', '', '');";

		$queries[] = ['kunena_categories', $query];

		$query = "INSERT INTO `#__kunena_messages`
		(`id`, `parent`, `thread`, `catid`, `userid`, `name`, `subject`, `time`, `ip`) VALUES
		(1, 0, 1, 2, " . $db->quote($my->id) . ", 'Kunena', " . $db->quote(KText::_('COM_KUNENA_SAMPLEDATA_POST_WELCOME_SUBJECT')) . ", " . $posttime->toUnix() . ", '127.0.0.1');";

		$queries[] = ['kunena_messages', $query];

		$query = "INSERT INTO `#__kunena_messages_text`
		(`mesid`, `message`) VALUES
		(1, " . $db->quote(KText::_('COM_KUNENA_SAMPLEDATA_POST_WELCOME_TEXT_CONTENT')) . ");";

		$queries[] = ['kunena_messages_text', $query];

		$query = "INSERT INTO `#__kunena_topics`
		(`id`, `category_id`, `subject`, `posts`, `first_post_id`, `first_post_time`, `first_post_userid`, `first_post_message`, `first_post_guest_name`, `last_post_id`, `last_post_time`, `last_post_userid`, `last_post_message`, `last_post_guest_name`, `rating`, `params`) VALUES
		(1, 2, " . $db->quote(KText::_('COM_KUNENA_SAMPLEDATA_POST_WELCOME_SUBJECT')) . ", 1, 1, " . $posttime->toUnix() . ", " . $db->quote($my->id) . ", " . $db->quote(KText::_('COM_KUNENA_SAMPLEDATA_POST_WELCOME_TEXT_CONTENT')) . ", 'Kunena', 1, " . $posttime->toUnix() . ", " . $db->quote($my->id) . ", " . $db->quote(KText::_('COM_KUNENA_SAMPLEDATA_POST_WELCOME_TEXT_CONTENT')) . ", 'Kunena', 1, '');";

		$queries[] = ['kunena_topics', $query];

		$counter = 0;

		foreach ($queries as $query)
		{
			// Only insert sample/default data if table is empty
			$db->setQuery("SELECT * FROM " . $db->quoteName($db->getPrefix() . $query[0]), 0, 1);
			$filled = $db->loadObject();

			if (!$filled)
			{
				$db->setQuery($query[1]);

				try
				{
					$db->execute();
				}
				catch (ExecutionFailureException $e)
				{
					throw new Exception($e->getMessage(), $e->getCode());
				}

				if ($query[0] == 'kunena_categories')
				{
					$db->setQuery("CREATE TABLE IF NOT EXISTS `#__kunena_aliases` (
					`alias` varchar(255) NOT NULL,
					`type` varchar(10) NOT NULL,
					`item` varchar(32) NOT NULL,
					`state` tinyint(4) NOT NULL default '0',
					UNIQUE KEY `alias` (alias),
					KEY `state` (state),
					KEY `item` (item),
					KEY `type` (type) ) DEFAULT CHARACTER SET utf8;"
					);

					try
					{
						$db->execute();
					}
					catch (ExecutionFailureException $e)
					{
						throw new Exception($e->getMessage(), $e->getCode());
					}

					$db->setQuery($aliasquery);

					try
					{
						$db->execute();
					}
					catch (ExecutionFailureException $e)
					{
						throw new Exception($e->getMessage(), $e->getCode());
					}
				}

				$counter++;
			}
		}

		// $lang->setDebug($debug);

		// Insert missing users
		$query = "INSERT INTO #__kunena_users (userid, showOnline)
 SELECT a.id AS userid, 1 AS showOnline FROM #__users AS a LEFT JOIN #__kunena_users AS b ON b.userid=a.id WHERE b.userid IS NULL";
		$db->setQuery($query);

		try
		{
			$db->execute();
		}
		catch (ExecutionFailureException $e)
		{
			throw new Exception($e->getMessage(), $e->getCode());
		}

		return $counter;
	}
}

/**
 * Class KText
 *
 * @since   Kunena 6.0
 */
class KText
{
	/**
	 * @param   string  $string  string
	 *
	 * @return  mixed
	 *
	 * @since   Kunena 6.0
	 */
	public static function _($string)
	{
		return str_replace('\n', "\n", html_entity_decode(Text::_($string), ENT_COMPAT, 'UTF-8'));
	}
}
