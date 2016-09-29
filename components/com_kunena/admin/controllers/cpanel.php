<?php
/**
 * Kunena Component
 *
 * @package       Kunena.Administrator
 * @subpackage    Controllers
 *
 * @copyright (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license       http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/
defined('_JEXEC') or die ();

/**
 * Kunena Cpanel Controller
 *
 * @since 2.0
 */
class KunenaAdminControllerCpanel extends KunenaController
{
	protected $baseurl = null;

	public function __construct($config = array())
	{
		parent::__construct($config);
		$this->baseurl = 'index.php?option=com_kunena';
	}

	public function display($cachable = false, $urlparams = false)
	{
		$db = JFactory::getDbo();

		// Enable Kunena updates if they were disabled (but only every 6 hours or logout/login).
		$now       = time();
		$timestamp = $this->app->getUserState('pkg_kunena.updateCheck', 0);

		if ($timestamp < $now)
		{
			$query = $db->getQuery(true)
				->update($db->quoteName('#__update_sites'))
				->set($db->quoteName('enabled') . '=1')
				->where($db->quoteName('location') . ' LIKE ' . $db->quote('http://update.kunena.org/%'));
			$db->setQuery($query);
			$db->execute();

			$this->app->setUserState('pkg_kunena.updateCheck', $now + 60 * 60 * 6);
		}

		parent::display($cachable, $urlparams);
	}
	/**
	 * On get icons
	 *
	 * Display Kunena updates on dashboard
	 *
	 * @return array|null
	 *
	 * @since    2.0.0-BETA2
	 */
	public static function onGetIcons()
	{
		$updateInfo = null;

		if (KunenaForum::installed() && JFactory::getUser()->authorise('core.manage', 'com_installer'))
		{
			$updateSite = 'https://update.kunena.org/%';
			$db         = JFactory::getDbo();

			$query = $db->getQuery(true)
				->select('*')
				->from($db->qn('#__updates'))
				->where($db->qn('extension_id') . ' > 0')
				->where($db->qn('detailsurl') . ' LIKE ' . $db->q($updateSite));
			$db->setQuery($query);
			$list = (array) $db->loadObjectList();

			if ($list)
			{
				$updateInfo          = new stdClass;
				$updateInfo->addons  = 0;
				$updateInfo->version = 0;

				foreach ($list as $item)
				{
					if ($item->element == 'pkg_kunena')
					{
						$updateInfo->version = $item->version;
					}
					else
					{
						$updateInfo->addons++;
					}
				}
			}
			else
			{
				$query = $db->getQuery(true)
					->select('update_site_id')
					->from($db->qn('#__update_sites'))
					->where($db->qn('enabled') . ' = 0')
					->where($db->qn('location') . ' LIKE ' . $db->q($updateSite));
				$db->setQuery($query);
				$updateInfo = !$db->loadResult();
			}
		}

		if (!empty($updateInfo->version) && version_compare(KunenaForum::version(), $updateInfo->version, '<'))
		{
			// Has updates
			$template =  KunenaFactory::getTemplate()->isHmvc();

			if (version_compare(JVERSION, '3.0', '>'))
			{
				if ($template)
				{
					JFactory::getApplication()->enqueueMessage(JText::_('This version of Kunena is no longer supported. Please upgrade to a supported version. <br/><br/> Please backup before updating.<br/> <br/><a class="btn btn-small btn-info" href="index.php?option=com_installer&view=update&filter_search=kunena"> Update Now</a>'), 'Notice');
				}
				elseif (KunenaFactory::getTemplate()->name = 'blue_eagle')
				{
					JFactory::getApplication()->enqueueMessage(JText::_('This version of Kunena is no longer supported. Please upgrade to a supported version.  <br/>This template (' . KunenaFactory::getTemplate()->name . ') is no longer supported. Please upgrade to a supported version.  <br/> Please backup before updating.<br/> <a class="btn btn-small btn-info" href="index.php?option=com_installer&view=update&filter_search=kunena"> Update Now</a>'), 'warning');
				}
				else
				{
					JFactory::getApplication()->enqueueMessage(JText::_('This version of Kunena is no longer supported. Please upgrade to a supported version. <br/> Please backup before updating.<br/> <a class="btn btn-small btn-info" href="index.php?option=com_installer&view=update&filter_search=kunena"> Update Now</a>'), 'warning');
				}
			}

			$icon = 'media/kunena/images/icons/icon-48-kupdate-update-white.png';
			$link = 'index.php?option=com_installer&view=update&filter_search=kunena';
		}
		elseif (!empty($updateInfo->addons))
		{
			// Has updated add-ons
			JFactory::getApplication()->enqueueMessage(JText::_('Kunena Update Found.  <a class="btn btn-small btn-info" href="index.php?option=com_installer&view=update&filter_search=kunena"> Update Now</a><br/> Please backup before updating.'), 'Notice');
			$icon = 'media/kunena/images/icons/icon-48-kupdate-update-white.png';
			$link = 'index.php?option=com_installer&view=update&filter_search=kunena';
		}
		else
		{
			// Already in the latest release
			$icon = 'media/kunena/images/icons/icon-48-kupdate-good-white.png';
			$link = '#';
		}

		return '<a href="' . $link . '"><img src="' . JUri::root() . $icon . '"/></a>';
	}
}
