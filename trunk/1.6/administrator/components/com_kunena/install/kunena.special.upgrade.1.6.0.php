<?php
/**
* @version $Id: kunena.special.upgrade.1.6.0.php $
* Kunena Component
* @package Kunena
* @Copyright (C) 2008 - 2009 Kunena Team All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.com
*
* Kunena Delete deprecated template for 1.6.0
* component: com_kunena
**/

defined( '_JEXEC' ) or die('Restricted access');

// Add custom upgrade code here
// Most or all sql statements should be covered within comupgrade.xml

$kunena_db =& JFactory::getDBO();

//Import filesystem libraries.
jimport('joomla.filesystem.folder');

$kunena_db->setQuery("SELECT template FROM #__fb_config");
$kunena_db->query();
$kactualtemplate = $kunena_db->loadResult();
if ($kactualtemplate == 'default_ex' || $kactualtemplate == 'default_green' || $kactualtemplate == 'default_red' || $kactualtemplate == 'default_gray')
{
	$kunena_db->setQuery("UPDATE #__fb_config SET template='default',templateimagepath='default'");
	$kunena_db->query();
	$templatedeprecatedlist = array('default_ex','default_green','default_red','default_gray');
	foreach ($templatedeprecatedlist as $template)
	{  
		if (file_exists(KUNENA_PATH_TEMPLATE. DS . $template))
		{
			JFolder::delete(KUNENA_PATH_TEMPLATE. DS . $template);
		}

	}
}
?>
