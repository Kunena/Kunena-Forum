<?php
/**
 * Kunena Component
 * @package     Kunena.Site
 * @subpackage  Controller.Application
 *
 * @copyright   (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        http://www.kunena.org
 **/
defined('_JEXEC') or die;

/**
 * Class ComponentKunenaControllerApplicationTopicIndentedDisplay
 *
 * @since  3.1
 */
class ComponentKunenaControllerApplicationTopicIndentedDisplay extends KunenaControllerApplicationDisplay
{
	/**
	 * Return true if layout exists.
	 *
	 * @return bool
	 */
	public function exists()
	{
		$this->page = KunenaLayoutPage::factory("{$this->input->getCmd('view')}/default");

		return (bool) $this->page->getPath();
	}

	/**
	 * Change topic layout to indented.
	 *
	 * @return void
	 */
	protected function before()
	{
		$layout = $this->input->getWord('layout');
		KunenaUserHelper::getMyself()->setTopicLayout($layout);

		parent::before();
	}
}
