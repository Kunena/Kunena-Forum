




<?php
/**
 * @version		$Id: admin.kunena.php 1222 2009-11-28 04:09:05Z mahagr $
 * @package		Kunena
 * @subpackage	com_kunena
 * @copyright	Copyright (C) 2008 - 2009 Kunena Team. All rights reserved.
 * @license		GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
 * @link		http://www.kunena.com
 */

// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');

$view	= JRequest::getCmd('view','kunena');

JHTML::_('behavior.switcher');

// Load submenu's
$views	= array(
					'config'			=> JText::_('Configuration'),
					'forums'			=> JText::_('Forums'),
					'users'				=> JText::_('Users'),
					'smilies' 			=> JText::_('Emoticons'),
					'ranks'				=> JText::_('Ranks'),
					'templates'			=> JText::_('Templates'),
					'queue'				=> JText::_('Job Queue'),
					'tools'				=> JText::_('Tools'),
					'about'				=> JText::_('About')
				);

foreach( $views as $key => $val )
{
	$active	= ( $view == $key );

	JSubMenuHelper::addEntry( $val , 'index.php?option=com_kunena&view=' . $key , $active );
}
?>
