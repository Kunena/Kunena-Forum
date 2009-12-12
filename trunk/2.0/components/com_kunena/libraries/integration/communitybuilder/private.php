<?php
/**
* @version $Id:  $
* Kunena Component - Community Builder PMS Integration
* @package Kunena
*
* @Copyright (C) 2009 www.kunena.com All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.com
**/

// Dont allow direct linking
defined( '_JEXEC' ) or die('Restricted access');

kimport('integration.private');

/**
 * CB framework
 * @global CBframework $_CB_framework
 */
global $_CB_framework, $_CB_database, $ueConfig;

class KPrivateMessagesCommunityBuilder extends KPrivateMessages 
{
	protected function __construct() 
	{
		kimport('integration.communitybuilder.integration');
		KIntegrationCommunityBuilder::init(); 
		if (!KIntegrationCommunityBuilder::usePrivateMessagesIntegration()) 
		{
			return null;
		}
	}
	
	public function &getInstance() 
	{
		if (!self::$instance) {
			self::$instance = new KPrivateMessagesCommunityBuilder();
		}
		return self::$instance;
	}	
	
	function showSendPMIcon($userid) 
	{
		global $_CB_framework, $_CB_PMS;
		
		$kunenaConfig =& KConfig::getInstance();
		$myid = $_CB_framework->myId();
		
		// Don't send messages from/to anonymous and to yourself
		if ($myid == 0 || $userid == 0 || $userid == $myid) return '';
		
		outputCbTemplate( $_CB_framework->getUi() );
		$resultArray = $_CB_PMS->getPMSlinks( $userid, $myid, '', '', 1);
		$html = '';
		if ( count( $resultArray ) > 0) {
			$linkItem = $this->_getIcon(_VIEW_PMS, _VIEW_PMS);
			foreach ( $resultArray as $res ) {
				if ( is_array( $res ) ) {
					$html .= '<a href="' . cbSef( $res["url"] ) . '" title="' . getLangDefinition( $res["tooltip"] ) . '">' . $linkItem . '</a> ';
				}
			}
		}
		return $html;
	}
}
