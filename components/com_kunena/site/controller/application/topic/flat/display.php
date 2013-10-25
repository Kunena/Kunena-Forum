<?php
/**
 * Kunena Component
 * @package Kunena.Site
 * @subpackage Controllers.Topic
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/**
 * Class ComponentKunenaControllerApplicationTopicFlatDisplay
 */
class ComponentKunenaControllerApplicationTopicFlatDisplay extends KunenaControllerApplicationDisplay
{
	public function exists() {
		$this->page = KunenaLayoutPage::factory("{$this->input->getCmd('view')}/default");
		return (bool) $this->page->getPath();
	}

	/*
	 * Redirect unread layout to the page that contains the first unread message.
	 */
	protected function before()
	{
		$layout = $this->input->getWord('layout');
		KunenaUserHelper::getMyself()->setTopicLayout($layout);

		parent::before();
	}
}
