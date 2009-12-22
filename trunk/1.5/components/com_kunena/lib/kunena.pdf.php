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
* @Copyright (C) 2006 - 2007 Best Of Joomla All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.bestofjoomla.com
*
* Based on Joomlaboard Component
* @copyright (C) 2000 - 2004 TSMF / Jan de Graaff / All Rights Reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author TSMF & Jan de Graaff
**/

defined( '_JEXEC' ) or die('Restricted access');

global $kunena_db;

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

function dofreePDF($kunena_db)
{
    global $aro_group, $kunena_is_admin;

    $app =& JFactory::getApplication();

    $kunena_acl = &JFactory::getACL();
    $kunena_my = &JFactory::getUser();
    $kunena_config =& CKunenaConfig::getInstance();

    require_once (KUNENA_PATH_LIB .DS. 'kunena.authentication.php');
    $kunena_is_moderator = 0;

    $catid = JRequest::getInt('catid', 2);

    if (!$kunena_is_admin)
    {
        $kunena_db->setQuery("SELECT userid FROM #__fb_moderation WHERE catid='{$catid}' AND userid='{$kunena_my->id}'");

        if ($kunena_db->loadResult()) {
            $kunena_is_moderator = 1;
        }
    }
    else {
        $kunena_is_moderator = 1;
    } //superadmins always are

    if (!$kunena_is_moderator)
    {
        //get all the info on this forum:
        $kunena_db->setQuery("SELECT id, pub_access, pub_recurse, admin_access, admin_recurse FROM #__fb_categories WHERE id='{$catid}'");
        $row = $kunena_db->loadObjectList();
                check_dberror("Unable to load category detail.");


        $allow_forum = explode(',', CKunenaTools::getAllowedForums($kunena_my->id, $aro_group->id, $kunena_acl));
    }

	if ($kunena_is_moderator || in_array($catid, $allow_forum))
    {
        $id = JRequest::getInt('id', 1);
        $catid = JRequest::getInt('catid', 2);
        //first get the thread id for the current post to later on determine the parent post
        $kunena_db->setQuery("SELECT thread FROM #__fb_messages WHERE id='{$id}' AND catid='{$catid}'");
        $threadid = $kunena_db->loadResult();
        //load topic post and details
        $kunena_db->setQuery("SELECT a.*, b.* FROM #__fb_messages AS a, #__fb_messages_text AS b WHERE a.thread='{$threadid}' AND a.catid='{$catid}' AND a.parent='0' AND a.id=b.mesid");
        $row = $kunena_db->loadObjectList();
                check_dberror("Unable to load message details.");

        $mes_text = $row[0]->message;
        filterHTML($mes_text);

		if (file_exists(KUNENA_ROOT_PATH .DS. 'includes/class.ezpdf.php')) {
			include (KUNENA_ROOT_PATH .DS. 'includes/class.ezpdf.php');
			$pdf = new Cezpdf('a4', 'P'); //A4 Portrait
		} elseif (class_exists('JDocument')) {
        	$pdf = new fbpdfwrapper();
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
        $pdf->addText(30, 34, 6, $kunena_config->board_title . ' - ' . $app->getCfg('sitename'));

        $strtmp = _KUNENA_PDF_VERSION;
        $strtmp = str_replace('%version%', "NEW VERSION GOES HERE" /*$kunena_config->version*/, $strtmp); // TODO: fxstein - Need to change version handling
        $pdf->addText(250, 34, 6, $strtmp);
        $strtmp = _KUNENA_PDF_DATE;
        $strtmp = str_replace('%date%', date('j F, Y, H:i', CKunenaTools::fbGetShowTime()), $strtmp);
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
        $kunena_db->setQuery("SELECT a.*, b.* FROM #__fb_messages AS a, #__fb_messages_text AS b WHERE a.catid='{$catid}' AND a.thread='{$threadid}' AND a.id=b.mesid AND a.parent!='0' ORDER BY a.time ASC");
        $replies = $kunena_db->loadObjectList();
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
        echo "You don't have access to this resource.";
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
    $string = preg_replace('/\[(.*?)\]/si', "", $string);
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

dofreePDF ($kunena_db);
?>
