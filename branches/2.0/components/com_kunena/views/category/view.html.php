<?php
/**
 * @version		$Id$
 * @package		Kunena
 * @subpackage	com_kunena
 * @copyright	(C) 2008 Kunena. All rights reserved, see COPYRIGHT.php
 * @license		GNU General Public License, see LICENSE.php
 * @link		http://www.kunena.com
 */

defined('_JEXEC') or die('Invalid Request.');

require_once (JPATH_COMPONENT.'/libraries/view.php');

/**
 * The HTML Kunena category view
 *
 * @package		Kunena
 * @subpackage	com_kunena
 * @version		1.0
 */
class KunenaViewCategory extends KunenaView
{
	/**
	 * Display the view
	 *
	 * @access	public
	 */
	function display($tpl = null)
	{
		$state = $this->get('state');

		// assign some variables to the view
		$this->assignRef('params', $state->get('params'));
		$this->assignRef('user', JFactory::getUser());
		$this->assignRef('category', $this->get('Category'));
		$this->assignRef('threads', $this->get('Threads'));
		$this->assignRef('totalThreads', $this->get('ThreadsTotal'));
		$this->assignRef('lastReplies', $this->get('LastReplies'));

		// Get the category paths for the system pathway object.
		$app = & JFactory::getApplication();
		$pathway = & $app->getPathWay();

		$paths = $this->get('CategoryPathsToRoot');
		if ($paths)
		{
			foreach ($paths as $path)
			{
				$pathway->addItem($path->title, JRoute::_('index.php?option=com_kunena&view=category&cat_id='.$path->id.':'.$path->path));
			}
		}
		$pathway->addItem($this->category->title, JRoute::_('index.php?option=com_kunena&view=category&cat_id='.$this->category->id.':'.$this->category->path));

		parent::display($tpl);
	}
}