<?php
/**
* @version $Id: fb_converter.class.php 577 2008-01-22 06:22:16Z fxstein $
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

class fb_Converter {
	var $xmlFileName=null;
	var $subdir=null;
	var $silent=null;
	var $_error=null;
	var $_return=true;
	var $_converterDir=null;
	
	function fb_Converter( $xmlFileName = "joomlaboard.xml", $subdir = "plugin/converter", $silent = false ) {
	    $this->xmlFileName = $xmlFileName;
	    $this->subdir = $subdir;
		$this->silent = $silent;
	}
	
	/**
	 * Main conversion/import function. Processes XML file
	 */
	function doConversion() {
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
		
		$componentBaseDir	= ;
		$this->_converterDir = mosPathName( $mosConfig_absolute_path . '/administrator/components/fireboard' ) . '/' . $this->subdir;
	
		//initiate XML doc
		$xmlDoc = new DOMIT_Lite_Document();
		$xmlDoc->loadXML( $this->converterDir . '/'. $this->xmlFileName, false, true );
		
		//load root element and check XML version (for future use)
		$root = &$xmlDoc->documentElement;
		$comUpgradeVersion = $root->getAttribute( "version" );
		
        $importElement =& $root->firstChild;
        $versionNumber = $importElement->getAttribute( "version" );
        $versionDate = $importElement->getAttribute( "date" );
			
        //import mode, run import queries
        $importElement = $root->getElementsByPath('import', 1);
        if (!is_null($importElement)) {
            $this->processNode($importElement,1);
        }
        if(!$this->silent) {
            ?>
            </table>
            <?php
        }
    }	
	
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
					if (!@$database->query()) {
						$this->_error = "DB function failed with error number $database->_errorNum<br /><font color=\"red\">";
						$this->_error .= mysql_error($database->_resource);
						$this->_error .= "</font>";
						$img = "publish_x.png";
						$this->_return = false;
					} else {
						$this->_error = "";
						$img = "tick.png";
					}
					$database->setQuery($currentNode->getText());
					if(!$this->silent) {
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