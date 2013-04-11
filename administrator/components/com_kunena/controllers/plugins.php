<?php
/**
 * Kunena Component
 * @package Kunena.Administrator
 * @subpackage Controllers
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/**
 * Kunena Plugins Controller
 *
 * @since 2.0
 */
class KunenaAdminControllerPlugins extends KunenaController {
	protected $baseurl = null;

	public function __construct($config = array()) {
		parent::__construct($config);
		$this->baseurl = 'administrator/index.php?option=com_kunena&view=plugins';
		$this->baseurl2 = 'administrator/index.php?option=com_kunena&view=plugins';
	}

	function edit() {
		KunenaFactory::loadLanguage('com_kunena', 'admin');

		if (! JSession::checkToken('post')) {
			$this->app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$this->redirectBack();
		}

		$cid = JRequest::getVar ( 'cid', array (), 'post', 'array' );
		$id = array_shift($cid);
		if (!$id) {
			$this->app->enqueueMessage ( JText::_ ( 'COM_KUNENA_A_NO_CATEGORIES_SELECTED' ), 'notice' );
			$this->redirectBack();
		} else {
			$this->setRedirect(JRoute::_("index.php?option=com_kunena&view=plugins&layout=edit&extension_id={$id}", false));
		}
	}
}
