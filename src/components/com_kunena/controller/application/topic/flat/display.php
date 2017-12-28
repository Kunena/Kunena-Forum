<?php
/**
 * Kunena Component
 * @package     Kunena.Site
 * @subpackage  Controller.Application
 *
 * @copyright   (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license     https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die;

/**
 * Class ComponentKunenaControllerApplicationTopicFlatDisplay
 *
 * @since  K4.0
 */
class ComponentKunenaControllerApplicationTopicFlatDisplay extends KunenaControllerApplicationDisplay
{
	/**
	 * Return true if layout exists.
	 *
	 * @return boolean
	 */
	public function exists()
	{
		$this->page = KunenaLayoutPage::factory("{$this->input->getCmd('view')}/default");

		return (bool) $this->page->getPath();
	}

	/**
	 * Change topic layout to flat.
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
