<?php
/**
 * Kunena Component
 * @package Kunena.Site
 * @subpackage Controllers.Search
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/**
 * Class ComponentKunenaControllerSearchFormDisplay
 */
class ComponentKunenaControllerSearchFormDisplay extends KunenaControllerDisplay
{
	protected $name = 'Search/Form';

	/** @var KunenaModelSearch */
	public $model;

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

	protected function prepareDocument()
	{
		$this->setTitle(JText::_('COM_KUNENA_SEARCH_ADVSEARCH'));
	}
}
