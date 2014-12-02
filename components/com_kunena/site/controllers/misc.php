<?php
/**
 * Kunena Component
 * @package Kunena.Site
 * @subpackage Controllers
 *
 * @copyright (C) 2008 - 2014 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/**
 * Kunena Misc Controller
 *
 * @since		2.0
 */
class KunenaControllerMisc extends KunenaController {
	public function __construct($config = array()) {
		parent::__construct($config);
	}

	public function template() {
		jimport('joomla.filesystem.path');
		$name = JRequest::getString ( 'name', JRequest::getString ( 'kunena_template', '', 'COOKIE' ) );
		if ($name) {
			$name = JPath::clean($name);
			if (!is_readable ( KPATH_SITE . "/template/{$name}/template.xml" )) {
				$name = 'blue_eagle';
			}
			setcookie ( 'kunena_template', $name, 0, JUri::root(true).'/' );
		} else {
			setcookie ( 'kunena_template', null, time () - 3600, JUri::root(true).'/' );
		}
		$this->setRedirect ( KunenaRoute::_('index.php?option=com_kunena', false) );
	}
}
