<?php
/**
 * Kunena QuickIcon Plugin
 * @package Kunena.Plugins
 * @subpackage QuickIcon
 *
 * @copyright (C) 2008 - 2014 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/**
 * Class plgQuickiconKunena
 */
class plgQuickiconKunena extends JPlugin {

	public function __construct(& $subject, $config) {
		// Do not load if Kunena version is not supported or KunenaForum isn't detected
		if (!class_exists('KunenaForum')) return;

		parent::__construct ( $subject, $config );

		// ! Always load language after parent::construct else the name of plugin isn't yet set
		$this->loadLanguage('plg_quickicon_kunena.sys');
	}

	/**
	 * Display Kunena backend icon in Joomla 2.5+
	 *
	 * @param string $context
	 * @return array|null
	 */
	public function onGetIcons($context) {
		if ($context != $this->params->get('context', 'mod_quickicon') || !JFactory::getUser()->authorise('core.manage', 'com_kunena')) {
			return null;
		}
		KunenaFactory::loadLanguage('com_kunena.sys', 'admin');

		$updateInfo = null;
		if (KunenaForum::installed() && JFactory::getUser()->authorise('core.manage', 'com_installer')) {
			$updateSite = 'http://update.kunena.org/%';
			$db = JFactory::getDbo();

			$query = $db->getQuery(true)
				->select('*')
				->from($db->qn('#__updates'))
				->where($db->qn('extension_id').' > 0')
				->where($db->qn('detailsurl').' LIKE '.$db->q($updateSite));
			$db->setQuery($query);
			$list = (array) $db->loadObjectList();

			if ($list) {
				$updateInfo = new stdClass();
				$updateInfo->addons = 0;
				$updateInfo->version = 0;
				foreach ($list as $item) {
					if ($item->element == 'pkg_kunena') $updateInfo->version = $item->version;
					else $updateInfo->addons++;
				}

			} else {
				$query = $db->getQuery(true)
					->select('update_site_id')
					->from($db->qn('#__update_sites'))
					->where($db->qn('enabled').' = 0')
					->where($db->qn('location').' LIKE '.$db->q($updateSite));
				$db->setQuery($query);
				$updateInfo = !$db->loadResult();
			}
		}

		$link = 'index.php?option=com_kunena';

		$useIcons = version_compare(JVERSION, '3.0', '>');

		if(!KunenaForum::installed()) {
			// Not fully installed
			$img = $useIcons ? 'warning' : 'kunena/icons/icon-48-kupdate-alert-white.png';
			$icon = 'kunena/icons/icon-48-kupdate-alert-white.png';
			$text = JText::_('PLG_QUICKICON_KUNENA_COMPLETE_INSTALLATION');

		} elseif ($updateInfo === null) {
			// Unsupported
			$img = $useIcons ? 'remove' : 'kunena/icons/kunena-logo-48-white.png';
			$icon = 'kunena/icons/kunena-logo-48-white.png';
			$text = JText::_('COM_KUNENA');

		} elseif ($updateInfo === false) {
			// Disabled
			$img = $useIcons ? 'minus' : 'kunena/icons/icon-48-kupdate-alert-white.png';
			$icon = 'kunena/icons/icon-48-kupdate-alert-white.png';
			$text = JText::_('COM_KUNENA') . '<br />' . JText::_('PLG_QUICKICON_KUNENA_UPDATE_DISABLED');

		} elseif (!empty($updateInfo->version) && version_compare(KunenaForum::version(), $updateInfo->version, '<')) {
			// Has updates
			$img = $useIcons ? 'download' : 'kunena/icons/icon-48-kupdate-update-white.png';
			$icon = 'kunena/icons/icon-48-kupdate-update-white.png';
			$text = 'Kunena ' . $updateInfo->version . '<br />' . JText::_('PLG_QUICKICON_KUNENA_UPDATE_NOW');
			$link = 'index.php?option=com_installer&view=update&filter_search=kunena';

		} elseif (!empty($updateInfo->addons)) {
			// Has updated add-ons
			$img = $useIcons ? 'download' : 'kunena/icons/icon-48-kupdate-update-white.png';
			$icon = 'kunena/icons/icon-48-kupdate-update-white.png';
			$text = JText::_('COM_KUNENA') . '<br />' . JText::sprintf('PLG_QUICKICON_KUNENA_UPDATE_ADDONS', $updateInfo->addons);
			$link = 'index.php?option=com_installer&view=update&filter_search=kunena';

		} else {
			// Already in the latest release
			$img = $useIcons ? 'comments' : 'kunena/icons/icon-48-kupdate-good-white.png';
			$icon = 'kunena/icons/icon-48-kupdate-good-white.png';
			$text = JText::_('COM_KUNENA');
		}

		// Use one line in J!3.0.
		if (version_compare(JVERSION, '3.0', '>')) {
			$text = preg_replace('|<br />|', ' - ', $text);
		}
		return array( array(
			'link' => JRoute::_($link),
			'image' => $img,
			'text' => $text,
			'icon' => $icon,
			'access' => array('core.manage', 'com_kunena'),
			'id' => 'com_kunena_icon' ) );
	}
}
