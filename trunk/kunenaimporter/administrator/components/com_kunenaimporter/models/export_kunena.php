<?php
/**
 * Joomla! 1.5 component: Kunena Forum Importer
 *
 * @version $Id: $
 * @author Kunena Team
 * @package Joomla
 * @subpackage Kunena Forum Importer
 * @license GNU/GPL
 *
 * Imports forum data into Kunena
 *
 * @Copyright (C) 2008 - 2009 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

// Import Joomla! libraries
jimport('joomla.application.component.model');
jimport('joomla.application.application');

require_once( JPATH_COMPONENT.DS.'models'.DS.'export.php' );

class KunenaimporterModelExport_Kunena extends KunenaimporterModelExport {
	var $version;

	function checkConfig() {
		parent::checkConfig();
		if (JError::isError($this->ext_database)) return;

		$query="SELECT version FROM #__version ORDER BY id DESC";
		$this->ext_database->setQuery($query);
		$this->version = $this->ext_database->loadResult();
		if (!$this->version) {
			$this->error = $this->ext_database->getErrorMsg();
			if (!$this->error) $this->error = 'Configuration information missing: External Kunena version not found';
		}
		if ($this->error) {
			$this->addMessage('<div>External Kunena version: <b style="color:red">FAILED</b></div>');
			$this->addMessage('<div><b>Error:</b> '.$this->error.'</div>');
			return false;
		}
		
		$version = explode('.', $this->version, 3);
		if ($version[0] != 1 || $version[1] > 5) $this->error = "Unsupported forum: Kunena $this->version"; 
		if ($this->error) {
			$this->addMessage('<div>External Kunena version: <b style="color:red">'.$this->version.'</b></div>');
			$this->addMessage('<div><b>Error:</b> '.$this->error.'</div>');
			return false;
		}
		$this->addMessage('<div>External Kunena version: <b style="color:green">'.$this->version.'</b></div>');

	}

	function buildImportOps() {
		// select, from, where, orderby
		$importOps = array();
		$importOps['announcements'] = array('select'=>'*', 'from'=>'#__announcement', 'orderby'=>'id');
		$importOps['attachments'] = array('select'=>'*', 'from'=>'#__attachments', 'orderby'=>'mesid');
		$importOps['categories'] = array('select'=>'*', 'from'=>'#__categories', 'orderby'=>'id');
		$importOps['config'] = array('select'=>'*', 'from'=>'#__config');
		$importOps['favorites'] = array('select'=>'*', 'from'=>'#__favorites', 'orderby'=>'thread');
		$importOps['messages'] = array('select'=>'*', 'from'=>'#__messages AS m LEFT JOIN #__messages_text AS t ON m.id=t.mesid', 'orderby'=>'id');
		$importOps['moderation'] = array('select'=>'*', 'from'=>'#__moderation', 'orderby'=>'userid');
		$importOps['ranks'] = array('select'=>'*', 'from'=>'#__ranks', 'orderby'=>'rank_id');
		$importOps['sessions'] = array('select'=>'*', 'from'=>'#__sessions', 'orderby'=>'userid');
		$importOps['smilies'] = array('select'=>'*', 'from'=>'#__smileys', 'orderby'=>'id');
		$importOps['subscriptions'] = array('select'=>'*', 'from'=>'#__subscriptions', 'orderby'=>'thread');
		$importOps['userprofile'] = array('select'=>'*', 'from'=>'#__users', 'orderby'=>'userid');
		$importOps['version'] = array('select'=>'*', 'from'=>'#__version', 'orderby'=>'id');
		$importOps['whoisonline'] = array('select'=>'*', 'from'=>'#__whoisonline', 'orderby'=>'id');
		$this->importOps =& $importOps;
	}
}

?>
