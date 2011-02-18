<?php
/**
 * @version $Id: kunenacategories.php 4220 2011-01-18 09:13:04Z mahagr $
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

kimport ( 'kunena.controller' );
kimport('kunena.user.helper');
kimport('kunena.forum.category.helper');

/**
 * Kunena Recount Controller
 *
 * @package		Kunena
 * @subpackage	com_kunena
 * @since		1.6
 */
class KunenaAdminControllerRecount extends KunenaController {
	protected $baseurl = null;

	public function __construct($config = array()) {
		parent::__construct($config);

		$app = JFactory::getApplication ();
		KunenaUserHelper::recount();
		KunenaForumCategoryHelper::recount ();

		$app->enqueueMessage (JText::_('COM_KUNENA_RECOUNTFORUMS_DONE'));
	  	$this->redirectBack ();
	}
}
