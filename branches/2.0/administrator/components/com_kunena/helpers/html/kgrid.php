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

// Load the Kunena Grid Stylesheet and Behaviors.
JHTML::_('behavior.tooltip');
JHTML::stylesheet('grid.css', 'administrator/components/com_kunena/media/css/');
JHTML::script('grid.js', 'administrator/components/com_kunena/media/js/');

/**
 * Kunena HTML grid class.
 *
 * @package		Kunena
 * @subpackage	com_kunena
 * @version		1.0
 */
class JHtmlKGrid
{
	/**
	 * Display a boolean setting widget.
	 *
	 * @static
	 * @param	integer	The row index.
	 * @param	integer	The value of the boolean field.
	 * @param	string	Task to turn the boolean setting on.
	 * @param	string	Task to turn the boolean setting off.
	 * @return	string	The boolean setting widget.
	 * @since	1.0
	 */
	function boolean($i, $value, $taskOn, $taskOff)
	{
		// Build the title.
		$title = ($value) ? JText::_('Yes') : JText::_('No');
		$title .= '::'.JText::_('KUNENA_CLICK_TO_TOGGLE');

		// Build the <a> tag.
		$bool = ($value) ? 'true' : 'false';
		$task = ($value) ? $taskOff : $taskOn;
		$html = '<a class="grid_'.$bool.' hasTip" title="'.$title.'" rel="{id:\'cb'.$i.'\', task:\''.$task.'\'}" href="#toggle"></a>';

		return $html;
	}

	/**
	 * Display the checked out icon
	 *
	 * @param	string	The editor name
	 * @param	string	The checked out time
	 *
	 * @return	string
	 */
	function checkedout($editor, $time)
	{
		$text = addslashes(htmlspecialchars($editor));
		$date = JHTML::_('date', $time, '%A, %d %B %Y');
		$time = JHTML::_('date', $time, '%H:%M');

		$hover = '<span class="editlinktip hasTip" title="'.JText::_('Checked Out').'::'.$text.'<br />'.$date.'<br />'.$time.'">';
		$checked = $hover.'<img src="components/com_kunena/media/images/checked_out.png" alt="" /></span>';

		return $checked;
	}

	/**
	 * Return the icon to move an item UP
	 *
	 * @access	public
	 * @param	int		$i The row index
	 * @param	boolean	$condition True to show the icon
	 * @param	string	$task The task to fire
	 * @param	string	$alt The image alternate text string
	 * @return	string	Either the icon to move an item up or a space
	 * @since	1.0
	 */
	function orderUpIcon($i, $n, $pagination, $task = 'orderup', $alt = 'Move Up', $enabled = true)
	{
		$alt = JText::_($alt);

		if($enabled) {
			$html = '<a class="move_up" title="'.$alt.'" rel="{id:\'cb'.$i.'\', task:\''.$task.'\'}" href="#move_up"></a>';
		} else {
			$html = '<span class="move_up"></span>';
		}

		return $html;
	}

	/**
	 * Return the icon to move an item DOWN
	 *
	 * @access	public
	 * @param	int		$i The row index
	 * @param	int		$n The number of items in the list
	 * @param	boolean	$condition True to show the icon
	 * @param	string	$task The task to fire
	 * @param	string	$alt The image alternate text string
	 * @return	string	Either the icon to move an item down or a space
	 * @since	1.0
	 */
	function orderDownIcon($i, $n, $pagination, $task = 'orderdown', $alt = 'Move Down', $enabled = true)
	{
		$alt = JText::_($alt);

		if($enabled) {
			$html = '<a class="move_down" title="'.$alt.'" rel="{id:\'cb'.$i.'\', task:\''.$task.'\'}" href="#move_down"></a>';
		} else {
			$html = '<span class="move_down"></span>';
		}

		return $html;
	}

	/**
	 * Display the published setting widget.
	 *
	 * @static
	 * @param	integer	The row index.
	 * @param	integer	The value of the published field.
	 * @param	string	Task prefix.
	 * @return	string	The published setting widget.
	 * @since	1.0
	 */
	function published($i, $value, $prefix = '')
	{
		// Initialize variables based on value.
		switch ($value)
		{
			case -2:
				$state = 'trash';
				$task = $prefix.'unpublish';
				$title	= JText::_('Trash');
				break;

			case 0:
				$state = 'false';
				$task = $prefix.'publish';
				$title	= JText::_('Unpublished');
				break;

			case 1:
				$state = 'true';
				$task = $prefix.'unpublish';
				$title	= JText::_('Published');
				break;
		}

		// Build the title.
		$title .= '::'.JText::_('KUNENA_CLICK_TO_TOGGLE');

		// Build the <a> tag.
		$html = '<a class="grid_'.$state.' hasTip" title="'.$title.'" rel="{id:\'cb'.$i.'\', task:\''.$task.'\'}" href="#toggle"></a>';

		return $html;
	}
}
