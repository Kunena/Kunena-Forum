<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Controllers
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
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
	 * @param   array $config config
	 *
	 * @throws Exception
	 * @since Kunena
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);
	}

	/**
	 * @since Kunena
	 */
	public function results()
	{
		$model = $this->getModel('Search');
		$this->setRedirect(
			$model->getSearchURL(
				'search', $model->getState('searchwords'),
				$model->getState('list.start'), $model->getState('list.limit'), $model->getUrlParams(), false
			)
		);
	}
}
