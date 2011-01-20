<?php
/**
 * @version		$Id: manage.php 3901 2010-11-15 14:14:02Z mahagr $
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 */
defined ( '_JEXEC' ) or die ();

kimport ( 'kunena.model' );

/**
 * Report Model for Kunena
 *
 * @package		Kunena
 * @subpackage	com_kunena
 * @since		1.6
 */
class KunenaModelReport extends KunenaModel {
	public function getData($id) {
		$db = JFactory::getDBO ();
		$query = "SELECT a.id, a.thread, a.name AS nickname, a.catid, a.userid, a.subject, b.mesid, b.message AS msg_text, c.username, c.name, c.id AS userid
							FROM #__kunena_messages AS a
							INNER JOIN #__kunena_messages_text AS b ON b.mesid = a.id
							LEFT JOIN #__users AS c ON c.id = a.userid
							WHERE a.id={$db->Quote($id)}";
		$db->setQuery ( $query );
		$row = $db->loadObject ();
		if (KunenaError::checkDatabaseError ()) 	return;

		return $row;
	}

	public function getMailMessage($reason,$text,$id) {
		$config = KunenaFactory::getConfig ();
		$me = JFactory::getUser ();
		$messagecontent = array();

		$row = $this->getData($id);


		if ($row->userid && $row->username)
			$baduser = $config->username ? $row->username : $row->name;
		else
			$baduser = $row->nickname;

		$sendername = $config->username ? $me->username : $me->name;
		$messagecontent['sendername'] = $sendername;

		if ($reason) {
			$subject = "[" . $config->board_title . " " . JText::_ ( 'COM_KUNENA_GEN_FORUM' ) . "] " . JText::_ ( 'COM_KUNENA_REPORT_MSG' ) . ": " . $reason;
		} else {
			$subject = "[" . $config->board_title . " " . JText::_ ( 'COM_KUNENA_GEN_FORUM' ) . "] " . JText::_ ( 'COM_KUNENA_REPORT_MSG' ) . ": " . $row->subject;
		}

		$messagecontent['subject'] = $subject;

		jimport ( 'joomla.environment.uri' );
		$uri = & JURI::getInstance ( JURI::base () );
		$msglink = $uri->toString ( array ('scheme', 'host', 'port' ) ) . str_replace ( '&amp;', '&', CKunenaLink::GetThreadPageURL ( 'view', $catid, $row->id, 0, NULL, $row->id ) );

		$message = "" . JText::_ ( 'COM_KUNENA_REPORT_RSENDER' ) . " " . $sendername;
		$message .= "\n";
		$message .= "" . JText::_ ( 'COM_KUNENA_REPORT_RREASON' ) . " " . $reason;
		$message .= "\n";
		$message .= "" . JText::_ ( 'COM_KUNENA_REPORT_RMESSAGE' ) . " " . $text;
		$message .= "\n\n";
		$message .= "" . JText::_ ( 'COM_KUNENA_REPORT_POST_POSTER' ) . " " . $baduser;
		$message .= "\n";
		$message .= "" . JText::_ ( 'COM_KUNENA_REPORT_POST_SUBJECT' ) . " " . $subject;
		$message .= "\n";
		$message .= "" . JText::_ ( 'COM_KUNENA_REPORT_POST_MESSAGE' ) . "\n-----\n" . $msg_text;
		$message .= "\n-----\n\n";
		$message .= "" . JText::_ ( 'COM_KUNENA_REPORT_POST_LINK' ) . " " . $msglink;
		$message .= "\n\n\n\n** Powered by Kunena! - http://www.kunena.org **";
		$message = strtr ( $message, array ('&#32;' => '' ) );

		$messagecontent['message'] = $message;

		return $messagecontent;
	}
}