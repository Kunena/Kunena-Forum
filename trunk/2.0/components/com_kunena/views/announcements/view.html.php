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
class KunenaViewAnnouncements extends KView
{
	/**
	 * Display the view.
	 *
	 * @return	void
	 * @since	1.6
	 */
	public function display($tpl = null)
	{
		$this->assignRef ( 'state', $this->get ( 'State' ) );
		$this->assign('total', $this->get('Total'));
	    $this->assignRef('announcements', $this->get('Items'));
	    $this->assignRef('pagination', $this->get('Pagination'));

	    parent::display($tpl);
	}
}