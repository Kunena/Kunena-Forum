<?php
/**
* @version $Id: fb_pdf.php 969 2008-08-12 09:23:54Z racoon $
* Fireboard Component
* @package Fireboard
* @Copyright (C) 2006 - 2007 Best Of Joomla All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.bestofjoomla.com
*
* Based on Joomlaboard Component
* @copyright (C) 2000 - 2004 TSMF / Jan de Graaff / All Rights Reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author TSMF & Jan de Graaff
**/

defined ('_VALID_MOS') or die('Direct Access to this location is not allowed.');

class fbpdfwrapper {
	// small wrapper class for J1.5 to emulate Cezpdf-class
	var $_title = '';
	var $_header = '';
	var $_text = '';
	function __construct() { $this->_title = $this->_header = $this->_text = ''; }
	function ezSetCmMargins($v1, $v2, $v3, $v4) {}
	function selectFont($font) {}
	function openObject() { return 0; }
	function saveState() {}
	function setStrokeColor($v1, $v2, $v3, $v4) {}
	function line($v1, $v2, $v3, $v4) {}
	function addText($v1, $v2, $v3, $text) {
		if ($this->_title == '') { $this->_title = $text; } else { $this->_header = $text; }
	}
	function restoreState() {}
	function closeObject() {}
	function addObject($v1, $v2) {}
	function ezSetDy($v1) {}
	function ezText($text, $size) {
		$this->_text .= '<font size='. ($size-11) .'>' . str_replace("\n", '<br/>', $text) . '</font><br/>';
	}
	function ezStream() {
		$options = array('margin-header' => 5, 'margin-footer' => 10, 'margin-top' => 20,
			'margin-bottom' => 20, 'margin-left' => 15, 'margin-right' => 15);
        $pdfDoc =& JDocument::getInstance('pdf', $options);
		$pdfDoc->setTitle($this->_title);
        $pdfDoc->setHeader($this->_header);
        $pdfDoc->setBuffer($this->_text);
        header('Content-Type: application/pdf');
        header('Content-disposition: inline; filename="file.pdf"', true);
		echo $pdfDoc->render();
	}
}

function dofreePDF($database)
{
    global $mosConfig_sitename, $my, $aro_group, $acl;
    global $fbConfig, $fbSession, $catid;
    require_once (JB_ABSSOURCESPATH . 'fb_auth.php');
    $is_Mod = 0;

    if (!$is_admin)
    {
        $database->setQuery("SELECT userid FROM #__fb_moderation WHERE catid=$catid and userid=$my->id");

        if ($database->loadResult()) {
            $is_Mod = 1;
        }
    }
    else {
        $is_Mod = 1;
    } //superadmins always are

    if (!$is_Mod)
    {

        //get all the info on this forum:
        $database->setQuery("SELECT id,pub_access,pub_recurse,admin_access,admin_recurse FROM #__fb_categories where id=$catid");
        $row = $database->loadObjectList();
                check_dberror("Unable to load categorie detail.");


        $allow_forum = explode(',', FBTools::getAllowedForums($my->id, $aro_group->group_id, $acl));

        //Do user identification based upon the ACL
        $letPass = 0;
        $letPass = fb_auth::validate_user($row[0], $allow_forum, $aro_group->group_id, $acl);
    }

    if ($letPass || $is_Mod)
    {
        $id = intval(mosGetParam($_REQUEST, 'id', 1));
        $catid = intval(mosGetParam($_REQUEST, 'catid', 2));
        //first get the thread id for the current post to later on determine the parent post
        $database->setQuery("SELECT `thread` FROM #__fb_messages WHERE id='$id' AND catid='$catid'");
        $threadid = $database->loadResult();
        //load topic post and details
        $database->setQuery("SELECT a.*, b.message FROM #__fb_messages AS a, #__fb_messages_text AS b WHERE a.thread = $threadid AND a.catid=$catid AND a.parent=0 AND a.id=b.mesid");
        $row = $database->loadObjectList();
                check_dberror("Unable to load message details.");

        $mes_text = $row[0]->message;
        filterHTML($mes_text);

		if (file_exists(JB_JABSPATH . '/includes/class.ezpdf.php')) {
			include (JB_JABSPATH . '/includes/class.ezpdf.php');
			$pdf = &new Cezpdf('a4', 'P'); //A4 Portrait
		} elseif (class_exists('JDocument')) {
        	$pdf = &new fbpdfwrapper();
		} else {
			echo 'strange... no supported pdf class found!';
			exit;
		}
        $pdf->ezSetCmMargins(2, 1.5, 1, 1);
        $pdf->selectFont('./fonts/Helvetica.afm'); //choose font

        $all = $pdf->openObject();
        $pdf->saveState();
        $pdf->setStrokeColor(0, 0, 0, 1);

        // footer
        $pdf->line(10, 40, 578, 40);
        $pdf->line(10, 822, 578, 822);
        $pdf->addText(30, 34, 6, $fbConfig->board_title . ' - ' . $mosConfig_sitename);

        $strtmp = _FB_PDF_VERSION;
        $strtmp = str_replace('%version%', "NEW VERSION GOES HERE" /*$fbConfig->version*/, $strtmp); // TODO: fxstein - Need to change version handling
        $pdf->addText(250, 34, 6, $strtmp);
        $strtmp = _FB_PDF_DATE;
        $strtmp = str_replace('%date%', date('j F, Y, H:i', FBTools::fbGetShowTime()), $strtmp);
        $pdf->addText(450, 34, 6, $strtmp);

        $pdf->restoreState();
        $pdf->closeObject();
        $pdf->addObject($all, 'all');
        $pdf->ezSetDy(30);

        $txt0 = $row[0]->subject;
        $pdf->ezText($txt0, 14);
        $pdf->ezText(_VIEW_POSTED . " " . $row[0]->name . " - " . date(_DATETIME, $row[0]->time), 8);
        $pdf->ezText("_____________________________________", 8);
        //$pdf->line( 10, 780, 578, 780 );

        $txt3 = "\n";
        $txt3 .= stripslashes($mes_text);
        $pdf->ezText($txt3, 10);
        $pdf->ezText("\n============================================================================\n\n", 8);
        //now let's try to see if there's more...
        $database->setQuery("SELECT a.*, b.message FROM #__fb_messages AS a, #__fb_messages_text AS b WHERE a.catid=$catid AND a.thread=$threadid AND a.id=b.mesid AND a.parent != 0 ORDER BY a.time ASC");
        $replies = $database->loadObjectList();
                check_dberror("Unable to load messages & detail.");

        $countReplies = count($replies);

        if ($countReplies > 0)
        {
            foreach ($replies as $reply)
            {
                $mes_text = $reply->message;
                filterHTML($mes_text);

                $txt0 = $reply->subject;
                $pdf->ezText($txt0, 14);
                $pdf->ezText(_VIEW_POSTED . " " . $reply->name . " - " . date(_DATETIME, $reply->time), 8);
                $pdf->ezText("_____________________________________", 8);
                $txt3 = "\n";
                $txt3 .= stripslashes($mes_text);
                $pdf->ezText($txt3, 10);
                $pdf->ezText("\n============================================================================\n\n", 8);
            }
        }

        $pdf->ezStream();
    }
    else {
        echo "You don't have access to this resource. Your IP address has been logged and the System Admininstrator of this Web Site has been sent an email with these error details.";
    }
} //function dofreepdf

function filterHTML(&$string)
{
    // Ugly but needed to get rid of all the stuff the PDF class cant handle
	$string = str_replace('<p>', "\n\n", $string);
    $string = str_replace('<P>', "\n\n", $string);
    $string = str_replace('<br />', "\n", $string);
    $string = str_replace('<br>', "\n", $string);
    $string = str_replace('<BR />', "\n", $string);
    $string = str_replace('<BR>', "\n", $string);
    $string = str_replace('<li>', "\n - ", $string);
    $string = str_replace('<LI>', "\n - ", $string);
    $string = strip_tags($string);
    $string = str_replace('{mosimage}', '', $string);
    $string = str_replace('{mospagebreak}', '', $string);
    // bbcode
    $string = preg_replace("/\[(.*?)\]/si", "", $string);
    $string = decodeHTML($string);
}

function decodeHTML($string)
{
    $string = strtr($string, array_flip(get_html_translation_table(HTML_ENTITIES)));
    $string = preg_replace("/&#([0-9]+);/me", "chr('\\1')", $string);
    return $string;
}

function get_php_setting($val)
{
    $r = (ini_get($val) == '1' ? 1 : 0);
    return $r ? 'ON' : 'OFF';
}

dofreePDF ($database);
?>