<?php
/**
 * Kunena QuickIcon Plugin
 *
 * @package         Kunena.Plugins
 * @subpackage      QuickIcon
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

defined('_JEXEC') or die();

use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\CMSPlugin;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Forum\KunenaForum;

/**
 * Class plgQuickiconKunena
 *
 * @since  Kunena 6.0
 */
class plgQuickiconKunena extends CMSPlugin
{
	/**
	 * Load the language file on instantiation.
	 *
	 * @var    boolean
	 * @since  3.1
	 */
	protected $autoloadLanguage = true;

	/**
	 * Application object.
	 *
	 * @var    CMSApplication
	 * @since  3.7.0
	 */
	protected $app;

	/**
	 * Display Kunena backend icon in Joomla 4.0
	 *
	 * @param   string  $context  context
	 *
	 * @return array
	 * @since   Kunena 6.0
	 *
	 * @throws \Exception
	 */
	public function onGetIcons(string $context): array
	{
		if ($context != $this->params->get('context', 'mod_quickicon') || !$this->app->getIdentity()->authorise('core.manage', 'com_kunena'))
		{
			return [];
		}

		KunenaFactory::loadLanguage('com_kunena.sys', 'admin');

		$updateInfo = null;

		if (KunenaForum::installed() && $this->app->getIdentity()->authorise('core.manage', 'com_installer'))
		{
			$updateSite = 'https://update.kunena.org/%';
			$db         = Factory::getContainer()->get('DatabaseDriver');

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

		$link = 'index.php?option=com_kunena';

		$useIcons = version_compare(JVERSION, '3.0', '>');

		if (!KunenaForum::installed())
		{
			$icon = 'fas fa-exclamation-triangle';

			// Not fully installed
			$img   = $useIcons ? $icon : 'kunena/icons/icon-48-kupdate-alert-white.png';
			$icon  = 'kunena/icons/icon-48-kupdate-alert-white.png';
			$text  = Text::_('PLG_QUICKICON_KUNENA_COMPLETE_INSTALLATION');
			$class = 'pulse warning';
		}
		elseif ($updateInfo === null)
		{
			// Unsupported
			$icon = 'fas fa-remove';

			$img   = $useIcons ? $icon : 'kunena/icons/kunena-logo-48-white.png';
			$icon  = 'kunena/icons/kunena-logo-48-white.png';
			$text  = Text::_('COM_KUNENA');
			$class = 'pulse error';
		}
		elseif ($updateInfo === false)
		{
			// Disabled
			$icon = 'fas fa-minus-circle';

			$img   = $useIcons ? $icon : 'kunena/icons/icon-48-kupdate-alert-white.png';
			$icon  = 'kunena/icons/icon-48-kupdate-alert-white.png';
			$text  = Text::_('COM_KUNENA') . '<br />' . Text::_('PLG_QUICKICON_KUNENA_UPDATE_DISABLED');
			$class = 'pulse error';
		}
		elseif (!empty($updateInfo->version) && version_compare(KunenaForum::version(), $updateInfo->version, '<'))
		{
			// Has updates
			$icon = 'fas fa-download';

			$img   = $useIcons ? $icon : 'kunena/icons/icon-48-kupdate-update-white.png';
			$icon  = 'kunena/icons/icon-48-kupdate-update-white.png';
			$text  = 'Kunena ' . $updateInfo->version . '<br />' . Text::_('PLG_QUICKICON_KUNENA_UPDATE_NOW');
			$link  = 'index.php?option=com_installer&view=update&filter_search=kunena';
			$class = 'pulse warning';
		}
		elseif (!empty($updateInfo->addons))
		{
			// Has updated add-ons
			$icon = 'fas fa-download';

			$img   = $useIcons ? $icon : 'kunena/icons/icon-48-kupdate-update-white.png';
			$icon  = 'kunena/icons/icon-48-kupdate-update-white.png';
			$text  = Text::_('COM_KUNENA') . '<br />' . Text::sprintf('PLG_QUICKICON_KUNENA_UPDATE_ADDONS', $updateInfo->addons);
			$link  = 'index.php?option=com_installer&view=update&filter_search=kunena';
			$class = 'pulse warning';
		}
		else
		{
			$icon = 'fas fa-comments';

			$img   = $useIcons ? $icon : 'kunena/icons/icon-48-kupdate-good-white.png';
			$icon  = 'kunena/icons/icon-48-kupdate-good-white.png';
			$text  = Text::_('COM_KUNENA');
			$class = 'pulse success';
		}

		// Use one line in J!4.0.
		$text = preg_replace('|<br />|', ' - ', $text);

		return [
			[
				'link'  => $link,
				'image' => $img,
				'icon'  => $icon,
				'text'  => $text,
				'class' => $class,
				'id'    => 'plg_quickicon_kunena',
				'group' => 'update_quickicon',
			],
		];
	}
}
