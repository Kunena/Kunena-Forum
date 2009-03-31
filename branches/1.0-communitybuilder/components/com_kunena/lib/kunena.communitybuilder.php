<?php
/**
* @version $Id: kunena.user.class.php 570 2009-03-31 10:04:30Z mahagr $
* Kunena Component - Community Builder compability
* @package Kunena
*
* @Copyright (C) 2009 www.kunena.com All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.com
**/

// Dont allow direct linking
defined ('_VALID_MOS') or die('Direct Access to this location is not allowed.');

/**
 * CB framework
 * @global CBframework $_CB_framework
 */
global $_CB_framework, $_CB_database, $ueConfig, $mainframe;
$tmp_db =& $database;

if ( defined( 'JPATH_ADMINISTRATOR' ) ) {
        if ( ! file_exists( JPATH_ADMINISTRATOR . '/components/com_comprofiler/plugin.foundation.php' ) ) {
                echo 'CB not installed';
                return;
        }
        include_once( JPATH_ADMINISTRATOR . '/components/com_comprofiler/plugin.foundation.php' );
} else {
        if ( ! file_exists( $mainframe->getCfg( 'absolute_path' ) . '/administrator/components/com_comprofiler/plugin.foundation.php' ) ) {
                echo 'CB not installed';
                return;
        }
        include_once( $mainframe->getCfg( 'absolute_path' ) . '/administrator/components/com_comprofiler/plugin.foundation.php' );
}
cbimport( 'cb.database' );
cbimport( 'cb.tables' );
cbimport( 'language.front' );
cbimport( 'cb.tabs' );

$database =& $tmp_db;
unset ($tmp_db);

class CKunenaCBProfile {
	var $sidebarText;
	
	function CKunenaCBProfile() {
		$this->sidebarText = <<<EOS
<span class="view-username">[cb:userfield field="username"/]</span>
<span class="fb_avatar">[cb:userfield field="avatar"/]</span>
<div class="viewcover">
  <span>[cb:userfield field="forumkarma"/]</span>
</div>
<div class="viewcover">
  <span>[cb:userfield field="forumrank"/]</span>
</div>
<div class="viewcover">
  <span>[cb:userfield field="connections"/] Connections</span>
</div>
EOS;
	}
	
	function showUserProfile($userid) {
		$cbUser =& CBuser::getInstance( $userid );
		if ( $cbUser !== null ) {
			return $cbUser->replaceUserVars( $this->sidebarText );
		} else {
    		return "User doesn't exist anymore";
		}
	}

	function &getInstance() {
		static $instance;
		if (!$instance) $instance = new CKunenaCBProfile();
		return $instance;
	}

	function showAvatar($userid, $size='medium') {
		if ( $userid ) {
			$cbUser =& CBuser::getInstance( (int) $userid );
			if ( $cbUser == null ) {
				// FIXME: handle this one!
				return '';
			}
			if ($size=='large') return $cbUser->getField( 'avatar' );
			else return $cbUser->getField( 'avatar', null, 'html', 'none', 'list' );
		}
	}
}
	