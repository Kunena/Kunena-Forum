<?php
/**
 * Kunena Component
 * @package Kunena.Site
 * @subpackage Controllers.Misc
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

class ComponentKunenaControllerSearchFormDisplay extends KunenaControllerDisplay
{
	/**
	 * @var KunenaModelSearch
	 */
	public $model;

	protected function display()
	{
		// Display layout with given parameters.
		$content = KunenaLayout::factory('Search/Form')
			->setProperties($this->getProperties());

		return $content;
	}

	protected function before()
	{
		parent::before();

		require_once KPATH_SITE . '/models/search.php';
		$this->model = new KunenaModelSearch();
		$this->state = $this->model->getState();

		$this->me = KunenaUserHelper::getMyself();

		$this->isModerator = ($this->me->isAdmin() || KunenaAccess::getInstance()->getModeratorStatus());
		$this->error = $this->model->getError();
	}
}
