<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Administrator
 * @subpackage      Controllers
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

/**
 * Kunena Cpanel Controller
 *
 * @since  2.0
 */
class KunenaAdminControllerCpanel extends KunenaController
{
	/**
	 * @var null|string
	 *
	 * @since    2.0.0-BETA2
	 */
	protected $baseurl = null;

	/**
	 * Construct
	 *
	 * @param   array $config construct
	 *
	 * @throws Exception
	 * @since    2.0.0-BETA2
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);
		$this->baseurl = 'index.php?option=com_kunena';
	}

	/**
	 * On get icons
	 *
	 * Display Kunena updates on dashboard
	 *
	 * @return array|null|string
	 *
	 * @throws Exception
	 * @since    2.0.0-BETA2
	 */
	public static function onGetIcons()
	{
		$updateInfo = null;

		if (KunenaForum::installed() && Factory::getUser()->authorise('core.manage', 'com_installer'))
		{
			$updateSite = 'https://update.kunena.org/%';
			$db         = Factory::getDbo();

			$query = $db->getQuery(true)
				->select('*')
				->from($db->quoteName('#__updates'))
				->where($db->quoteName('extension_id') . ' > 0')
				->where($db->quoteName('detailsurl') . ' LIKE ' . $db->quote($updateSite));
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
					->from($db->quoteName('#__update_sites'))
					->where($db->quoteName('enabled') . ' = 0')
					->where($db->quoteName('location') . ' LIKE ' . $db->quote($updateSite));
				$db->setQuery($query);
				$updateInfo = !$db->loadResult();
			}
		}

		if (!empty($updateInfo->version) && version_compare(KunenaForum::version(), $updateInfo->version, '<'))
		{
			// Has updates
			Factory::getApplication()->enqueueMessage(Text::_('Kunena Update Found.  <a class="btn btn-small btn-info" href="index.php?option=com_installer&view=update&filter_search=kunena"> Update Now</a><br/> Please backup before updating.'), 'Notice');
			$icon = 'media/kunena/images/icons/icon-48-kupdate-update-white.png';
			$link = 'index.php?option=com_installer&view=update&filter_search=kunena';
		}
		elseif (!empty($updateInfo->addons))
		{
			// Has updated add-ons
			Factory::getApplication()->enqueueMessage(Text::_('Kunena Update Found.  <a class="btn btn-small btn-info" href="index.php?option=com_installer&view=update&filter_search=kunena"> Update Now</a><br/> Please backup before updating.'), 'Notice');
			$icon = 'media/kunena/images/icons/icon-48-kupdate-update-white.png';
			$link = 'index.php?option=com_installer&view=update&filter_search=kunena';
		}
		else
		{
			// Already in the latest release
			$icon = 'media/kunena/images/icons/icon-48-kupdate-good-white.png';
			$link = '#';
		}

		return '<a href="' . $link . '"><img src="' . Uri::root() . $icon . '"/></a>';
	}

	/**
	 * Display
	 *
	 * @param   bool $cachable  cachable
	 * @param   bool $urlparams urlparams
	 *
	 * @return \Joomla\CMS\MVC\Controller\BaseController|void
	 *
	 * @throws Exception
	 * @since    2.0.0-BETA2
	 * @throws null
	 */
	public function display($cachable = false, $urlparams = false)
	{
		$db = Factory::getDbo();

		// Enable Kunena updates if they were disabled (but only every 6 hours or logout/login).
		$now       = time();
		$timestamp = $this->app->getUserState('pkg_kunena.updateCheck', 0);

		if ($timestamp < $now)
		{
			$query = $db->getQuery(true)
				->update($db->quoteName('#__update_sites'))
				->set($db->quoteName('enabled') . '=1')
				->where($db->quoteName('location') . ' LIKE ' . $db->quote('https://update.kunena.org/%'));
			$db->setQuery($query);
			$db->execute();

			$this->app->setUserState('pkg_kunena.updateCheck', $now + 60 * 60 * 6);
		}

		parent::display($cachable, $urlparams);
	}
}
