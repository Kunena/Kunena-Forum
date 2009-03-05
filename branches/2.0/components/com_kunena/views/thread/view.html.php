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
 * The HTML Kunena thread view
 *
 * @package		Kunena
 * @subpackage	com_kunena
 * @version		1.0
 */
class KunenaViewThread extends KunenaView
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
		$this->assignRef('posts', $this->get('Posts'));
		$this->assignRef('totalPosts', $this->get('PostsTotal'));
		$this->assignRef('thread', $this->get('Thread'));

		// import library dependencies
		jximport('jxtended.html.bbcode');

		// instantiate bbcode parser object
		$parser = &JXBBCode::getInstance(array(
			'smiley_path' => JPATH_ROOT.'/media/jxtended/img/smilies/default',
			'smiley_url' => JURI::base().'media/jxtended/img/smilies/default'
		));
		$this->assignRef('parser', $parser);

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
		$pathway->addItem($this->thread->subject, JRoute::_('index.php?option=com_kunena&view=category&cat_id='.$this->category->id.':'.$this->category->path.'&thread_id='.$this->thread->id));

		parent::display($tpl);
	}
}