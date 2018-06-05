<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Layout.Category.Manage
 *
 * @copyright   (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die;

/**
 * KunenaLayoutCategoryManage
 *
 * @since  K5.1
 */
class KunenaLayoutCategoryManage extends KunenaLayout
{
	/**
	 * Method to return a KunenaPagination object
	 *
	 * @param   int $maxpages Maximum that are allowed for pagination
	 *
	 * @return KunenaPagination
	 * @since Kunena 5.1
	 */
	public function getPaginationObject($maxpages)
	{
		$pagination = new KunenaPagination($this->total, $this->state->get('list.start'), $this->state->get('list.limit'));
		$pagination->setDisplayedPages($maxpages);

		return $pagination;
	}
}
