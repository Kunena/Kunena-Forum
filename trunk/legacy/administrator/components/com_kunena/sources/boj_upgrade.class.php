<?php
/**
* @version $Id: boj_upgrade.class.php 900 2008-08-03 21:24:14Z fxstein $
* Fireboard Component
* @package Fireboard
* @Copyright (C) 2006 - 2008 Best Of Joomla All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.bestofjoomla.com
*
* Based on comUpgrade class
* @copyright (C) 2005 - 2007 Samuel Suter / All Rights Reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author Samuel Suter
**/

// ensure this file is being included by a parent file
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

include_once ($mainframe->getCfg("absolute_path") . "/components/com_fireboard/sources/fb_debug.php");

class boj_Upgrade {
	var $component=null;
	var $xmlFileName=null;
	var $subdir=null;
	var $versionTablePrefix=null;
	var $versionTable=null;
	var $silent=null;
	var $_error=null;
	var $_return=true;
	var $_upgradeDir=null;

	// helper function to create version table
	function boj_Upgrade( $component, $xmlFileName = "boj_upgrade.xml", $versionTablePrefix = "boj_", $subdir = "", $silent = false ) {
		$this->component = $component;
	    $this->xmlFileName = $xmlFileName;
	    $this->subdir = $subdir;
	    $this->versionTablePrefix = $versionTablePrefix;
	    $this->versionTable = "#__" . $this->versionTablePrefix . "version";
		$this->silent = $silent;
	}

	// helper function to create new version table
	function createVersionTable()
	{
		global $database;
		$database->setQuery( "CREATE TABLE `$this->versionTable`
								(`id` INTEGER NOT NULL AUTO_INCREMENT,
								`version` VARCHAR(20) NOT NULL,
								`versiondate` DATE NOT NULL,
								`installdate` DATE NOT NULL,
								`build` VARCHAR(20) NOT NULL,
								`versionname` VARCHAR(40) NULL,
								PRIMARY KEY(`id`));" );
		$database->query() or trigger_dberror('Unable to create version table.');
	}

	// helper function to drop existing version table
	function dropVersionTable()
	{
		global $database;
        $database->setQuery("DROP TABLE IF EXISTS `$this->versionTable`;");
		$database->query() or trigger_dberror('Unable to drop version table.');
   	}

	// helper function retrieve latest version from version table
	function getLatestVersion($versionTable)
	{
		global $database;
		$database->setQuery( 	"SELECT
									`version`,
									`versiondate`,
									`installdate`,
									`build`,
									`versionname`
								FROM `$versionTable`
								ORDER BY `id` DESC LIMIT 1;" );
		$database->loadObject($currentVersion) or trigger_dbwarning('Could not load latest Version record.');
		return $currentVersion;
	}

	function insertVersionData( $version, $versiondate, $build, $versionname)
	{
		global $database;
		$database->setQuery( "INSERT INTO `$this->versionTable`
								SET `version` = '".$version."',
								`versiondate` = '".$versiondate."',
								`installdate` = CURDATE(),
								`build` = '".$build."',
								`versionname` = '".$versionname."';"
								);
		$database->query() or trigger_dberror('Unable to insert version record.');
	}

	function insertDummyVersion()
	{
		$this->insertVersionData('1.0.0','20070101',0,'Placeholder for unknown prior version');
	}

	function backupVersionTable()
	{
		global $database;

        $database->setQuery("DROP TABLE IF EXISTS `".$this->versionTable."_backup`;");
        $database->query() or trigger_dberror('Unable to drop previous backup version table.');

        $database->setQuery("CREATE TABLE `".$this->versionTable."_backup` SELECT * FROM `".$this->versionTable."`;");
        $database->query() or trigger_dberror('Unable to backup version table.');
	}

	/**
	 * Main upgrade function. Processes XML file
	 */
	function doUpgrade() {
		global $database, $mosConfig_absolute_path, $mosConfig_live_site;
		require_once( $mosConfig_absolute_path . '/includes/domit/xml_domit_lite_include.php' );
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

		$componentBaseDir	= mosPathName( $mosConfig_absolute_path . '/administrator/components' );
		$this->_upgradeDir = $componentBaseDir . $this->component . '/' . $this->subdir;
		$versionTableNoPrefix = $this->versionTablePrefix . "version";

		//get current version, check if version table exists
		$createVersionTable = 1;
		$upgrade=null;
		$database->setQuery( "SHOW TABLES LIKE '%".$versionTableNoPrefix."'" );
		$database->query() or trigger_dberror('Unable to check for existing version table.');
		if($database->loadResult()) {
			//table already exists, so do not create a new table
			$createVersionTable = 0;

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
				$this->_error = "DB function failed with error number <b>" . $database->_errorNum . "</b><br/>";
				$this->_error .= $database->getErrorMsg();
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
					<td width="20"><a href="#" onMouseOver="return overlib('<?=$this->_error?>', BELOW, RIGHT,WIDTH,300);" onmouseout="return nd();" ><img src="images/<?php echo $img;?>" border="0"></a></td>
				</tr>
				</table>
				<?php
			}
		}

		//initiate XML doc
		$xmlDoc = new DOMIT_Lite_Document();
		$xmlDoc->loadXML( $this->_upgradeDir . '/'. $this->xmlFileName, false, true );

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

			//Store version info and date in database
			$this->insertVersionData( $version, $versiondate, $build, $versionname);

			if(!$this->silent)
			{
				?>
				<div id="overDiv" style="position:absolute; visibility:hidden; z-index:10000;"></div>
				<script  type="text/javascript" src="<?php echo $mosConfig_live_site;?>/includes/js/overlib_mini.js"></script>
				<table class="adminlist">
					<tr>
						<th colspan="2">Installing "<?=$this->component?>" (Version: <?=$version;?> / Date: <?=$versiondate;?> / Build: <?=build;?> / VersionName: <?=$versionname;?> )</th>
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
		} else {
			if(!$this->silent) {
				?>
				<div id="overDiv" style="position:absolute; visibility:hidden; z-index:10000;"></div>
				<script  type="text/javascript" src="<?php echo $mosConfig_live_site;?>/includes/js/overlib_mini.js"></script>
				<table class="adminlist">
					<tr>
						<th colspan="2">Upgrading "<?=$this->component?>" (Version: <?=@$currentVersion->version; ?> / Version Date: <?=@$currentVersion->versiondate;?> / Install Date: <?=@$currentVersion->installdate;?> / Build: <?=@$currentVersion->build;?> / Version Name: <?=@$currentVersion->versionname;?>)</th>
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
					if(($currentVersion->versiondate && $versiondate > $currentVersion->versiondate) OR ($version > $currentVersion->version) OR ($version == $currentVersion->version && $build > $currentVersion->build)) {
						//these instructions are for a newer version than the currently installed version

						if(!$this->silent) {
							?>
							<tr>
								<td colspan="2">&nbsp;</td>
							</tr>
							<tr>
								<th colspan="2">Version: <?=$version;?> (Version Date: <?=$versiondate;?>, Build: <?=$build;?>, Version Name: <?=$versionname;?>)</th>
							</tr>
							<?php
						}
						//Store version info and date in database
						$this->insertVersionData( $version, $versiondate, $build, $versionname);

						$this->processNode($versionElement,$k);
					} //end if version newer check
				} //end version element loop
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
		global $database;
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
					$include = $this->_upgradeDir . '/' . $fileName;
					$fileCheck = file_exists($include);
					if($fileCheck) {
						require( $include );
						$img = "tick.png";
						$this->_error = "";
					} else {
						$img = "publish_x.png";
						$this->_error = "<font color=\"red\">File not found!</font>";
						$this->_return = false;
					}
					if(!$this->silent) {
						?>
						<tr>
							<td>
								<div id="id<?=$i;?>_<?=$batch;?>" onClick="javascript:showDetail(this);" style="cursor:pointer;">
									<img id="id<?=$i;?>_<?=$batch;?>_img" src="images/expandall.png" border="0">
									Including file
								</div>
								<div id="id<?=$i;?>_<?=$batch;?>_details" style="display:None;" class="details"><?=$this->_error;?><pre><?=$include;?></pre></div>
							</td>
							<td width="20" valign="top"><img src="images/<?php echo $img;?>"></td>
						</tr>
						<?php
					}
					break;
				case "query":
					$query = $currentNode->getText();
					$database->setQuery($query);
					if (!@$database->query())
					{
						$this->_error = "DB function failed with error number $database->_errorNum<br /><font color=\"red\">";
						$this->_error .= mysql_error($database->_resource);
						$this->_error .= "</font>";
						$img = "publish_x.png";
						$this->_return = false;
					}
					else
					{
						$this->_error = "";
						$img = "tick.png";
					}
					$database->setQuery($currentNode->getText());
					if(!$this->silent)
					{
						if (!($nodemode=='silenterror' AND $this->_error != ""))
						{
						?>
						<tr>
							<td>
								<div id="id<?=$i;?>_<?=$batch;?>" onClick="javascript:showDetail(this);" style="cursor:pointer;">
									<img id="id<?=$i;?>_<?=$batch;?>_img" src="images/expandall.png" border="0">
									Running SQL Query
								</div>
								<div id="id<?=$i;?>_<?=$batch;?>_details" style="display:None;" class="details"><?=$this->_error;?><pre><?=$database->_sql;?></pre></div>
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
								<div id="id<?=$i;?>_<?=$batch;?>" onClick="javascript:showDetail(this);" style="cursor:pointer;">
									<img id="id<?=$i;?>_<?=$batch;?>_img" src="images/expandall.png" border="0">
									Executing PHP Code
								</div>
								<div id="id<?=$i;?>_<?=$batch;?>_details" style="display:None;" class="details"><?=$this->_error;?><?php highlight_string( "<?php\n".$code."\n?>" );?></div>
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