<?php
/**
 * @version		$Id: view.raw.php 994 2009-08-16 08:18:03Z fxstein $
 * @package		Kunena
 * @subpackage	com_kunena
 * @copyright	Copyright (C) 2008 - 2009 Kunena Team. All rights reserved.
 * @license		GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
 * @link		http://www.kunena.com
 */

defined('_JEXEC') or die;

kimport('application.view');
kimport('html.bbcode');

/**
 * The Raw Kunena recent view.
 *
 * @package		Kunena
 * @subpackage	com_kunena
 * @since		1.6
 */
class KunenaViewMessages extends KView
{
	/**
	 * Display the view.
	 *
	 * @return	void
	 * @since	1.6
	 */
	public function display($tpl = null)
	{
		$this->assign('total', $this->get('Total'));
	    $this->assignRef('pagination', $this->get('Pagination'));

		$bbcode = KBBCode::getInstance();

	    $items = $this->get('Items');
		foreach($items as &$item)
	    {
	        $item->message = $bbcode->Parse(stripslashes($item->message));

	    }
	    $this->assignRef('messages', $items);

	    $this->assignRef('announcements', $this->get('Announcement'));
	    $this->assignRef('statistics', $this->get('Statistics'));
	    
	    parent::display($tpl);
	    //echo "<code>"; print_r($items); echo "</code>";
	}
}