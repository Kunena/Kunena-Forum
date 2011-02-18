<?php
/**
 * @version $Id: categories.php 4381 2011-02-05 20:55:31Z mahagr $
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

kimport ( 'kunena.controller' );

/**
 * Kunena Createmenu Controller
 *
 * @package		Kunena
 * @subpackage	com_kunena
 * @since		1.6
 */
class KunenaAdminControllerCreatemenu extends KunenaController {
	protected $baseurl = null;

	public function __construct($config = array()) {
		parent::__construct($config);

		$app = JFactory::getApplication ();
		$lang = JFactory::getLanguage();
		// Start by loading English strings and override them by current locale
		$lang->load('com_kunena.install',JPATH_ADMINISTRATOR, 'en-GB');
		$lang->load('com_kunena.install',JPATH_ADMINISTRATOR);

		require_once(KPATH_ADMIN . '/install/model.php');
		$installer = new KunenaModelInstall();
		$installer->deleteMenu();
		$installer->createMenu();

		$app->enqueueMessage ( JText::_('COM_KUNENA_MENU_CREATED') );
		$this->redirectBack ();
	}
}