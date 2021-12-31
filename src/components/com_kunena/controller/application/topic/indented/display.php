<?php
/**
 * Kunena Component
 * @package         Kunena.Site
 * @subpackage      Controller.Application
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die;

/**
 * Class ComponentKunenaControllerApplicationTopicIndentedDisplay
 *
 * @since  K4.0
 */
class ComponentKunenaControllerApplicationTopicIndentedDisplay extends KunenaControllerApplicationDisplay
{
	/**
	 * Return true if layout exists.
	 *
	 * @return boolean
	 * @throws Exception
	 * @since Kunena
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
	 * @throws Exception
	 * @since Kunena
	 * @throws null
	 */
	protected function before()
	{
		$layout = $this->input->getWord('layout');
		KunenaUserHelper::getMyself()->setTopicLayout($layout);

		parent::before();
	}
}
