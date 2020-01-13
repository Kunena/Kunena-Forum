<?php
/**
 * @package     Forum.Administrator
 * @subpackage  com_kunena
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Kunena\Forum\Administrator\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\FormController;

/**
 * Controller for a single forum
 *
 * @since  1.6
 */
class ForumController extends FormController
{
	public function onAfterInitialise()
	{
		\JLoader::registerNamespace('Kunena\Forum\Libraries', JPATH_LIBRARIES . '/kunena/', true,false,'psr4');
	}
}
