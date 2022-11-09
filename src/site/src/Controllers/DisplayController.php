<?php

/**
 * @package     Kunena.Site
 * @subpackage  com_kunena
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Kunena\Forum\Site\Controllers;

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Kunena\Forum\Libraries\Controller\KunenaController;

/**
 * Kunena Component Controller
 *
 * @since   Kunena 6.0
 */
class DisplayController extends KunenaController
{
    protected $default_view = 'Kunena\Forum\Site\Controller\Display';

    protected $prefix = 'site';

    protected $layout = 'default';

    /**
     * Constructor.
     *
     * @param   array                     $config   An optional associative array of configuration settings.
     *                                              Recognized key values include 'name', 'default_task', 'model_path', and
     *                                              'view_path' (this list is not meant to be comprehensive).
     * @param   MVCFactoryInterface|null  $factory  The factory.
     * @param   null                      $app      The JApplication for the dispatcher
     * @param   null                      $input    Input
     *
     * @throws \Exception
     * @since   3.0.1
     */
    public function __construct($config = [], MVCFactoryInterface $factory = null, $app = null, $input = null)
    {
        $this->input = Factory::getApplication()->input;

        parent::__construct($config, $factory, $app, $input);
    }

    /**
     * Method to display a view.
     *
     * @param   boolean  $cachable   If true, the view output will be cached.
     * @param   boolean  $urlparams  An array of safe URL parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
     *
     * @return  KunenaController  This object to support chaining.
     *
     * @throws \Exception
     * @since   1.5
     */
    public function display($cachable = false, $urlparams = false): KunenaController
    {
        try {
            return parent::display($cachable, $urlparams);
        } catch (\Exception $e) {
            $trace = $e->getTrace();

            $thrower = $trace[0];

            // If this is the "view not found" error, the thrower is JControllerLegacy::getView(); standardize to lowercased strings just in case
            if (strtolower($thrower['class']) === 'basecontroller' && strtolower($thrower['function']) === 'getview') {
                throw new \Exception(Text::_('JERROR_LAYOUT_REQUESTED_RESOURCE_WAS_NOT_FOUND'), 404, $e);
            }

            // Some other error, just let it bubble up
            throw $e;
        }
    }
}
