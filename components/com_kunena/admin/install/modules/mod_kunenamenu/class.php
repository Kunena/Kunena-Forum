<?php
/**
 * Kunena Menu Module
 * @package Kunena.Modules
 * @subpackage Menu
 *
 * @copyright (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined('_JEXEC') or die;

class modKunenaMenu {
	public function __construct($params) {
		$this->parameters = $params;
	}

	function display() {
		$this->list = KunenaMenuHelper::getList($this->parameters);
		$this->app = JFactory::getApplication();
		$this->menu = $this->app->getMenu();
		$this->active = $this->menu->getActive();
		$this->active_id = isset($this->active) ? $this->active->id : $this->menu->getDefault()->id;
		$this->path = isset($this->active) ? $this->active->tree : array();
		$this->showAll = $this->parameters->get('showAllChildren');
		$this->class_sfx = htmlspecialchars($this->parameters->get('class_sfx'), ENT_COMPAT, 'UTF-8');

		if(count($this->list)) {
			require JModuleHelper::getLayoutPath('mod_kunenamenu', $this->parameters->get('layout', 'default'));
		}
	}
}
