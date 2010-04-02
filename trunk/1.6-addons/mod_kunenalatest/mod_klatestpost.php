<?php
/**
* @version		$Id
* @package		klatestpost
* @copyright	(c) 2010 Kunena Team, All rights reserved
* @license		GNU/GPL
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// Include the syndicate functions only once
require_once (dirname(__FILE__).DS.'helper.php');

$db = JFactory::getDBO ();
if (modklatestpostHelper::getKunenaConfigClass()) {
  $k_config = modklatestpostHelper::getKunenaConfigClass();
}

$klistpost = modklatestpostHelper::getKunenaLatestList($params,$k_config,$db);

//check if we have all the itemid sets. if so, then no need for DB call
if (!defined("KUNENA_COMPONENT_ITEMID"))
{
	$db->setQuery("SELECT id FROM #__menu WHERE link='index.php?option=com_kunena' AND published='1'");
    $kid = $db->loadResult();
    
    define("KUNENA_COMPONENT_ITEMID", (int)$kid);
    define("KUNENA_COMPONENT_ITEMID_SUFFIX", "&amp;Itemid=" . KUNENA_COMPONENT_ITEMID);
 }   

if (!defined("KUNENA_LIVEURLREL"))
{
  // Kunena live url
  define('KUNENA_LIVEURLREL', 'index.php?option=com_kunena' . KUNENA_COMPONENT_ITEMID_SUFFIX);
}

if (!defined("KUNENA_URLICONSPATH"))
{
  define('KUNENA_URLICONSPATH', JURI::root() . 'components/com_kunena/template/default/images/icons/');
}

if (modklatestpostHelper::getKunenaLinkClass()) {
  $klink = modklatestpostHelper::getKunenaLinkClass();
}

require(JModuleHelper::getLayoutPath('mod_klatestpost'));
