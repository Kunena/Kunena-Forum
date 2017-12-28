<?php
/**
 * Kunena Component
 *
 * @package     Kunena.Site
 * @subpackage  Controllers
 *
 * @copyright   (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license     https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die();

/**
 * Kunena Search Controller
 *
 * @since  2.0
 */
class KunenaControllerSearch extends KunenaController
{
	/**
	 * @param   array $config
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);
	}

	/**
	 *
	 */
	public function results()
	{
		$model = $this->getModel('Search');
		$this->setRedirect(
			$model->getSearchURL(
			'search', $model->getState('searchwords'),
			$model->getState('list.start'), $model->getState('list.limit'), $model->getUrlParams(), false));
	}
}
