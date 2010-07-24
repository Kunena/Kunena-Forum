<?php
/**
 * @version $Id: kunena.special.upgrade.1.6.0a.php 2749 2010-06-16 08:38:27Z mahagr $
 * Kunena Component
 * @package Kunena
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 *
 * Kunena update for 1.6.0 ALPHA2
 * component: com_kunena
 **/

defined ( '_JEXEC' ) or die ();

jimport ( 'joomla.version' );
$jversion = new JVersion ();
if ($jversion->RELEASE == 1.5 && is_dir(JPATH_ROOT.'/plugins/system/mootools12')) {
	$kdb = JFactory::getDBO();
	$query = "SELECT id FROM #__plugins WHERE element='mootools12'";
	$kdb->setQuery ( $query );
	$id = $kdb->loadResult ();
	if ($id) {
		jimport('joomla.installer.installer');
		$installer = new JInstaller ( );
		$installer->uninstall ( 'plugin', $id );
	}
}