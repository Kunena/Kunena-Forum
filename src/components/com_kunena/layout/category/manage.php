<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Layout.Category.Manage
 *
 * @copyright       Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site\Layout\Category;

defined('_JEXEC') or die;

use Kunena\Forum\Libraries\Layout\Layout;
use Kunena\Forum\Libraries\Pagination\Pagination;
use function defined;

/**
 * KunenaLayoutCategoryManage
 *
 * @since  K5.1
 */
class KunenaLayoutCategoryManage extends Layout
{
	/**
	 * @var     integer
	 * @since   Kunena 6.0
	 */
	public $total;

	/**
	 * @var     object
	 * @since   Kunena 6.0
	 */
	public $state;

	/**
	 * Method to return a KunenaPagination object
	 *
	 * @param   integer  $maxpages  Maximum that are allowed for pagination
	 *
	 * @return  Pagination
	 *
	 * @since   Kunena 5.1
	 */
	public function getPaginationObject($maxpages)
	{
		$pagination = new Pagination($this->total, $this->state->get('list.start'), $this->state->get('list.limit'));
		$pagination->setDisplayedPages($maxpages);

		return $pagination;
	}
}
