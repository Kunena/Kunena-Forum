<?php
/**
 * Kunena QuickIcon Plugin
 * @package Kunena.Plugins
 * @subpackage QuickIcon
 *
 * @copyright (C) 2008 - 2012 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

class plgQuickiconKunena extends JPlugin {

	function __construct(& $subject, $config) {
		// Do not load if Kunena version is not supported or KunenaForum isn't detected
		if (!(class_exists('KunenaForum'))) return false;

		$this->loadLanguage('plg_quickicon_kunena.sys', JPATH_ADMINISTRATOR) || $this->loadLanguage('plg_quickicon_kunena.sys', KPATH_ADMIN);

		parent::__construct ( $subject, $config );
	}

	/**
	 * Display Kunena backend icon in Joomla 2.5+
	 *
	 * @param string $context
	 */
	public function onGetIcons($context) {
		if (!$context == 'mod_quickicon' || !JFactory::getUser()->authorise('core.manage', 'com_kunena')) {
			return;
		}
		KunenaFactory::loadLanguage('com_kunena.sys', 'admin');

		if (KunenaForum::installed() && KunenaFactory::getConfig()->version_check && JFactory::getUser()->authorise('core.manage', 'com_installer')) {
			$db = JFactory::getDbo();
			$query = $db->getQuery(true)
				->select($db->qn('params'))
				->from($db->qn('#__extensions'))
				->where($db->qn('type').' = '.$db->q('component'))
				->where($db->qn('element').' = '.$db->q('com_kunena'));
			$db->setQuery($query);
			$cparams = new JRegistry((string) $db->loadResult());

			//$cparams = JComponentHelper::getParams('com_kunena');
			$liveupdate = new JRegistry($cparams->get('liveupdate', null));
			$lastCheck = $liveupdate->get('lastcheck', 0);
			$updateInfo = json_decode(trim((string) $liveupdate->get('updatedata', ''), '"'));
			$valid = abs(time() - $lastCheck) <= 24 * 3600; // 24 hours

			if (!$valid) {
				// If information is not valid, update it asynchronously.
				$ajax_url = json_encode(JURI::base().'index.php?option=com_kunena&view=liveupdate&task=ajax');
				$script = "window.addEvent('domready', function() {
	var com_kunena_updatecheck_ajax_structure = {
		onSuccess: function(msg, responseXML) {
			var updateInfo = JSON.decode(msg, true);
			if (updateInfo.html) {
				document.id('com_kunena_icon').getElement('img').setProperty('src',updateInfo.img);
				document.id('com_kunena_icon').getElement('span').set('html', updateInfo.html);
				document.id('com_kunena_icon').getElement('a').set('href', updateInfo.link);
			}
		},
		url: {$ajax_url}
	};
	ajax_object = new Request(com_kunena_updatecheck_ajax_structure);
	ajax_object.send();
});";

				$document = JFactory::getDocument();
				$document->addScriptDeclaration($script);
			}
		}

		$link = 'index.php?option=com_kunena';

		if(!KunenaForum::installed()) {
			// Not fully installed
			$img = 'kunena/icons/icon-48-kupdate-alert-white.png';
			$text = JText::_('PLG_QUICKICON_KUNENA_COMPLETE_INSTALLATION');

		} elseif (empty($updateInfo->supported)) {
			// Unsupported
			$img = 'kunena/icons/kunena-logo-48-white.png';
			$text = JText::_('COM_KUNENA');

		} elseif ($updateInfo->stuck) {
			// Stuck
			$img = 'kunena/icons/icon-48-kupdate-alert-white.png';
			$text = JText::_('COM_KUNENA') . '<br />' . JText::_('PLG_QUICKICON_KUNENA_UPDATE_CRASH');

		} elseif (version_compare(KunenaForum::version(), $updateInfo->version, '<')) {
			// Has updates
			$img = 'kunena/icons/icon-48-kupdate-update-white.png';
			$text = 'Kunena ' . $updateInfo->version . '<br />' . JText::_('PLG_QUICKICON_KUNENA_UPDATE_AVAILABLE');
			$link .= '&view=liveupdate';

		} else {
			// Already in the latest release
			$img = 'kunena/icons/icon-48-kupdate-good-white.png';
			$text = JText::_('COM_KUNENA');
		}

		return array( array(
			'link' => JRoute::_($link),
			'image' => $img,
			'text' => $text,
			'access' => array('core.manage', 'com_kunena'),
			'id' => 'com_kunena_icon' ) );
	}
}