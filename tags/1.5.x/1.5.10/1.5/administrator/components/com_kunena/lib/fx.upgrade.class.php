<?php
/**
* @version $Id:fx.upgrade.class.php 97 2009-01-23 21:58:23Z fxstein $
* Kunena Component
* @package Kunena
*
* @Copyright (C) 2008 - 2009 Kunena Team All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.com
*
* Based on comUpgrade class
* @copyright (C) 2005 - 2007 Samuel Suter / All Rights Reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author Samuel Suter
**/

// ensure this file is being included by a parent file
defined( '_JEXEC' ) or die('Restricted access');

// Kunena wide defines
require_once (JPATH_ROOT  .DS. 'components' .DS. 'com_kunena' .DS. 'lib' .DS. 'kunena.defines.php');

include_once (KUNENA_PATH_LIB .DS. 'kunena.debug.php');

class fx_Upgrade {
	var $component=null;
	var $xmlFileName=null;
	var $subdir=null;
	var $versionTable=null;
	var $silent=null;
	var $_error=null;
	var $_return=true;
	var $_upgradeDir=null;

	// helper function to create version table
	function fx_Upgrade( $component, $xmlFileName = "fx_upgrade.xml", $versionTablePrefix = "fx_", $subdir = "", $silent = false ) {
		$db =& JFactory::getDBO();
		$this->component = $component;
	    $this->xmlFileName = $xmlFileName;
	    $this->subdir = $subdir;
	    $this->versionTable = $db->getPrefix() . $versionTablePrefix . "version";
		$this->silent = $silent;
	}

	// helper function to create new version table
	function createVersionTable()
	{
		$kunena_db =& JFactory::getDBO();
	    $kunena_db->setQuery( "CREATE TABLE IF NOT EXISTS `$this->versionTable`
								(`id` INTEGER NOT NULL AUTO_INCREMENT,
								`version` VARCHAR(20) NOT NULL,
								`versiondate` DATE NOT NULL,
								`installdate` DATE NOT NULL,
								`build` VARCHAR(20) NOT NULL,
								`versionname` VARCHAR(40) NULL,
								PRIMARY KEY(`id`)) DEFAULT CHARSET=utf8;" );
		// Let the install handle the error
		return $kunena_db->query();
		check_dberror("Version table creation failed.");
	}

	// helper function to drop existing version table
	function dropVersionTable()
	{
		$kunena_db =& JFactory::getDBO();
	    $kunena_db->setQuery("DROP TABLE IF EXISTS `$this->versionTable`;");
		$kunena_db->query();
		check_dbwarning('Unable to drop version table.');
   	}

	// helper function retrieve latest version from version table
	function getLatestVersion($versionTable)
	{
		$kunena_db =& JFactory::getDBO();

		$query = "SELECT
		                `version`,
		                `versiondate`,
		                `installdate`,
		                `build`,
		                `versionname`
		            FROM `$versionTable`
		            ORDER BY `id` DESC";

	    $kunena_db->setQuery($query,0,1);// LIMIT 1
		$currentVersion = $kunena_db->loadObject();
		check_dberror('Could not load latest Version record.');
		return $currentVersion;
	}

	function insertVersionData( $version, $versiondate, $build, $versionname)
	{
		$kunena_db =& JFactory::getDBO();
	    $kunena_db->setQuery( "INSERT INTO `$this->versionTable`
								SET `version` = '".$version."',
								`versiondate` = '".$versiondate."',
								`installdate` = CURDATE(),
								`build` = '".$build."',
								`versionname` = '".$versionname."';"
								);
		$kunena_db->query();
		check_dberror('Unable to insert version record.');
	}

	function insertDummyVersion()
	{
		$this->insertVersionData('0.0.1','2007-01-01',0,'Placeholder for unknown prior version');
	}

	function backupVersionTable()
	{
		$kunena_db =& JFactory::getDBO();
	    $kunena_db->setQuery("DROP TABLE IF EXISTS `".$this->versionTable."_backup`;");
        $kunena_db->query();
        check_dberror('Unable to drop previous backup version table.');

        $kunena_db->setQuery("CREATE TABLE `".$this->versionTable."_backup` SELECT * FROM `".$this->versionTable."`;");
        $kunena_db->query();
        check_dberror('Unable to backup version table.');
	}

	/**
	 * Main upgrade function. Processes XML file
	 */
	function doUpgrade() {
		require_once( KUNENA_ROOT_PATH .DS. 'includes/domit/xml_domit_lite_include.php' );
		if(!$this->silent) {
			?>
			<script language=JavaScript>
			function showDetail(srcElement) {
				var targetID, srcElement, targetElement, imgElementID, imgElement;
				targetID = srcElement.id + "_details";
				imgElementID = srcElement.id + "_img";

				targetElement = document.getElementById(targetID);
				imgElement = document.getElementById(imgElementID);
				if (targetElement.style.display == "none") {
					targetElement.style.display = "";
					imgElement.src = "images/collapseall.png";
				} else {
					targetElement.style.display = "none";
					imgElement.src = "images/expandall.png";
				}
			}
			</script>
			<style>
			.details {
				font-family: courier;
				background-color: #EEEEEE;
				border: 1px dashed #BBBBBB;
				padding-left: 10px;
				margin-left: 20px;
				margin-top: 5px;
			</style>
			<?php
		}

		$componentBaseDir = KUNENA_ROOT_PATH_ADMIN .DS. 'components/';
		$this->_upgradeDir = $componentBaseDir . $this->component .DS . $this->subdir;

		//get current version, check if version table exists
		$createVersionTable = 1;
		$upgrade=null;

		// Legacy enabler
		// Versions prior to 1.0.5 did not came with a version table inside the database
		// this would make the installer believe this is a fresh install. We need to perform
		// a 'manual' check if this is going to be an upgrade and if so create that table
		// and write a dummy version entry to force an upgrade.

		$kunena_db =& JFactory::getDBO();
		$kunena_db->setQuery( "SHOW TABLES LIKE ".$kunena_db->quote($kunena_db->getPrefix().'fb_messages') );
		$kunena_db->query();
		check_dberror("Unable to search for messages table.");

		if($kunena_db->getNumRows()) {
			// fb tables exist, now lets see if we have a version table
			$kunena_db->setQuery( "SHOW TABLES LIKE ".$kunena_db->quote($this->versionTable) );
			$createVersionTable = $kunena_db->loadResult();
			$createVersionTable = empty($createVersionTable);

			check_dberror("Unable to search for version table.");

			if($createVersionTable) {
				//version table does not exist - this is a pre 1.0.5 install - lets create
				$this->createVersionTable();
				// insert dummy version entry to force upgrade
				$this->insertDummyVersion();
				$createVersionTable = 0;
			}
		}

		if(!$createVersionTable) {
			// lets see if we need to update the version table layout from it original
			$currentVersion = $this->getLatestVersion($this->versionTable);
			if(!is_object($currentVersion))
			{
				// version table exisits, but we cannot retrieve the latest version
				// in this case we assume the table layout might have changed
				// backup old table and create new version table
				$this->backupVersionTable();
				$this->dropVersionTable();
				$this->createVersionTable();
				// insert dummy version info to start with
				$this->insertDummyVersion();
			}

			//check for latest version and date entry
			$currentVersion = $this->getLatestVersion($this->versionTable);
			if(!$currentVersion->version && !$currentVersion->versiondate) {
				//there was an error in retrieving the version and date, goto install mode
				$upgrade = 0;
			} else {
				//OK, no error, there is a version table and it also contains version and date information, switching to upgrade mode
				$upgrade = 1;
			}
		}

		//Create version table
		if($createVersionTable == 1)
		{
			if (!$this->createVersionTable())
			{
				$this->_error = "DB function failed with error number <b>" . $kunena_db->_errorNum . "</b><br/>";
				$this->_error .= $kunena_db->getErrorMsg();
				$img = "publish_x.png";
				$this->_return = false;
			} else
			{
				$img = "tick.png";
			}
			if(!$this->silent) {
				?>
				<table class="adminlist">
				<tr>
					<td>Creating version table</td>
					<td width="20"><a href="#" onMouseOver="return overlib('<?php echo $this->_error?>', BELOW, RIGHT,WIDTH,300);" onmouseout="return nd();" ><img src="images/<?php echo $img;?>" border="0"></a></td>
				</tr>
				</table>
				<?php
			}
		}

		//initiate XML doc
		$xmlDoc = new DOMIT_Lite_Document();
		$xmlDoc->loadXML( $this->_upgradeDir .DS. $this->xmlFileName, false, true );

		//load root element and check XML version (for future use)
		$root = &$xmlDoc->documentElement;
		$comUpgradeVersion = $root->getAttribute( "version" );

		//here comes the real stuff
		if($upgrade == 0) {
			$installElement =& $root->firstChild;
			$version = $installElement->getAttribute( "version" );
			$versiondate = $installElement->getAttribute( "versiondate" );
			$build = $installElement->getAttribute( "build" );
			$versionname = $installElement->getAttribute( "versionname" );

			if(!$this->silent)
			{
				?>
				<div id="overDiv" style="position:absolute; visibility:hidden; z-index:10000;"></div>
				<script  type="text/javascript" src="<?php echo JURI::root();?>/includes/js/overlib_mini.js"></script>
				<table class="adminlist">
					<tr>
						<th colspan="2">Installing "<?php echo $this->component?>" (Version: <?php echo $version;?> / Date: <?php echo $versiondate;?> / Build: <?php echo $build;?> / VersionName: <?php echo $versionname;?> )</th>
					</tr>
				<?php
			}

			//install mode, run install queries
			$installElement = $root->getElementsByPath('install', 1);
			if (!is_null($installElement)) {
				$this->processNode($installElement,1);
			}
			if(!$this->silent) {
				?>
				</table>
				<?php
			}

			//Store version info and date in database
			$this->insertVersionData( $version, $versiondate, $build, $versionname);

		} else {
			if(!$this->silent) {
				?>
				<div id="overDiv" style="position:absolute; visibility:hidden; z-index:10000;"></div>
				<script  type="text/javascript" src="<?php echo JURI::root();?>/includes/js/overlib_mini.js"></script>
				<table class="adminlist">
					<tr>
						<th colspan="2">Upgrading "<?php echo $this->component?>" (Version: <?php echo @$currentVersion->version; ?> / Version Date: <?php echo @$currentVersion->versiondate;?> / Install Date: <?php echo @$currentVersion->installdate;?> / Build: <?php echo @$currentVersion->build;?> / Version Name: <?php echo @$currentVersion->versionname;?>)</th>
					</tr>
				<?php
			}
			//upgrade mode
			$upgradeElement = $root->getElementsByPath('upgrade', 1);

			if (!is_null($upgradeElement)) {
				//walk through the versions
				$numChildrenMain =& $upgradeElement->childCount;
				$childNodesMain =& $upgradeElement->childNodes;
				for($k = 0; $k < $numChildrenMain; $k++) {
					$versionElement =& $childNodesMain[$k];
					$version = $versionElement->getAttribute( "version" );
					$versiondate = $versionElement->getAttribute( "versiondate" );
					$build = $versionElement->getAttribute( "build" );
					$versionname = $versionElement->getAttribute( "versionname" );

					//when legacy version exists, just compare version, if date exists as well, compare date
					if(($currentVersion->versiondate && $versiondate > $currentVersion->versiondate) OR (version_compare($version, $currentVersion->version, '>')) OR (version_compare($version, $currentVersion->version, '==') && $build > $currentVersion->build)) {
						//these instructions are for a newer version than the currently installed version

						if(!$this->silent) {
							?>
							<tr>
								<td colspan="2">&nbsp;</td>
							</tr>
							<tr>
								<th colspan="2">Version: <?php echo $version;?> (Version Date: <?php echo $versiondate;?>, Build: <?php echo $build;?>, Version Name: <?php echo $versionname;?>)</th>
							</tr>
							<?php
						}
						//Store version info and date in database
						$this->insertVersionData( $version, $versiondate, $build, $versionname);
						$added_version=1;

						$this->processNode($versionElement,$k);
					} //end if version newer check
				} //end version element loop
				if (!isset($added_version)) $this->insertVersionData( $version, $versiondate, $build, $versionname);
			} //end if !is_null($upgradeElement)
			if(!$this->silent) {
				?>
				</table>
				<?php
			}
		} //end main if upgrade or not
		return $this->_return;
	} //end doUpgrade function

	/**
	 * Processes "phpfile", "query" and "phpcode" child-nodes of the node provided
	 */
	function processNode(&$startNode,$batch = 0) {
		$numChildren =& $startNode->childCount;
		$childNodes =& $startNode->childNodes;

		for($i = 0; $i < $numChildren; $i++) {
			$currentNode =& $childNodes[$i];
			$nodeName =& $currentNode->nodeName;
			$nodemode = strtolower($currentNode->getAttribute( "mode" ));

			switch($nodeName) {
				case "phpfile":
					//include file
					$fileName = $currentNode->getAttribute( "name" );
					$include = $this->_upgradeDir .DS . $fileName;
					$fileCheck = file_exists($include);
					if($fileCheck) {
						ob_start();
						require( $include );
						$img = "tick.png";
						$this->_error = ob_get_contents();
						ob_end_clean();
					}
					else {
						$this->_error = "<font color=\"red\">File not found!</font>";
					}
					if (!$fileCheck || $this->_error) {
						$img = "publish_x.png";
						$this->_return = false;
					}
					
					if(!$this->silent) {
						?>
						<tr>
							<td>
								<div id="id<?php echo $i;?>_<?php echo $batch;?>" onClick="javascript:showDetail(this);" style="cursor:pointer;">
									<img id="id<?php echo $i;?>_<?php echo $batch;?>_img" src="images/expandall.png" border="0">
									Including file
								</div>
								<div id="id<?php echo $i;?>_<?php echo $batch;?>_details" style="display:None;" class="details"><?php echo $this->_error;?><pre><?php echo $include;?></pre></div>
							</td>
							<td width="20" valign="top"><img src="images/<?php echo $img;?>"></td>
						</tr>
						<?php
					}
					break;
				case "query":
					$query = $currentNode->getText();
					$kunena_db =& JFactory::getDBO();

					$kunena_db->setQuery($query);
					$kunena_db->query();
					if ($kunena_db->getErrorNum() != 0)
					{
						$this->_error = "DB function failed with error number ".$kunena_db->getErrorNum()."<br /><font color=\"red\">";
						$this->_error .= $kunena_db->stderr(true);
						$this->_error .= "</font>";
						$img = "publish_x.png";
						$this->_return = false;
					}
					else
					{
						$this->_error = "";
						$img = "tick.png";
					}
					$kunena_db->setQuery($currentNode->getText());
					if(!$this->silent)
					{
						if (!($nodemode=='silenterror' AND $this->_error != ""))
						{
						?>
						<tr>
							<td>
								<div id="id<?php echo $i;?>_<?php echo $batch;?>" onClick="javascript:showDetail(this);" style="cursor:pointer;">
									<img id="id<?php echo $i;?>_<?php echo $batch;?>_img" src="images/expandall.png" border="0">
									Running SQL Query
								</div>
								<div id="id<?php echo $i;?>_<?php echo $batch;?>_details" style="display:None;" class="details"><?php echo $this->_error;?><pre><?php echo $kunena_db->_sql;?></pre></div>
							</td>
							<td width="20" valign="top"><img src="images/<?php echo $img;?>" border="0"></td>
						</tr>
						<?php
						}
					}
					break;
				case "phpcode":
					$code = $currentNode->getText();
					ini_set ("track_errors", 1);
					if(@eval($code) === FALSE) {
						$img = "publish_x.png";
						$this->_error = "<font color=\"red\">".$php_errormsg."</font><br /><br />";
					} else {
						$img = "tick.png";
						$this->_error = "";
					}

					if(!$this->silent) {
						?>
						<tr>
							<td>
								<div id="id<?php echo $i;?>_<?php echo $batch;?>" onClick="javascript:showDetail(this);" style="cursor:pointer;">
									<img id="id<?php echo $i;?>_<?php echo $batch;?>_img" src="images/expandall.png" border="0">
									Executing PHP Code
								</div>
								<div id="id<?php echo $i;?>_<?php echo $batch;?>_details" style="display:None;" class="details"><?php echo $this->_error;?><?php highlight_string( "<?php\n".$code."\n?>" );?></div>
							</td>
							<td width="20" valign="top"><img src="images/<?php echo $img;?>" border="0"></td>
						</tr>
						<?php
					}
					break;
			} //end switch()
		} //end children loop
	}
};

?>