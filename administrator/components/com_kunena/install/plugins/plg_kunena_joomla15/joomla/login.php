<?php
/**
 * Kunena Plugin
 * @package Kunena.Plugins
 * @subpackage Joomla15
 *
 * @copyright (C) 2008 - 2012 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

class KunenaLoginJoomla {
	protected $params = null;

	public function __construct($params) {
		$this->params = $params;
	}

	public function loginUser($username, $password, $rememberme) {
		$credentials = array('username' => $username, 'password' => $password);
		$options = array('remember' => $rememberme);
		$error = JFactory::getApplication()->login ( $credentials, $options );
		return is_bool($error) ? '' : $error;
	}

	public function logoutUser() {
		$error = JFactory::getApplication()->logout ();
		return is_bool($error) ? '' : $error;
	}

	public function getRememberMe() {
		return (bool) JPluginHelper::isEnabled('system', 'remember');
	}

	public function getLoginURL() {
		$Itemid = $this->getRoute('login');
		return JRoute::_('index.php?option=com_user&view=login'.($Itemid ? "&Itemid={$Itemid}" : ''));
	}

	public function getLogoutURL() {
		$Itemid = $this->getRoute('login');
		return JRoute::_('index.php?option=com_user&view=login'.($Itemid ? "&Itemid={$Itemid}" : ''));
	}

	public function getRegistrationURL() {
		$usersConfig = JComponentHelper::getParams ( 'com_users' );
		if ($usersConfig->get ( 'allowUserRegistration' )) {
			$Itemid = $this->getRoute('register');
			return JRoute::_('index.php?option=com_user&view=register'.($Itemid ? "&Itemid={$Itemid}" : ''));
		}
	}

	public function getResetURL() {
		$Itemid = $this->getRoute('reset');
		return JRoute::_('index.php?option=com_user&view=reset'.($Itemid ? "&Itemid={$Itemid}" : ''));
	}

	public function getRemindURL() {
		$Itemid = $this->getRoute('remind');
		return JRoute::_('index.php?option=com_user&view=remind'.($Itemid ? "&Itemid={$Itemid}" : ''));
	}

	private function &getItems() {
		static $items = null;

		// Get the menu items for this component.
		if (!isset($items)) {
			// Include the site app in case we are loading this from the admin.
			require_once JPATH_SITE.'/includes/application.php';

			$app	= JFactory::getApplication();
			$menu	= $app->getMenu();
			$com	= JComponentHelper::getComponent('com_user');
			$items	= $menu->getItems('componentid', $com->id);

			// If no items found, set to empty array.
			if (!$items) {
				$items = array();
			}
		}

		return $items;
	}

	private function getRoute($view) {
		// Get the items.
		$items	= $this->getItems();
		$itemid	= null;

		// Search for a suitable menu id.
		foreach ($items as $item) {
			if (isset($item->query['view']) && $item->query['view'] === $view) {
				$itemid = $item->id;
				break;
			}
		}

		return $itemid;
	}
}

