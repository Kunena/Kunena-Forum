<?php
/**
 * @package     Kunena.Site
 * @subpackage  com_kunena
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Kunena\Forum\Site\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;

/**
 * Kunena Component Controller
 *
 * @since   Kunena 6.0
 */
class DisplayController extends BaseController
{
	/**
	 * Constructor.
	 *
	 * @param   array                $config   An optional associative array of configuration settings.
	 *                                         Recognized key values include 'name', 'default_task', 'model_path', and
	 *                                         'view_path' (this list is not meant to be comprehensive).
	 * @param   MVCFactoryInterface  $factory  The factory.
	 * @param   CMSApplication       $app      The JApplication for the dispatcher
	 * @param   \JInput              $input    Input
	 *
	 * @since   3.0.1
	 */
	public function __construct($config = array(), MVCFactoryInterface $factory = null, $app = null, $input = null)
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
	 * @return  BaseController  This object to support chaining.
	 *
	 * @since   1.5
	 */
	public function display($cachable = false, $urlparams = false)
	{
		try
		{
			return parent::display($cachable, $urlparams);
		}
		catch (\Exception $e)
		{
			$trace = $e->getTrace();

			$thrower = $trace[0];

			// If this is the "view not found" error, the thrower is JControllerLegacy::getView(); standardize to lowercased strings just in case
			if (strtolower($thrower['class']) === 'basecontroller' && strtolower($thrower['function']) === 'getview')
			{
				throw new \Exception(Text::_('JERROR_LAYOUT_REQUESTED_RESOURCE_WAS_NOT_FOUND'), 404, $e);
			}

			// Some other error, just let it bubble up
			throw $e;
		}
	}
}
