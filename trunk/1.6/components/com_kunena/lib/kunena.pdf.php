<?php
/**
* @version $Id$
* Kunena Component
* @package Kunena
*
* @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.org
*
* Based on FireBoard Component
* @Copyright (C) 2006 - 2007 Best Of Joomla All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.bestofjoomla.com
*
* Based on Joomlaboard Component
* @copyright (C) 2000 - 2004 TSMF / Jan de Graaff / All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author TSMF & Jan de Graaff
**/

defined( '_JEXEC' ) or die();


$kunena_db  = &JFactory::getDBO();

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
		$this->_text .= '<font size='. ($size-11) .'>' . str_replace("\n", '<br />', $text) . '</font><br />';
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

function dofreePDF()
{
    $kunena_app =& JFactory::getApplication();
	$kunena_db  = &JFactory::getDBO();
    $kunena_acl = &JFactory::getACL();
    $kunena_my = &JFactory::getUser();
    $kunena_config = KunenaFactory::getConfig ();

    $catid = JRequest::getInt('catid', 0);
	$id = JRequest::getInt('id', 0);
	$limit = JRequest::getInt ( 'limit', 0 );
	$limitstart = JRequest::getInt ( 'limitstart', 0 );
	if ($limit < 1) $limit = $kunena_config->messages_per_page-1;

	require_once (KUNENA_PATH_LIB . '/kunena.timeformat.class.php');
    $kunena_session = KunenaFactory::getSession(true);
    $kunena_session->updateAllowedForums ();
	$allow_forum = ($kunena_session->allowed <> '')?explode(',', $kunena_session->allowed):array();

	if (in_array($catid, $allow_forum) && $id)
    {
        //first get the thread id for the current post to later on determine the parent post
        $kunena_db->setQuery("SELECT thread FROM #__kunena_messages WHERE id={$kunena_db->Quote($id)} AND catid={$kunena_db->Quote($catid)}");
        $threadid = $kunena_db->loadResult();
        if (KunenaError::checkDatabaseError()) return;
        //load topic post and details
        $kunena_db->setQuery("SELECT a.*, b.* FROM #__kunena_messages AS a, #__kunena_messages_text AS b WHERE a.thread={$kunena_db->Quote($threadid)} AND a.catid={$kunena_db->Quote($catid)} AND a.parent='0' AND a.id=b.mesid");
        $row = $kunena_db->loadObjectList();
        if (KunenaError::checkDatabaseError()) return;

        if (file_exists(KUNENA_ROOT_PATH . '/includes/class.ezpdf.php')) {
			include (KUNENA_ROOT_PATH . '/includes/class.ezpdf.php');
			$pdf = new Cezpdf('a4', 'P'); //A4 Portrait
		} elseif (class_exists('JDocument')) {
        	$pdf = new fbpdfwrapper();
		} else {
			echo 'No supported pdf class found!';
			exit;
		}

		if (empty($row)) { //if the messages doesn't exist don't need to continue
        	echo '<br /><br /><div align="center">' . JText::_('COM_KUNENA_PDF_NOT_GENERATED_MESSAGE_DELETED') . '</div><br /><br />';
        	echo CKunenaLink::GetAutoRedirectHTML ( KunenaRoute::_ ( KUNENA_LIVEURLREL . '&func=showcat&catid=' . $catid ), 3500 );
    	} else {
        	$mes_text = $row[0]->message;
    	    filterHTML($mes_text);

        	$pdf->ezSetCmMargins(2, 1.5, 1, 1);
        	$pdf->selectFont('./fonts/Helvetica.afm'); //choose font

        	$all = $pdf->openObject();
        	$pdf->saveState();
        	$pdf->setStrokeColor(0, 0, 0, 1);

        	// footer
        	$pdf->line(10, 40, 578, 40);
        	$pdf->line(10, 822, 578, 822);
        	$pdf->addText(30, 34, 6, $kunena_config->board_title . ' - ' . $kunena_app->getCfg('sitename'));

        	$strtmp = JText::_('COM_KUNENA_PDF_VERSION');
        	$strtmp = str_replace('%version%', "NEW VERSION GOES HERE" /*$kunena_config->version*/, $strtmp); // TODO: fxstein - Need to change version handling
        	$pdf->addText(250, 34, 6, $strtmp);
        	$strtmp = JText::_('COM_KUNENA_PDF_DATE');
        	$strtmp = str_replace('%date%', date('j F, Y, H:i', CKunenaTimeformat::internalTime()), $strtmp);
        	$pdf->addText(450, 34, 6, $strtmp);

        	$pdf->restoreState();
        	$pdf->closeObject();
        	$pdf->addObject($all, 'all');
        	$pdf->ezSetDy(30);

        	$txt0 = $row[0]->subject;
        	$pdf->ezText($txt0, 14);
        	$pdf->ezText(JText::_('COM_KUNENA_VIEW_POSTED') . " " . $row[0]->name . " - " . CKunenaTimeformat::showDate($row[0]->time), 8);
        	$pdf->ezText("_____________________________________", 8);
        	//$pdf->line( 10, 780, 578, 780 );

        	$txt3 = "\n";
        	$txt3 .= $mes_text;
        	$pdf->ezText($txt3, 10);
        	$pdf->ezText("\n============================================================================\n\n", 8);
        	//now let's try to see if there's more...
        	$query = "SELECT a.*, b.* FROM #__kunena_messages AS a, #__kunena_messages_text AS b WHERE a.catid={$kunena_db->Quote($catid)} AND a.thread={$kunena_db->Quote($threadid)} AND a.id=b.mesid AND a.parent!='0' ORDER BY a.time ASC";
        	$kunena_db->setQuery($query, $limitstart, $limit);
        	$replies = $kunena_db->loadObjectList();
            if (KunenaError::checkDatabaseError()) return;

        	$countReplies = count($replies);

       		if ($countReplies > 0)
        	{
            	foreach ($replies as $reply)
            	{
                	$mes_text = $reply->message;
                	filterHTML($mes_text);

                	$txt0 = $reply->subject;
                	$pdf->ezText($txt0, 14);
                	$pdf->ezText(JText::_('COM_KUNENA_VIEW_POSTED') . " " . $reply->name . " - " . CKunenaTimeformat::showDate($reply->time), 8);
                	$pdf->ezText("_____________________________________", 8);
                	$txt3 = "\n";
                	$txt3 .= $mes_text;
                	$pdf->ezText($txt3, 10);
                	$pdf->ezText("\n============================================================================\n\n", 8);
           		}
        	}

        	$pdf->ezStream();
    	}
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
	$string = preg_replace ( '/\[confidential\](.*?)\[\/confidential\]/s', '', $string );
	$string = preg_replace ( '/\[ebay\](.*?)\[\/ebay\]/s', '', $string );
	$string = preg_replace ( '/\[map\](.*?)\[\/map\]/s', '', $string );
    $string = preg_replace('/\[video(.*?)\](.*?)\[\/video\]/s', "", $string);
    $string = decodeHTML($string);
}

function decodeHTML($string)
{
	require_once(JPATH_ADMINISTRATOR.'/components/com_kunena/libraries/html/parser.php');
	$string = KunenaParser::parseBBCode($string);
    return $string;
}

function get_php_setting($val)
{
    $r = (ini_get($val) == '1' ? 1 : 0);
    return $r ? 'ON' : 'OFF';
}

dofreePDF();
?>
