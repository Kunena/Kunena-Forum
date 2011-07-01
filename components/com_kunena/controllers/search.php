<?php
/**
 * @version $Id$
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
 * Kunena Search Controller
 *
 * @package		Kunena
 * @subpackage	com_kunena
 * @since		2.0
 */
class KunenaControllerSearch extends KunenaController {
	public function __construct($config = array()) {
		parent::__construct($config);
		$this->app = JFactory::getApplication ();
	}

	public function results() {
		require_once KPATH_SITE . '/lib/kunena.link.class.php';

		$model = $this->getModel('Search');
		$this->app->redirect ( CKunenaLink::GetSearchURL('advsearch', $model->getState('searchwords'),
			$model->getState('list.start'), $model->getState('list.limit'), $model->getUrlParams(), false) );
	}
}