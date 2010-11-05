<?php
/**
* @version $Id$
* Kunena Component
* @package Kunena
*
* @Copyright (C) 2008 - 2009 Kunena Team All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.com
*
* Based on FireBoard Component
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
defined( '_JEXEC' ) or die('Restricted access');

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
		$database = &JFactory::getDBO();
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

		$componentBaseDir = '';
		$this->_converterDir = KUNENA_PATH_ADMIN .DS. $this->subdir;

		//initiate XML doc
		$xmlDoc = new DOMIT_Lite_Document();
		$xmlDoc->loadXML( $this->converterDir .DS. $this->xmlFileName, false, true );

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
		$database = &JFactory::getDBO();
		$numChildren =& $startNode->childCount;
		$childNodes =& $startNode->childNodes;

		for($i = 0; $i < $numChildren; $i++) {
			$currentNode =& $childNodes[$i];
			$nodeName =& $currentNode->nodeName;
			switch($nodeName) {
				case "phpfile":
					//include file
					$fileName = $currentNode->getAttribute( "name" );
					$include = $this->_upgradeDir .DS . $fileName;
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
								<div id="id<?php echo $i;?>_<?php echo $batch;?>" onClick="javascript:showDetail(this);" style="cursor:pointer;">
									<img id="id<?php echo $i;?>_<?php echo $batch;?>_img" src="images/expandall.png" border="0">
									Running SQL Query
								</div>
								<div id="id<?php echo $i;?>_<?php echo $batch;?>_details" style="display:None;" class="details"><?php echo $this->_error;?><pre><?php echo $database->_sql;?></pre></div>
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
