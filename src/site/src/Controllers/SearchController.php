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

namespace Kunena\Forum\Site\Controllers;

\defined('_JEXEC') or die();

use Exception;
use Kunena\Forum\Libraries\Controller\KunenaController;

/**
 * Kunena Search Controller
 *
 * @since   Kunena 2.0
 */
class SearchController extends KunenaController
{
	/**
	 * @param   array  $config  config
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public function __construct($config = [])
	{
		parent::__construct($config);
	}

	/**
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	public function results()
	{
		$model = $this->getModel('Search', 'Kunena\Forum\Site\\');
		$this->setRedirect(
			$model->getSearchURL(
				'search',
				$model->getState('searchwords'),
				$model->getState('list.start'),
				$model->getState('list.limit'),
				$model->getUrlParams(),
				false
			)
		);
	}

	/**
	 * Custom getModel() else it want to load from LegacyModelLoaderTrait
	 *
	 * @see     \Joomla\CMS\MVC\Controller\BaseController::getModel()
	 *
	 * @since   Kunena 6.0
	 */
	public function getModel($name = '', $prefix = '', $config = [])
	{
		$className = $prefix . 'Model\\' . ucfirst($name) . 'Model';

		$model = new $className($config);

		return $model;
	}
}
