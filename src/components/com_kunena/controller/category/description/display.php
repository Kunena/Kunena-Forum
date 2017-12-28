<?php
/**
 * Kunena Component
 * @package     Kunena.Site
 * @subpackage  Controller.Category
 *
 * @copyright   (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license     https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die;

/**
 * Class ComponentKunenaControllerApplicationMiscDisplay
 *
 * @since  K4.0
 */
class ComponentKunenaControllerCategoryDescriptionDisplay extends KunenaControllerDisplay
{
	protected $name = 'Category/Description';

	/**
	 * Prepare category display.
	 *
	 * @return void
	 *
	 * @throws KunenaExceptionAuthorise
	 */
	protected function before()
	{
		parent::before();

		require_once KPATH_SITE . '/models/category.php';

		$catid = $this->input->getInt('catid');

		$this->category = KunenaForumCategoryHelper::get($catid);
		$this->category->tryAuthorise();
	}
}
