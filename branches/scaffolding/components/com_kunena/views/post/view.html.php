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
 * The HTML Kunena post view
 *
 * @package		Kunena
 * @subpackage	com_kunena
 * @version		1.0
 */
class KunenaViewPost extends KunenaView
{
	/**
	 * Display the view
	 *
	 * @access	public
	 * @return	void
	 * @since	1.0
	 */
	function display($tpl = null)
	{
		$state	= $this->get('State');
		$cart	= $this->get('Cart');
		$form	= $this->get('Form');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		// Set the form name and action.
		$form->setName('post_form');
		$form->setAction(JRoute::_('index.php?option=com_kunena&task=post.save'));

		$this->assignRef('state',	$state);
		$this->assignRef('cart',	$cart);
		$this->assignRef('form',	$form);

		parent::display($tpl);
	}
}