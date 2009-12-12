<?php
/**
* @version $Id:  $
* Kunena Component - Community Builder Profile Integration
* @package Kunena
*
* @Copyright (C) 2009 www.kunena.com All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.com
**/

// Dont allow direct linking
defined( '_JEXEC' ) or die('Restricted access');

kimport('integration.login');

/**
 * CB framework
 * @global CBframework $_CB_framework
 */
global $_CB_framework, $_CB_database, $ueConfig;

class KLoginCommunityBuilder extends KLogin 
{
	protected function __construct() 
	{
		kimport('integration.communitybuilder.integration');
		KIntegrationCommunityBuilder::init(); 
		if (!KIntegrationCommunityBuilder::useLoginIntegration()) 
		{
			return null;
		}
	}
	
	public function &getInstance() 
	{
		if (!self::$instance) {
			self::$instance = new KLoginCommunityBuilder();
		}
		return self::$instance;
	}

	public function getLoginURL() 
	{
		if (!KIntegrationCommunityBuilder::useProfileIntegration()) return parent::getLoginURL();
		return cbSef( 'index.php?option=com_comprofiler&amp;task=login' );
	}

	public function getLogoutURL() 
	{
		if (!KIntegrationCommunityBuilder::useProfileIntegration()) return parent::getLogoutURL();
		return cbSef( 'index.php?option=com_comprofiler&amp;task=logout' );
	}

	public function getRegisterURL() 
	{
		if (!KIntegrationCommunityBuilder::useProfileIntegration()) return parent::getRegisterURL();
		return cbSef( 'index.php?option=com_comprofiler&amp;task=registers' );
	}

	public function getLostPasswordURL() 
	{
		if (!KIntegrationCommunityBuilder::useProfileIntegration()) return parent::getLostPasswordURL();
		return cbSef( 'index.php?option=com_comprofiler&amp;task=lostPassword' );
	}

}
