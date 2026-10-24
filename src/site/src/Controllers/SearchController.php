<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Controllers
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site\Controllers;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
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
     * @param   string  $name    The name of the model.
     * @param   string  $prefix  The prefix of the class of the model.
     * @param   array   $config  The configuration of the model.
     *
     * @return  BaseDatabaseModel
     *
     * @see     BaseController::getModel()
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
