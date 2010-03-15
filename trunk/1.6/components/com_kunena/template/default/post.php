<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
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
// Dont allow direct linking
defined( '_JEXEC' ) or die();


$kunena_app = & JFactory::getApplication ();
$kunena_config = & CKunenaConfig::getInstance ();
$kunena_session = & CKunenaSession::getInstance ();

global $topic_emoticons;

$kunena_my = &JFactory::getUser ();
$kunena_db = &JFactory::getDBO ();
$kunena_config =& CKunenaConfig::getInstance();

$subject = JRequest::getVar ( 'subject', '', 'POST', 'string', JREQUEST_ALLOWRAW );
$message = JRequest::getVar ( 'message', '', 'POST', 'string', JREQUEST_ALLOWRAW );
$authorname = JRequest::getVar ( 'authorname', '' );
$anonymous = JRequest::getInt('anonymous' , 0);
$email = JRequest::getVar ( 'email', '' );
$contentURL = JRequest::getVar ( 'contentURL', '' );
$subscribeMe = JRequest::getVar ( 'subscribeMe', '' );
$topic_emoticon = JRequest::getInt ( 'topic_emoticon', 0 );
$polltitle = JRequest::getString('poll_title' , 0);
$optionsnumbers = JRequest::getInt('number_total_options' , '');
$polltimetolive = JRequest::getString('poll_time_to_live' , 0);

$id = JRequest::getInt ( 'id', 0 );
$parentid = JRequest::getInt ( 'parentid', 0 );
if (! $id) {
	// Support for old $replyto variable in post reply/quote
	$id = JRequest::getInt ( 'replyto', 0 );
}
$catid = JRequest::getInt ( 'catid', 0 );
$do = JRequest::getCmd ( 'do', '' );
$action = JRequest::getCmd ( 'action', '' );

//get the token put in the message form to check that the form has been valided successfully
if($action == 'post'){
	if(JRequest::checkToken() == false) {
		$kunena_app->enqueueMessage(JText::_(COM_KUNENA_ERROR_TOKEN),'error');
		$kunena_app->redirect ( CKunenaLink::GetLatestPageAutoRedirectURL ( $kunena_config, $parentid, $kunena_config->messages_per_page, $catid) );
	}
}

$msg_cat = null;
if ($id || $parentid) {
	// Check that message and category exists and fill some information for later use
	$query = "SELECT m.*, (mm.locked OR c.locked) AS locked, t.message,
					c.name AS catname, c.parent AS catparent, c.pub_access,
					c.review, c.class_sfx, p.id AS poll_id, c.allow_anonymous,
					c.post_anonymous, c.allow_polls
				FROM #__fb_messages AS m
				INNER JOIN #__fb_messages AS mm ON mm.id=m.thread
				INNER JOIN #__fb_messages_text AS t ON t.mesid=m.id
				INNER JOIN #__fb_categories AS c ON c.id=m.catid
				LEFT JOIN #__fb_polls AS p ON m.id=p.threadid
				WHERE m.id='" . ($parentid ? $parentid : $id) . "'";

	$kunena_db->setQuery ( $query );
	$msg_cat = $kunena_db->loadObject ();
	check_dberror ( 'Unable to check message.' );

	if (! $msg_cat) {
		echo JText::_('COM_KUNENA_POST_INVALID');
		return;
	}

	// Load attachments
	$attachments = array();

	if ($do == "edit"){
		// Only populate attachments if we go into edit mode
		$query = "SELECT * FROM #__kunena_attachments
					WHERE mesid ='" . $id . "'";
		$kunena_db->setQuery ( $query );
		$attachments = $kunena_db->loadObjectList ();
		check_dberror ( 'Unable to load attachments' );

		$this->msg_html->attachments = array();

		foreach($attachments as $attachment)
		{
			// Check if file has been pre-processed
			if (is_null($attachment->hash)){
				// This attachment has not been processed.
				// It migth be a legacy file, or the settings might have been reset.
				// Force recalculation ...

				// TODO: Perform image re-prosessing
			}

			// shorttype based on MIME type to determine if image for displaying purposes
			$attachment->shorttype = (stripos($attachment->filetype, 'image/') !== false) ? 'image' : $attachment->filetype;

			$this->msg_html->attachments[] = $attachment;
		}
	}

	// Make sure that category id is from the message (post may have been moved)
	if ($do != 'domovepost' && $do != 'domergepost' && $do != 'dosplit') {
		$catid = $msg_cat->catid;
	}
} else if ($catid) {
	// Check that category exists and fill some information for later use
	$kunena_db->setQuery ( "SELECT 0 AS id, id AS catid, name AS catname, parent AS catparent, pub_access, locked, review, class_sfx, allow_anonymous, post_anonymous, allow_polls FROM #__fb_categories WHERE id='{$catid}'" );
	$msg_cat = $kunena_db->loadObject ();
	check_dberror ( 'Unable to load category.' );
	if (! $msg_cat) {
		echo JText::_('COM_KUNENA_NO_ACCESS');
		return;
	}
}

// Check user access rights
$allow_forum = ($kunena_session->allowed != '') ? explode ( ',', $kunena_session->allowed ) : array ();

if (($kunena_my->id==0 && !$kunena_config->pubwrite) || (empty($msg_cat->catparent) && $do != 'reply') &&
	(! in_array ( $catid, $allow_forum ) && ! CKunenaTools::isAdmin ())) {
	CKunenaTools::loadTemplate('/plugin/login/login.php');
	return;
}

//reset variables used
$this->kunena_editmode = 0;

$poll_exist = null;
if(!empty($optionsnumbers) && !empty($polltitle))
{
  $poll_exist = "1";
  //Begin Poll management options
  $optionvalue = array();
  for($ioptions = 0; $ioptions < $optionsnumbers; $ioptions++){
    $optionvalue[] = JRequest::getString('field_option'.$ioptions , null);
  }
}

//ip for floodprotection, post logging, subscriptions, etcetera
$ip = $_SERVER ["REMOTE_ADDR"];

// Flood protection
if ($kunena_config->floodprotection && ($action == "post" || $do == 'quote' || $do == 'reply') && ! CKunenaTools::isAdmin ()) {
	$kunena_db->setQuery ( "SELECT MAX(time) FROM #__fb_messages WHERE ip='{$ip}'" );
	$lastPostTime = $kunena_db->loadResult ();
	check_dberror ( "Unable to load max time for current request from IP: $ip" );

	if ($lastPostTime + $kunena_config->floodprotection > CKunenaTimeformat::internalTime()) {
		echo JText::_('COM_KUNENA_POST_TOPIC_FLOOD1') . ' ' . $kunena_config->floodprotection . ' ' . JText::_('COM_KUNENA_POST_TOPIC_FLOOD2') . '<br />';
		echo JText::_('COM_KUNENA_POST_TOPIC_FLOOD3');
		return;
	}
}

//Let's find out who we're dealing with if a registered user wants to make a post
if ($kunena_my->id) {
	$my_name = $kunena_config->username ? $kunena_my->username : $kunena_my->name;
	$this->email = $kunena_my->email;
	if (CKunenaTools::isModerator ( $kunena_my->id, $catid )) {
		if (! empty ( $authorname ))
			$my_name = $authorname;
		if (! empty ( $email ))
			$this->email = $email;
	}
} else {
	$my_name = $authorname;
	$this->email = (isset ( $email ) && ! empty ( $email )) ? $email : '';
}
?>

<table border="0" cellspacing="0" cellpadding="0" width="100%"
	align="center">
	<tr>
		<td><?php
		CKunenaTools::loadTemplate('/pathway.php');

		if ($action == "post" && (!$msg_cat->locked || CKunenaTools::isModerator ( $kunena_my->id, $catid ) )) {
			?>

		<table border="0" cellspacing="1" cellpadding="3" width="70%"
			align="center" class="contentpane">
			<tr>
				<td><?php
			$parent = ( int ) $parentid;
			jimport( 'joomla.mail.helper' );
			if ($catid==0 || empty($msg_cat)) {
				echo JText::_('COM_KUNENA_POST_ERROR_NO_CATEGORY');
			} else if ($msg_cat->catparent == 0) {
				echo JText::_('COM_KUNENA_POST_ERROR_IS_SECTION');
			} else if ($anonymous && !$msg_cat->allow_anonymous) {
				echo JText::_('COM_KUNENA_POST_ERROR_ANONYMOUS_FORBITTEN');
			} else if (empty ( $my_name )) {
				echo JText::_('COM_KUNENA_POST_FORGOT_NAME');
			} else if (!$kunena_my->id && $kunena_config->askemail && empty ( $this->email )) {
				echo JText::_('COM_KUNENA_POST_FORGOT_EMAIL');
			} else if ($kunena_config->askemail && ! JMailHelper::isEmailAddress($this->email)) {
				echo JText::_('COM_KUNENA_MY_EMAIL_INVALID');
			} else if (empty ( $subject )) {
				echo JText::_('COM_KUNENA_POST_FORGOT_SUBJECT');
			} else if (empty ( $message )) {
				echo JText::_('COM_KUNENA_POST_FORGOT_MESSAGE');
			} else {
				if ($parent == 0) {
					$thread = 0;
				}

				if ($msg_cat->id == 0) {
					// bad parent, create a new post
					$parent = 0;
					$thread = 0;
				} else {

					$thread = $msg_cat->parent == 0 ? $msg_cat->id : $msg_cat->thread;
				}

				$messagesubject = $subject; //before we add slashes and all... used later in mail

				$userid = $kunena_my->id;
				if ($anonymous) {
					// Anonymous post: remove all user information from the post
					$userid = 0;
					jimport('joomla.user.helper');
					if ($authorname == $kunena_my->name || $authorname == $kunena_my->username || JUserHelper::getUserId($authorname)) {
						$my_name = JText::_('COM_KUNENA_USERNAME_ANONYMOUS');
					} else {
						$my_name = $authorname;
					}
					$this->email = '';
					$ip = '';
				}

				$authorname = addslashes ( JString::trim ( $my_name ) );
				$subject = addslashes ( JString::trim ( $subject ) );
				$message = addslashes ( JString::trim ( $message ) );
				$email = addslashes ( JString::trim ( $this->email ) );
				$topic_emoticon = (!isset($topic_emoticons[$topic_emoticon])) ? 0 : $topic_emoticon;
				$posttime = CKunenaTimeformat::internalTime();
				if ($contentURL) {
					$message = $contentURL . "\n\n" . $message;
				}

				//check if the post must be reviewed by a moderator prior to showing
				$holdPost = 0;
				if (! CKunenaTools::isModerator ( $kunena_my->id, $catid )) {
					$holdPost = $msg_cat->review;
				}

				// DO NOT PROCEED if there is an exact copy of the message already in the db
				$duplicatetimewindow = $posttime - $kunena_config->fbsessiontimeout;
				$kunena_db->setQuery ( "SELECT m.id FROM #__fb_messages AS m JOIN #__fb_messages_text AS t ON m.id=t.mesid WHERE m.userid='{$userid}' AND m.name='{$authorname}' AND m.email='{$email}' AND m.subject='{$subject}' AND m.ip='{$ip}' AND t.message='{$message}' AND m.time>='{$duplicatetimewindow}'" );
				$pid = ( int ) $kunena_db->loadResult ();
				check_dberror ( 'Unable to load post.' );

				if ($pid) {
					// We get here in case we have detected a double post
					// We did not do any further processing and just display the failure message
					echo '<br /><br /><div align="center">' . JText::_('COM_KUNENA_POST_DUPLICATE_IGNORED') . '</div><br /><br />';
					echo CKunenaLink::GetLatestPostAutoRedirectHTML ( $kunena_config, $pid, $kunena_config->messages_per_page, $catid );
				} else {
					$kunena_db->setQuery ( "INSERT INTO #__fb_messages
                                    						(parent,thread,catid,name,userid,email,subject,time,ip,topic_emoticon,hold)
                                    						VALUES('$parent','$thread','$catid'," . $kunena_db->quote ( $authorname ) . ",'{$userid}'," . $kunena_db->quote ( $email ) . "," . $kunena_db->quote ( $subject ) . ",'$posttime','$ip','$topic_emoticon','$holdPost')" );

					if (! $kunena_db->query ()) {
						echo JText::_('COM_KUNENA_POST_ERROR_MESSAGE');
					} else {
						$pid = $kunena_db->insertId ();
						//Insert in the database the informations for the poll and the options for the poll
                        if (!empty($polltitle) && !empty($optionsnumbers))
                        {
                        	CKunenaPolls::save_new_poll($polltimetolive,$polltitle,$pid,$optionvalue);
                        }

						// now increase the #s in categories only case approved
						if ($holdPost == 0) {
							CKunenaTools::modifyCategoryStats ( $pid, $parent, $posttime, $catid );
						}

						$kunena_db->setQuery ( "INSERT INTO #__fb_messages_text (mesid,message) VALUES('$pid'," . $kunena_db->quote ( $message ) . ")" );
						$kunena_db->query ();

						// A couple more tasks required...
						if ($thread == 0) {
							//if thread was zero, we now know to which id it belongs, so we can determine the thread and update it
							$kunena_db->setQuery ( "UPDATE #__fb_messages SET thread='$pid' WHERE id='$pid'" );
							$kunena_db->query ();

							// if JomScoial integration is active integrate user points and activity stream
							if ($kunena_config->pm_component == 'jomsocial' || $kunena_config->fb_profile == 'jomsocial' || $kunena_config->avatar_src == 'jomsocial') {
								include_once (KUNENA_ROOT_PATH . DS . 'components/com_community/libraries/userpoints.php');

								CuserPoints::assignPoint ( 'com_kunena.thread.new' );

								// Check for permisions of the current category - activity only if public or registered
								if ($msg_cat->pub_access == 0 || $msg_cat->pub_access == -1) {
									if ($kunena_config->js_actstr_integration) {
										//activity stream  - new post
										$JSPostLink = CKunenaLink::GetThreadPageURL ( $kunena_config, 'view', $catid, $pid, 1 );

										$kunena_emoticons = smile::getEmoticons ( 1 );
										$content = stripslashes ( $message );
										$content = smile::smileReplace ( $content, 0, $kunena_config->disemoticons, $kunena_emoticons );
										$content = nl2br ( $content );

										$act = new stdClass ( );
										$act->cmd = 'wall.write';
										$act->actor = $userid;
										$act->target = 0; // no target
										$act->title = JText::_ ( '{actor} ' . JText::_('COM_KUNENA_JS_ACTIVITYSTREAM_CREATE_MSG1') . ' <a href="' . $JSPostLink . '">' . stripslashes ( $subject ) . '</a> ' . JText::_('COM_KUNENA_JS_ACTIVITYSTREAM_CREATE_MSG2') );
										$act->content = $content;
										$act->app = 'wall';
										$act->cid = 0;

										// jomsocial 0 = public, 20 = registered members
										if ($msg_cat->pub_access == 0){
											$act->access = 0;
										}
										else {
											$act->access = 20;
										}

										CFactory::load ( 'libraries', 'activities' );
										CActivityStream::add ( $act );
									}
								}
							}

						} else {
							// if JomScoial integration is active integrate user points and activity stream
							if ($kunena_config->pm_component == 'jomsocial' || $kunena_config->fb_profile == 'jomsocial' || $kunena_config->avatar_src == 'jomsocial') {
								include_once (KUNENA_ROOT_PATH . DS . 'components/com_community/libraries/userpoints.php');

								CuserPoints::assignPoint ( 'com_kunena.thread.reply' );

								// Check for permisions of the current category - activity only if public or registered
								if ($msg_cat->pub_access == 0 || $msg_cat->pub_access == -1 && $kunena_config->js_actstr_integration) {
									if ($kunena_config->js_actstr_integration) {
										//activity stream - reply post
										$JSPostLink = CKunenaLink::GetThreadPageURL ( $kunena_config, 'view', $catid, $thread, 1 );

										$content = stripslashes ( $message );
										$content = smile::smileReplace ( $content, 0, $kunena_config->disemoticons, $kunena_emoticons );
										$content = nl2br ( $content );

										$act = new stdClass ( );
										$act->cmd = 'wall.write';
										$act->actor = $userid;
										$act->target = 0; // no target
										$act->title = JText::_ ( '{single}{actor}{/single}{multiple}{actors}{/multiple} ' . JText::_('COM_KUNENA_JS_ACTIVITYSTREAM_REPLY_MSG1') . ' <a href="' . $JSPostLink . '">' . stripslashes ( $subject ) . '</a> ' . JText::_('COM_KUNENA_JS_ACTIVITYSTREAM_REPLY_MSG2') );
										$act->content = $content;
										$act->app = 'wall';
										$act->cid = 0;

										// jomsocial 0 = public, 20 = registered members
										if ($msg_cat->pub_access == 0){
											$act->access = 0;
										}
										else {
											$act->access = 20;
										}

										CFactory::load ( 'libraries', 'activities' );
										CActivityStream::add ( $act );
									}
								}
							}
						}
						// End Modify for activities stream

						CKunenaTools::markTopicRead($pid, $kunena_my->id);

						//update the user posts count
						if ($userid) {
							$kunena_db->setQuery ( "UPDATE #__fb_users SET posts=posts+1 WHERE userid={$userid}" );
							$kunena_db->query ();
						}

						//Update the attachments table if an image has been attached
						require_once (KUNENA_PATH_LIB .DS. 'kunena.attachments.class.php');
						$attachments = CKunenaAttachments::getInstance();
						$attachments->assign($pid);
						$fileinfos = $attachments->multiupload($pid);
						foreach ($fileinfos as $fileinfo) {
							if (!$fileinfo['status']) $kunena_app->enqueueMessage ( JText::sprintf ( 'COM_KUNENA_UPLOAD_FAILED', $fileinfo[name]).': '.$fileinfo['error'], 'error' );
						}

						// Perform proper page pagination for better SEO support
						// used in subscriptions and auto redirect back to latest post
						if ($thread == 0) {
							$querythread = $pid;
						} else {
							$querythread = $thread;
						}

						$kunena_db->setQuery ( "SELECT * FROM #__fb_sessions WHERE readtopics LIKE '%$thread%' AND userid!={$kunena_my->id}" );
						$sessions = $kunena_db->loadObjectList ();
						check_dberror ( "Unable to load sessions." );
						foreach ( $sessions as $session ) {
							$readtopics = $session->readtopics;
							$userid = $session->userid;
							$rt = explode ( ",", $readtopics );
							$key = array_search ( $thread, $rt );
							if ($key !== FALSE) {
								unset ( $rt [$key] );
								$readtopics = implode ( ",", $rt );
								$kunena_db->setQuery ( "UPDATE #__fb_sessions SET readtopics='$readtopics' WHERE userid=$userid" );
								$kunena_db->query ();
								check_dberror ( "Unable to update sessions." );
							}
						}

						$kunena_db->setQuery ( "SELECT COUNT(*) AS totalmessages FROM #__fb_messages WHERE thread='{$querythread}'" );
						$result = $kunena_db->loadObject ();
						check_dberror ( "Unable to load messages." );
						$threadPages = ceil ( $result->totalmessages / $kunena_config->messages_per_page );
						//construct a useable URL (for plaintext - so no &amp; encoding!)
						jimport ( 'joomla.environment.uri' );
						$uri = & JURI::getInstance ( JURI::base () );
						$LastPostUrl = $uri->toString ( array ('scheme', 'host', 'port' ) ) . str_replace ( '&amp;', '&', CKunenaLink::GetThreadPageURL ( $kunena_config, 'view', $catid, $querythread, $threadPages, $kunena_config->messages_per_page, $pid ) );

						// start integration alphauserpoints component
						if ($kunena_config->alphauserpointsrules) {
							// Insert AlphaUserPoints rules
							$api_AUP = JPATH_SITE . DS . 'components' . DS . 'com_alphauserpoints' . DS . 'helper.php';
							$datareference = '<a href="' . $LastPostUrl . '">' . $subject . '</a>';
							if (file_exists ( $api_AUP )) {
								require_once ($api_AUP);
								if ($thread == 0) {
									// rule for post a new topic
									AlphaUserPointsHelper::newpoints ( 'plgaup_newtopic_kunena', '', $pid, $datareference );
								} else {
									// rule for post a reply to a topic
									if ($kunena_config->alphauserpointsnumchars > 0) {
										// use if limit chars for a response
										if (JString::strlen ( $message ) > $kunena_config->alphauserpointsnumchars) {
											AlphaUserPointsHelper::newpoints ( 'plgaup_reply_kunena', '', $pid, $datareference );
										} else {
											$kunena_app->enqueueMessage ( JText::_('COM_KUNENA_AUP_MESSAGE_TOO_SHORT') );
										}
									} else {
										AlphaUserPointsHelper::newpoints ( 'plgaup_reply_kunena', '', $pid, $datareference );
									}
								}
							}
						}
						// end insertion AlphaUserPoints

						//get all subscriptions and moderators
						$emailToList = CKunenaTools::getEMailToList($catid, $querythread, $kunena_config->allowsubscriptions && !$holdPost,
							$kunena_config->mailmod, $kunena_config->mailadmin, $kunena_my->id);

						if (count($emailToList)) {
							if (! $kunena_config->email  || ! JMailHelper::isEmailAddress($kunena_config->email)) {
								$kunena_app->enqueueMessage (JText::_('COM_KUNENA_EMAIL_INVALID'), 'error' );
							} else {
							// clean up the message for review
							$mailmessage = smile::purify ( stripslashes ( $message ) );

							$mailsender = JMailHelper::cleanAddress( stripslashes ( $kunena_config->board_title ) . " " . JText::_('COM_KUNENA_GEN_FORUM') );
							$mailsubject = JMailHelper::cleanSubject("[" . stripslashes ( $kunena_config->board_title ) . " " . JText::_('COM_KUNENA_GEN_FORUM') . "] " . stripslashes ( $messagesubject ) . " (" . stripslashes ( $msg_cat->catname ) . ")");

							foreach ( $emailToList as $emailTo ) {
								if (! $emailTo->email || ! JMailHelper::isEmailAddress($emailTo->email)) continue;

								if ($emailTo->subscription) {
									$msg1 = JText::_('COM_KUNENA_POST_EMAIL_NOTIFICATION1');
									$msg2 = JText::_('COM_KUNENA_POST_EMAIL_NOTIFICATION2');
								} else {
									$msg1 = JText::_('COM_KUNENA_POST_EMAIL_MOD1');
									$msg2 = JText::_('COM_KUNENA_POST_EMAIL_MOD2');
								}

								$msg = "$emailTo->name,\n\n";
								$msg .=  $msg1 . " " . stripslashes ( $kunena_config->board_title ) . " " . JText::_('COM_KUNENA_GEN_FORUM') . "\n\n";
								$msg .= JText::_('COM_KUNENA_GEN_SUBJECT') . ": " . stripslashes ( $messagesubject ) . "\n";
								$msg .= JText::_('COM_KUNENA_GEN_FORUM') . ": " . stripslashes ( $msg_cat->catname ) . "\n";
								$msg .= JText::_('COM_KUNENA_VIEW_POSTED') . ": " . stripslashes ( $authorname ) . "\n\n";
								$msg .= $msg2 . "\n";
								$msg .= "URL: $LastPostUrl\n\n";
								if ($kunena_config->mailfull == 1) {
									$msg .= JText::_('COM_KUNENA_GEN_MESSAGE') . ":\n-----\n";
									$msg .= $mailmessage;
									$msg .= "\n-----";
								}
								$msg .= "\n\n";
								$msg .= JText::_('COM_KUNENA_POST_EMAIL_NOTIFICATION3') . "\n";
								$msg .= "\n\n\n\n";
								$msg .= "** Powered by Kunena! - http://www.Kunena.com **";
								$msg = JMailHelper::cleanBody($msg);

								if ($ip != "127.0.0.1") {
									JUtility::sendMail ( $kunena_config->email, $mailsender, $emailTo->email, $mailsubject, $msg );
								}
							}
						}
						}

						$redirectmsg = '';

						//now try adding any new subscriptions if asked for by the poster
						if ($subscribeMe == 1) {
							if ($thread == 0) {
								$fb_thread = $pid;
							} else {
								$fb_thread = $thread;
							}

							$kunena_db->setQuery ( "INSERT INTO #__fb_subscriptions (thread,userid) VALUES ('$fb_thread','{$kunena_my->id}')" );

							if (@$kunena_db->query ()) {
								$redirectmsg .= JText::_('COM_KUNENA_POST_SUBSCRIBED_TOPIC') . '<br />';
							} else {
								$redirectmsg .= JText::_('COM_KUNENA_POST_NO_SUBSCRIBED_TOPIC') . '<br />';
							}
						}

						if ($holdPost == 1) {
							$redirectmsg .= JText::_('COM_KUNENA_POST_SUCCES_REVIEW');
						} else {
							$redirectmsg .= JText::_('COM_KUNENA_POST_SUCCESS_POSTED');
						}
						$kunena_app->redirect ( CKunenaLink::GetLatestPageAutoRedirectURL ( $kunena_config, $pid, $kunena_config->messages_per_page, $catid), $redirectmsg );
					}
				}
			}
			?>
				</td>
			</tr>
		</table>

		<?php
		} else if ($action == "cancel") {
			echo '<br /><br /><div align="center">' . JText::_('COM_KUNENA_SUBMIT_CANCEL') . "</div><br />";
			echo CKunenaLink::GetLatestPostAutoRedirectHTML ( $kunena_config, $pid, $kunena_config->messages_per_page, $catid );
		} else {
			if (($do == 'quote' || $do == 'reply')  && (!$catid || !$msg_cat->locked || CKunenaTools::isModerator ( $kunena_my->id, $catid ) )) {
				$parentid = 0;
				if ($catid && $msg_cat->id > 0) {
					$message = $msg_cat;
					$parentid = $message->id;
					if ($do == 'quote') {
						$this->message_text = "[b]" . kunena_htmlspecialchars ( stripslashes ( $message->name ) ) . " " . JText::_('COM_KUNENA_POST_WROTE') . ":[/b]\n";
						$this->message_text .= '[quote]' . kunena_htmlspecialchars ( stripslashes ( $message->message ) ) . "[/quote]";
					} else {
						$this->message_text = '';
					}
					$reprefix = JString::substr ( stripslashes ( $message->subject ), 0, JString::strlen ( JText::_('COM_KUNENA_POST_RE') ) ) != JText::_('COM_KUNENA_POST_RE') ? JText::_('COM_KUNENA_POST_RE') . ' ' : '';
					$this->subject = kunena_htmlspecialchars ( stripslashes ( $message->subject ) );
					$this->resubject = $reprefix . $this->subject;
				} else {
					$this->message_text = '';
					$this->resubject = '';

					$options = array();
					if (empty($msg_cat->allow_anonymous)) $this->selectcatlist = CKunenaTools::forumSelectList('postcatid', $catid, $options, '');
				}
				$this->authorName = kunena_htmlspecialchars ( $my_name );
				$this->id = $id;
				$this->parentid = $parentid;
				$this->catid = $catid;
				$this->emoid = 0;
				$this->action = 'post';

				$this->allow_anonymous = !empty($msg_cat->allow_anonymous) && $kunena_my->id;
				$this->anonymous = ($this->allow_anonymous && !empty($msg_cat->post_anonymous));
				$this->allow_name_change = 0;
				if (!$kunena_my->id || $kunena_config->changename || !empty($msg_cat->allow_anonymous) || CKunenaTools::isModerator ( $kunena_my->id, $this->catid )) {
					$this->allow_name_change = 1;
				}

				CKunenaTools::loadTemplate('/write.html.php');
			} else if ($do == "newFromBot" && (!$msg_cat->locked || CKunenaTools::isModerator ( $kunena_my->id, $catid ) )) {
				// The Mosbot "discuss on forums" has detected an unexisting thread and wants to create one
				$parentid = 0;
				$this->kunena_set_focus = 0;
				$resubject = base64_decode ( strtr ( $resubject, "()", "+/" ) );
				$resubject = str_replace ( '%20', ' ', $resubject );
				$resubject = preg_replace ( '/%32/', '&', $resubject );
				$resubject = preg_replace ( '/%33/', ';', $resubject );
				$resubject = preg_replace ( '/\'/', '&#039;', $resubject );
				$this->resubject = preg_replace ( '/\"/', '&quot;', $resubject );
				$this->kunena_from_bot = 1; //this new topic comes from the discuss mambot
				$this->authorName = kunena_htmlspecialchars ( $my_name );
				$rowid = JRequest::getInt ( 'rowid', 0 );
				$rowItemid = JRequest::getInt ( 'rowItemid', 0 );

				if (!$rowItemid) $rowItemid = 1;
				$contentURL = CKunenaLink::GetContentView( $rowid, $rowItemid ) ;

				$this->contentURL = JText::_('COM_KUNENA_POST_DISCUSS') . ': [url=' . $contentURL . ']' . $resubject . '[/url]';
				$this->id = $id;
				$this->parentid = $parentid;
				$this->catid = $catid;
				$this->emoid = 0;
				$this->action = 'bot';

				$this->allow_anonymous = !empty($msg_cat->allow_anonymous) && $kunenna_my->id;
				$this->anonymous = ($this->allow_anonymous && !empty($msg_cat->post_anonymous));
				$this->allow_name_change = 0;
				if (!$kunena_my->id || $kunena_config->changename || !empty($msg_cat->allow_anonymous) || CKunenaTools::isModerator ( $kunena_my->id, $this->catid )) {
					$this->allow_name_change = 1;
				}

				//get the writing stuff in:
				CKunenaTools::loadTemplate('/write.html.php');
			} else if ($do == "edit" && (!$msg_cat->locked || CKunenaTools::isModerator ( $kunena_my->id, $catid ) )) {
				$message = $msg_cat;

				$allowEdit = 0;
				if (CKunenaTools::isModerator ( $kunena_my->id, $catid )) {
					// Moderator can edit any message
					$allowEdit = 1;
				} else if ($kunena_config->useredit == 1 && $kunena_my->id && $kunena_my->id == $message->userid) {
					// Registered users can edit their own messages, if it is allowed
					if ($kunena_config->useredittime == 0) {
						$allowEdit = 1;
					} else {
						//Check whether edit is in time
						$modtime = $message->modified_time;
						if (! $modtime) {
							$modtime = $message->time;
						}
						if ($modtime + $kunena_config->useredittime >= CKunenaTimeformat::internalTime()) {
							$allowEdit = 1;
						}
					}
				}

				if ($allowEdit == 1) {
					$this->kunena_editmode = 1;

					$this->message_text = kunena_htmlspecialchars ( stripslashes ( $message->message ) );
					$this->resubject = kunena_htmlspecialchars ( stripslashes ( $message->subject ) );
					$this->authorName = kunena_htmlspecialchars ( stripslashes ( $message->name ) );
					$this->email = kunena_htmlspecialchars ( stripslashes ( $message->email ) );
					$this->id = $message->id;
					$this->catid = $message->catid;
					$this->parentid = 0;
					$this->emoid = $message->topic_emoticon;
					$this->action = 'edit';

					//save the options for query after and load the text options, the number options is for create the fields in the form after
					if ($message->poll_id) {
						$this->polldatasedit = CKunenaPolls::get_poll_data($id);
						if ($this->kunena_editmode) {
							$this->polloptionstotal = count($this->polldatasedit);
						}
					}

					$this->allow_anonymous = !empty($msg_cat->allow_anonymous) && $message->userid;
					$this->anonymous = 0;
					$this->allow_name_change = 0;
					if (!$kunena_my->id || $kunena_config->changename || !empty($msg_cat->allow_anonymous) || CKunenaTools::isModerator ( $kunena_my->id, $this->catid )) {
						$this->allow_name_change = 1;
					}

					//get the writing stuff in:
					CKunenaTools::loadTemplate('/write.html.php');
				} else {
					$kunena_app->redirect ( CKunenaLink::GetKunenaURL(true), JText::_('COM_KUNENA_POST_NOT_MODERATOR') );
				}
			} else if ($do == "editpostnow" && (!$msg_cat->locked || CKunenaTools::isModerator ( $kunena_my->id, $catid ) )) {
				$modified_reason = addslashes ( JRequest::getVar ( "modified_reason", null ) );
				$modified_by = $kunena_my->id;
				$modified_time = CKunenaTimeformat::internalTime();

				$query = "SELECT a.*, b.*, p.id AS poll_id FROM #__fb_messages AS a
							LEFT JOIN #__fb_messages_text AS b ON a.id=b.mesid
							LEFT JOIN #__fb_polls AS p ON a.id=p.threadid
							WHERE a.id='$id'";

				$kunena_db->setQuery ( $query );

				$message1 = $kunena_db->loadObjectList ();
				check_dberror ( "Unable to load message." );
				$mes = $message1 [0];
				$userid = $mes->userid;

				//Check for a moderator or superadmin
				if (CKunenaTools::isModerator ( $kunena_my->id, $catid )) {
					$allowEdit = 1;
				}

				if ($kunena_config->useredit == 1 && $kunena_my->id != 0) {
					//Now, if the author==viewer and the viewer is allowed to edit his/her own post the let them edit
					if ($kunena_my->id == $userid) {
						if ((( int ) $kunena_config->useredittime) == 0) {
							$allowEdit = 1;
						} else {
							$modtime = $mes->modified_time;
							if (! $modtime) {
								$modtime = $mes->time;
							}
							if (($modtime + (( int ) $kunena_config->useredittime) + (( int ) $kunena_config->useredittimegrace)) >= CKunenaTimeformat::internalTime()) {
								$allowEdit = 1;
							}
						}
					}
				}

				if ($allowEdit == 1) {
					$subject = addslashes ( JString::trim ( $subject ) );
					$message = addslashes ( JString::trim ( $message ) );

					//parse the message for some preliminary bbcode and stripping of HTML
					//$message = smile::bbencode_first_pass($message);


					if (count ( $message1 ) > 0) {
						// Re-check the hold. If post gets edited and review is set to ON for this category


						// check if the post must be reviewed by a Moderator prior to showing
						// doesn't apply to admin/moderator posts ;-)
						$holdPost = 0;

						if (! CKunenaTools::isModerator ( $kunena_my->id, $catid )) {
							$kunena_db->setQuery ( "SELECT review FROM #__fb_categories WHERE id='{$catid}'" );
							$kunena_db->query () or check_dberror ( 'Unable to load review flag from categories.' );
							$holdPost = $kunena_db->loadResult ();
						}

					//update the poll when an user edit his post
                    if ($kunena_config->pollenabled)
                    {
                    	$optvalue = array();
                        for ($i = 0; $i < $optionsnumbers; $i++)
                        {
                        	$optvalue[] = JRequest::getString('field_option'.$i , null);
                         }
                         //need to check if the poll exist, if it's not the case the poll is insered like new poll
                         if (!$mes->poll_id)
                         {
                         	CKunenaPolls::save_new_poll($polltimetolive,$polltitle,$id,$optvalue);
                         }
                         else
                         {
                         	if (empty($polltitle) && empty($optionsnumbers))
                         	{
                            	//The poll is deleted because the polltitle and the options are empty
                                CKunenaPolls::delete_poll($id);
                             }
                             else
                             {
                             	CKunenaPolls::update_poll_edit($polltimetolive,$id,$polltitle,$optvalue,$optionsnumbers);
                              }
                           }
                        }

						if (!$kunena_my->id && !$kunena_config->askemail){
							jimport( 'joomla.mail.helper' );
							if (empty($email) || ! JMailHelper::isEmailAddress($kunena_config->email)) {
								$email = $mes->email;
							}
						}

					if ($anonymous) {
						// Anonymous post: remove all user information from the post
						jimport ( 'joomla.user.helper' );
						if (JUserHelper::getUserId ( $authorname )) {
							$authorname = JText::_ ( 'COM_KUNENA_USERNAME_ANONYMOUS' );
						}
						$modified = "modified_by='0', modified_time='0' ,modified_reason='', ";
						if ($kunena_config->editmarkup && $kunena_my->id != $mes->userid) {
							$modified = "modified_by='" . $modified_by . "', modified_time='" . $modified_time . "' ,modified_reason=" . $kunena_db->quote ( $modified_reason ) .", ";
						}
						$kunena_db->setQuery ( "UPDATE #__fb_messages SET userid='0', name=" . $kunena_db->quote ( $authorname ) . ", email='', ip='', " . $modified . " subject=" . $kunena_db->quote ( $subject ) . ", topic_emoticon='" . $topic_emoticon . "', hold='" . (( int ) $holdPost) . "' WHERE id={$id}" );
					} else {
						$kunena_db->setQuery ( "UPDATE #__fb_messages SET name=" . $kunena_db->quote ( $authorname ) . ", email=" . $kunena_db->quote ( addslashes ( $email ) ) . (($kunena_config->editmarkup) ? " ,modified_by='" . $modified_by . "' ,modified_time='" . $modified_time . "' ,modified_reason=" . $kunena_db->quote ( $modified_reason ) : "") . ", subject=" . $kunena_db->quote ( $subject ) . ", topic_emoticon='" . $topic_emoticon . "', hold='" . (( int ) $holdPost) . "' WHERE id={$id}" );
					}
						$dbr_nameset = $kunena_db->query ();
						$kunena_db->setQuery ( "UPDATE #__fb_messages_text SET message=" . $kunena_db->quote ( $message ) . " WHERE mesid='{$id}'" );

						if ($kunena_db->query () && $dbr_nameset) {
							//Update the attachments table if an file has been attached
							require_once (KUNENA_PATH_LIB .DS. 'kunena.attachments.class.php');
							$attachments = CKunenaAttachments::getInstance();
							$attachments->assign($id);
							$fileinfos = $attachments->multiupload($pid);
							foreach ($fileinfos as $fileinfo) {
								if (!$fileinfo['status']) $kunena_app->enqueueMessage ( JText::sprintf ( 'COM_KUNENA_UPLOAD_FAILED', $fileinfo[name]).': '.$fileinfo['error'], 'error' );
							}

							$kunena_app->redirect ( CKunenaLink::GetLatestPageAutoRedirectURL ( $kunena_config, $id, $kunena_config->messages_per_page, $catid), JText::_('COM_KUNENA_POST_SUCCESS_EDIT') );
						} else {
							$kunena_app->enqueueMessage ( JText::_('COM_KUNENA_POST_ERROR_MESSAGE_OCCURED'), 'error' );
							$kunena_app->redirect ( CKunenaLink::GetKunenaURL(true));
						}
					} else {
						$kunena_app->enqueueMessage ( JText::_('COM_KUNENA_POST_INVALID'), 'error' );
						$kunena_app->redirect ( CKunenaLink::GetKunenaURL(true));
					}
				} else {
					$kunena_app->redirect ( CKunenaLink::GetKunenaURL(true), JText::_('COM_KUNENA_POST_NOT_MODERATOR') );
				}
			} else if ($do == "deleteownpost") {
				$delete = $delete = CKunenaTools::userOwnDelete ($id);
				if (!$delete) {
					$message = JText::_('COM_KUNENA_POST_OWN_DELETE_ERROR');
				} else {
					$message = JText::_('COM_KUNENA_POST_SUCCESS_DELETE');
				}

				$kunena_app->redirect ( CKunenaLink::GetCategoryURL('showcat' , $catid, true ), $message );
			} else if ($do == "delete") {
				require_once (KUNENA_PATH_LIB.'/kunena.moderation.class.php');
				$kunena_mod = CKunenaModeration::getInstance();

				$delete = $kunena_mod->deleteMessage($id, $DeleteAttachments = false);
				if (!$delete) {
					$message = $kunena_mod->getErrorMessage();
				} else {
					$message = JText::_('COM_KUNENA_POST_SUCCESS_DELETE');
				}

				$kunena_app->redirect ( CKunenaLink::GetCategoryURL('showcat' , $catid, true ), $message );

			} //fi $do==deletepostnow
			else if ($do == "deletethread") {
				require_once (KUNENA_PATH_LIB.'/kunena.moderation.class.php');
				$kunena_mod = CKunenaModeration::getInstance();

				$delete = $kunena_mod->deleteThread($id, $DeleteAttachments = false);
				if (!$delete) {
					$message = $kunena_mod->getErrorMessage();
				} else {
					$message = JText::_('COM_KUNENA_POST_SUCCESS_DELETE');
				}

				$kunena_app->redirect ( CKunenaLink::GetCategoryURL('showcat' , $catid, true ), $message );
			} //fi $do==deletethreadnow
			else if ($do == "move") {
				if (! CKunenaTools::isModerator ( $kunena_my->id, $catid )) {
					$kunena_app->redirect ( CKunenaLink::GetKunenaURL(true), JText::_('COM_KUNENA_POST_NOT_MODERATOR') );
				}

				$options = array();
				$selectlist = CKunenaTools::forumSelectList('postmove', 0, $options, ' size="15" class="kmove_selectbox"');

				// get topic subject:
				$kunena_db->setQuery ( "SELECT subject, id FROM #__fb_messages WHERE id='{$id}'" );
				$topicSubject = $kunena_db->loadResult ();
				check_dberror ( "Unable to load messages." );
				?>

		<form action="<?php
				echo CKunenaLink::GetPostURL();
				?>"
			method="post" name="myform"><input type="hidden" name="do"
			value="domovepost" /> <input type="hidden" name="id"
			value="<?php
				echo $id;
				?>" />

		<p><?php
				echo JText::_('COM_KUNENA_GEN_TOPIC');
				?>: <strong><?php
				echo kunena_htmlspecialchars ( stripslashes ( $topicSubject ) );
				?></strong> <br />

		<br />
		<?php
				echo JText::_('COM_KUNENA_POST_MOVE_TOPIC');
				?>: <br />

		<?php echo $selectlist; ?> <br />

		<input type="checkbox" checked name="leaveGhost" value="1" /> <?php
				echo JText::_('COM_KUNENA_POST_MOVE_GHOST');
				?>

		<br />

		<input type="submit" class="button"
			value="<?php
				echo JText::_('COM_KUNENA_GEN_MOVE');
				?>" /></p>
		</form>

		<?php
			} else if ($do == "domovepost") {
				$leaveGhost = JRequest::getInt ( 'leaveGhost', 0 );
				require_once (KUNENA_PATH_LIB.'/kunena.moderation.class.php');
				$kunena_mod = CKunenaModeration::getInstance();

				$move = $kunena_mod->moveThread($id, $catid, $leaveGhost);
				if (!$move) {
					$message = $kunena_mod->getErrorMessage();
				} else {
					$message = JText::_('COM_KUNENA_POST_SUCCESS_MOVE');
				}

				$kunena_app->redirect ( CKunenaLink::GetCategoryURL('showcat' , $catid, true ), $message );
			} else if ($do == "mergethread") {
				if (! CKunenaTools::isModerator ( $kunena_my->id, $catid )) {
					$kunena_app->redirect ( CKunenaLink::GetKunenaURL(true), JText::_('COM_KUNENA_POST_NOT_MODERATOR') );
				}

				//get topic subject:
				$kunena_db->setQuery ( "SELECT subject, thread FROM #__fb_messages WHERE id='{$id}'" );
				$topicDetails = $kunena_db->loadObjectlist ();
				check_dberror ( "Unable to load specific message." );

				// get messages list:
				$kunena_db->setQuery ( "SELECT parent,subject,thread,id FROM #__fb_messages WHERE hold=0 AND catid='{$catid}'" );
				$messagesList = $kunena_db->loadObjectlist ();
				check_dberror ( "Unable to load messages." );

				foreach ($messagesList as $mes) {
					if ($mes->parent == '0' && $topicDetails[0]->thread != $mes->thread ) {
						$message[] = JHTML::_('select.option', $mes->id, kunena_htmlspecialchars ( stripslashes ( $mes->subject ) ) );
					}
				}
				$lists = JHTML::_('select.genericlist', $message, 'mergepost', 'class="inputbox" multiple="multiple" size="15"', 'value', 'text');
				?>
		<form action="<?php
				echo CKunenaLink::GetPostURL();
				?>"
			method="post" name="myform"><input type="hidden" name="do"
			value="domergethreadnow" /> <input type="hidden" name="id"
			value="<?php
				echo $id;
				?>" /> <input type="hidden" name="catid"
			value="<?php
				echo $catid;
				?>" />

		<p><?php
				echo JText::_('COM_KUNENA_GEN_TOPIC');
				?>: <strong><?php
				echo kunena_htmlspecialchars ( stripslashes ( $topicDetails[0]->subject ) );
				?></strong> <br />

		<br />
		<?php
				echo JText::_('COM_KUNENA_BUTTON_MERGE_TOPIC');
				?>: <br />

		<?php echo $lists; ?> <br />

		<br />

		<input type="submit" class="button"
			value="<?php
				echo JText::_('COM_KUNENA_BUTTON_MERGE');
				?>" /></p>
		</form>

		<?php
			} else if ($do == "domergethreadnow") {
				$TargetThreadID = JRequest::getInt ( 'mergepost', null );
				require_once (KUNENA_PATH_LIB.'/kunena.moderation.class.php');
				$kunena_mod = &CKunenaModeration::getInstance();

				$merge = $kunena_mod->moveMessageAndNewer($id, $catid, $TargetSubject = '', $TargetThreadID );
				if (!$merge) {
					$message = $kunena_mod->getErrorMessage();
				} else {
					$message = JText::_('COM_KUNENA_POST_SUCCESS_MERGE');
				}

				$kunena_app->redirect ( CKunenaLink::GetLatestPageAutoRedirectURL ( $kunena_config, $$TargetThreadID, $kunena_config->messages_per_page ), $message );

		} else if ($do == "merge") {
				if (! CKunenaTools::isModerator ( $kunena_my->id, $catid )) {
					$kunena_app->redirect ( CKunenaLink::GetKunenaURL(true), JText::_('COM_KUNENA_POST_NOT_MODERATOR') );
				}

				//get topic subject:
				$kunena_db->setQuery ( "SELECT subject, thread FROM #__fb_messages WHERE id='{$id}'" );
				$topicDetails = $kunena_db->loadObjectlist ();
				check_dberror ( "Unable to load specific message." );

				// get messages list:
				$kunena_db->setQuery ( "SELECT parent,subject,thread,id FROM #__fb_messages WHERE hold=0 AND catid='{$catid}'" );
				$messagesList = $kunena_db->loadObjectlist ();
				check_dberror ( "Unable to load messages." );

				foreach ($messagesList as $mes) {
					if ($mes->parent == '0' && $topicDetails[0]->thread != $mes->thread ) {
						$message[] = JHTML::_('select.option', $mes->id, kunena_htmlspecialchars ( stripslashes ( $mes->subject ) ) );
					}
				}
				$lists = JHTML::_('select.genericlist', $message, 'mergepost', 'class="inputbox" multiple="multiple" size="15"', 'value', 'text');
				?>
		<form action="<?php
				echo CKunenaLink::GetPostURL();
				?>"
			method="post" name="myform"><input type="hidden" name="do"
			value="domergepostnow" /> <input type="hidden" name="id"
			value="<?php
				echo $id;
				?>" /> <input type="hidden" name="catid"
			value="<?php
				echo $catid;
				?>" />

		<p><?php
				echo JText::_('COM_KUNENA_GEN_TOPIC');
				?>: <strong><?php
				echo kunena_htmlspecialchars ( stripslashes ( $topicDetails[0]->subject ) );
				?></strong> <br />

		<br />
		<?php
				echo JText::_('COM_KUNENA_BUTTON_MERGE_TOPIC');
				?>: <br />

		<?php echo $lists; ?> <br />

		<br />

		<input type="submit" class="button"
			value="<?php
				echo JText::_('COM_KUNENA_BUTTON_MERGE');
				?>" /></p>
		</form>

		<?php
			} else if ($do == "domergepostnow") {
				$TargetThreadID = JRequest::getInt ( 'mergepost', null );
				require_once (KUNENA_PATH_LIB.'/kunena.moderation.class.php');
				$kunena_mod = &CKunenaModeration::getInstance();

				$merge = $kunena_mod->moveMessage($id, $catid, $TargetSubject = '', $TargetThreadID );
				if (!$merge) {
					$message = $kunena_mod->getErrorMessage();
				} else {
					$message = JText::_('COM_KUNENA_POST_SUCCESS_MERGE');
				}

				$kunena_app->redirect ( CKunenaLink::GetLatestPageAutoRedirectURL ( $kunena_config, $$TargetThreadID, $kunena_config->messages_per_page ), $message );
			} else if ($do == "split") {
				if (! CKunenaTools::isModerator ( $kunena_my->id, $catid )) {
					$kunena_app->redirect ( CKunenaLink::GetKunenaURL(true), JText::_('COM_KUNENA_POST_NOT_MODERATOR') );
				}

				//get topic subject:
				$kunena_db->setQuery ( "SELECT subject FROM #__fb_messages WHERE id='{$id}'" );
				$topicSubject = $kunena_db->loadResult ();
				check_dberror ( "Unable to load specific message." );

				// get messages list:
				$kunena_db->setQuery ( "SELECT name,parent,id FROM #__fb_categories " );
				$categoriesList = $kunena_db->loadObjectlist ();
				check_dberror ( "Unable to load categories." );

				foreach ($categoriesList as $cat) {
					if ($cat->parent != '0') {
						$message[] = JHTML::_('select.option', $cat->id, kunena_htmlspecialchars ( stripslashes ( $cat->name ) ) );
					}
				}
				$lists = JHTML::_('select.genericlist', $message, 'targetcat', 'class="inputbox" multiple="multiple" size="8"', 'value', 'text');
				$lists2 = JHTML::_('select.genericlist', $message, 'targetcat2', 'class="inputbox" multiple="multiple" size="8"', 'value', 'text');
		?>
				<form action="<?php
				echo CKunenaLink::GetPostURL();
				?>"
			method="post" name="myform"><input type="hidden" name="do"
			value="splitnow" /> <input type="hidden" name="id"
			value="<?php
				echo $id;
				?>" /> <input type="hidden" name="messubeject"
			value="<?php
				echo stripslashes($topicSubject);
				?>" />

		<p><?php
				echo JText::_('COM_KUNENA_GEN_TOPIC');
				?>: <strong><?php
				echo kunena_htmlspecialchars ( stripslashes ( $topicSubject ) );
				?></strong> <br />

		<br />
		<?php
				echo JText::_('COM_KUNENA_BUTTON_SPLIT_TOPIC');
				?>: <br />

		<input type="radio" name="split" value="splitpost" /> Split only this message<br />
		<?php echo $lists; ?><br />
		<!-- <input type="radio" name="split" value="splitmultpost" > Split this message and messages following:<br />-->

		<br />

		<input type="submit" class="button"
			value="<?php
				echo JText::_('COM_KUNENA_BUTTON_SPLIT_TOPIC');
				?>" /></p>
		</form>
<?php
			} else if ($do == "splitnow") {
				require_once (KUNENA_PATH_LIB.'/kunena.moderation.class.php');
				$kunena_mod = &CKunenaModeration::getInstance();

				$mode = JRequest::getVar ( 'split', null );
				$TargetSubject = JRequest::getVar ( 'messubeject', null );

				if ( $mode == 'splitpost') { // we split only the message specified
					$TargetCatID = JRequest::getVar ( 'targetcat', null );

					$splitpost = $kunena_mod->moveMessage($id, $TargetCatID, $TargetSubject , '0');
					if (!$splitpost) {
						$message = $kunena_mod->getErrorMessage();
					} else {
						$message = JText::_('COM_KUNENA_POST_SUCCESS_SPLIT');
					}

				} else { // we split the message specified and the replies to this message
					$TargetCatID = JRequest::getVar ( 'targetcat2', null );
					$splitpost = $kunena_mod->moveMessageAndNewer($id, $TargetCatID, $TargetSubject , $TargetThreadID = 0);
					if (!$splitpost) {
						$message = $kunena_mod->getErrorMessage();
					} else {
						$message = JText::_('COM_KUNENA_POST_SUCCESS_SPLIT');
					}
				}

				$kunena_app->redirect ( CKunenaLink::GetLatestPageAutoRedirectURL ( $kunena_config, $id, $kunena_config->messages_per_page ), $message );
			} else if ($do == "subscribe") {
				$success_msg = JText::_('COM_KUNENA_POST_NO_SUBSCRIBED_TOPIC');
				$kunena_db->setQuery ( "SELECT thread FROM #__fb_messages WHERE id='{$id}'" );
				if ($id && $kunena_my->id && $kunena_db->query ()) {
					$thread = $kunena_db->loadResult ();
					$kunena_db->setQuery ( "INSERT INTO #__fb_subscriptions (thread,userid) VALUES ('$thread','$kunena_my->id')" );

					if (@$kunena_db->query () && $kunena_db->getAffectedRows () == 1) {
						$success_msg = JText::_('COM_KUNENA_POST_SUBSCRIBED_TOPIC');
					}
				}
				$kunena_app->redirect ( CKunenaLink::GetLatestPageAutoRedirectURL ( $kunena_config, $id, $kunena_config->messages_per_page ), $success_msg );
			} else if ($do == "unsubscribe") {
				$success_msg = JText::_('COM_KUNENA_POST_NO_UNSUBSCRIBED_TOPIC');
				$kunena_db->setQuery ( "SELECT MAX(thread) AS thread FROM #__fb_messages WHERE id='{$id}'" );
				if ($id && $kunena_my->id && $kunena_db->query ()) {
					$thread = $kunena_db->loadResult ();
					$kunena_db->setQuery ( "DELETE FROM #__fb_subscriptions WHERE thread=$thread AND userid=$kunena_my->id" );

					if ($kunena_db->query () && $kunena_db->getAffectedRows () == 1) {
						$success_msg = JText::_('COM_KUNENA_POST_UNSUBSCRIBED_TOPIC');
					}
				}
				$kunena_app->redirect ( CKunenaLink::GetLatestPageAutoRedirectURL ( $kunena_config, $id, $kunena_config->messages_per_page ), $success_msg );
			} else if ($do == "favorite") {
				$success_msg = JText::_('COM_KUNENA_POST_NO_FAVORITED_TOPIC');
				$kunena_db->setQuery ( "SELECT thread FROM #__fb_messages WHERE id='{$id}'" );
				if ($id && $kunena_my->id && $kunena_db->query ()) {
					$thread = $kunena_db->loadResult ();
					$kunena_db->setQuery ( "INSERT INTO #__fb_favorites (thread,userid) VALUES ('$thread','$kunena_my->id')" );

					if (@$kunena_db->query () && $kunena_db->getAffectedRows () == 1) {
						$success_msg = JText::_('COM_KUNENA_POST_FAVORITED_TOPIC');
					}
				}
				$kunena_app->redirect ( CKunenaLink::GetLatestPageAutoRedirectURL ( $kunena_config, $id, $kunena_config->messages_per_page ), $success_msg );
			} else if ($do == "unfavorite") {
				$success_msg = JText::_('COM_KUNENA_POST_NO_UNFAVORITED_TOPIC');
				$kunena_db->setQuery ( "SELECT MAX(thread) AS thread FROM #__fb_messages WHERE id='{$id}'" );
				if ($id && $kunena_my->id && $kunena_db->query ()) {
					$thread = $kunena_db->loadResult ();
					$kunena_db->setQuery ( "DELETE FROM #__fb_favorites WHERE thread=$thread AND userid=$kunena_my->id" );

					if ($kunena_db->query () && $kunena_db->getAffectedRows () == 1) {
						$success_msg = JText::_('COM_KUNENA_POST_UNFAVORITED_TOPIC');
					}
				}
				$kunena_app->redirect ( CKunenaLink::GetLatestPageAutoRedirectURL ( $kunena_config, $id, $kunena_config->messages_per_page ), $success_msg );
			} else if ($do == "sticky") {
				if (! CKunenaTools::isModerator ( $kunena_my->id, $catid )) {
					$kunena_app->redirect ( CKunenaLink::GetKunenaURL(true), JText::_('COM_KUNENA_POST_NOT_MODERATOR') );
				}

				$success_msg = JText::_('COM_KUNENA_POST_STICKY_NOT_SET');
				$kunena_db->setQuery ( "update #__fb_messages set ordering=1 where id=$id" );
				if ($id && $kunena_db->query () && $kunena_db->getAffectedRows () == 1) {
					$success_msg = JText::_('COM_KUNENA_POST_STICKY_SET');
				}
				$kunena_app->redirect ( CKunenaLink::GetLatestPageAutoRedirectURL ( $kunena_config, $id, $kunena_config->messages_per_page ), $success_msg );
			} else if ($do == "unsticky") {
				if (! CKunenaTools::isModerator ( $kunena_my->id, $catid )) {
					$kunena_app->redirect ( CKunenaLink::GetKunenaURL(true), JText::_('COM_KUNENA_POST_NOT_MODERATOR') );
				}

				$success_msg = JText::_('COM_KUNENA_POST_STICKY_NOT_UNSET');
				$kunena_db->setQuery ( "update #__fb_messages set ordering=0 where id=$id" );
				if ($id && $kunena_db->query () && $kunena_db->getAffectedRows () == 1) {
					$success_msg = JText::_('COM_KUNENA_POST_STICKY_UNSET');
				}
				$kunena_app->redirect ( CKunenaLink::GetLatestPageAutoRedirectURL ( $kunena_config, $id, $kunena_config->messages_per_page ), $success_msg );
			} else if ($do == "lock") {
				if (! CKunenaTools::isModerator ( $kunena_my->id, $catid )) {
					$kunena_app->redirect ( CKunenaLink::GetKunenaURL(true), JText::_('COM_KUNENA_POST_NOT_MODERATOR') );
				}

				$success_msg = JText::_('COM_KUNENA_POST_LOCK_NOT_SET');
				$kunena_db->setQuery ( "update #__fb_messages set locked=1 where id=$id" );
				if ($id && $kunena_db->query () && $kunena_db->getAffectedRows () == 1) {
					$success_msg = JText::_('COM_KUNENA_POST_LOCK_SET');
				}
				$kunena_app->redirect ( CKunenaLink::GetLatestPageAutoRedirectURL ( $kunena_config, $id, $kunena_config->messages_per_page ), $success_msg );
			} else if ($do == "unlock") {
				if (! CKunenaTools::isModerator ( $kunena_my->id, $catid )) {
					$kunena_app->redirect ( CKunenaLink::GetKunenaURL(true), JText::_('COM_KUNENA_POST_NOT_MODERATOR') );
				}

				$success_msg = JText::_('COM_KUNENA_POST_LOCK_NOT_UNSET');
				$kunena_db->setQuery ( "update #__fb_messages set locked=0 where id=$id" );
				if ($id && $kunena_db->query () && $kunena_db->getAffectedRows () == 1) {
					$success_msg = JText::_('COM_KUNENA_POST_LOCK_UNSET');
				}
				$kunena_app->redirect ( CKunenaLink::GetLatestPageAutoRedirectURL ( $kunena_config, $id, $kunena_config->messages_per_page ), $success_msg );
			} else if ($do == "approve") {
				if (! CKunenaTools::isModerator ( $kunena_my->id, $catid )) {
					$kunena_app->redirect ( CKunenaLink::GetKunenaURL(true), JText::_('COM_KUNENA_POST_NOT_MODERATOR') );
				}

				$success_msg = JText::_('COM_KUNENA_POST_LOCK_NOT_UNSET');
				$kunena_db->setQuery ( "UPDATE #__fb_messages SET hold=0 WHERE id=$id" );
				if ($id && $kunena_db->query () && $kunena_db->getAffectedRows () == 1) {
					$success_msg = JText::_('COM_KUNENA_MODERATE_1APPROVE_SUCCESS');
				}
				$kunena_app->redirect ( CKunenaLink::GetLatestPageAutoRedirectURL ( $kunena_config, $id, $kunena_config->messages_per_page ), $success_msg );
			}
		}
		?>
		</td>
	</tr>
</table>

<?php
function listThreadHistory($id, $kunena_config, $kunena_db) {
	if ($id != 0) {
		//get the parent# and  the thread#  for the post on which 'reply' or 'quote' is chosen
		$kunena_db->setQuery ( "SELECT thread,parent FROM #__fb_messages WHERE id='{$id}'" );
		$this_message = $kunena_db->loadObject ();
		check_dberror ( "Unable to load messages." );

		//determine the correct thread# for the entire thread
		if ($this_message->parent == 0) {
			$thread = $id;
		} else {
			$thread = $this_message->thread;
		}

		//get all the messages for this thread
		$kunena_db->setQuery ( "SELECT * FROM #__fb_messages AS m LEFT JOIN #__fb_messages_text AS t ON m.id=t.mesid WHERE (thread='{$thread}' OR id='{$thread}') AND hold='0' ORDER BY time DESC LIMIT " . $kunena_config->historylimit );
		$messages = $kunena_db->loadObjectList ();
		check_dberror ( "Unable to load messages." );
		//and the subject of the first thread (for reference)
		$kunena_db->setQuery ( "SELECT subject FROM #__fb_messages WHERE id='{$thread}' and parent='0'" );
		$this_message_subject = $kunena_db->loadResult ();
		check_dberror ( "Unable to load messages." );
		echo "<b>" . JText::_('COM_KUNENA_POST_TOPIC_HISTORY') . ":</b> " . kunena_htmlspecialchars ( stripslashes ( $this_message_subject ) ) . " <br />" . JText::_('COM_KUNENA_POST_TOPIC_HISTORY_MAX') . " $kunena_config->historylimit " . JText::_('COM_KUNENA_POST_TOPIC_HISTORY_LAST') . "<br />";
		?>

<table border="0" cellspacing="1" cellpadding="3" width="100%"
	class="kreview_table">
	<tr>
		<td class="kreview_header" width="20%" align="center"><strong><?php
		echo JText::_('COM_KUNENA_GEN_AUTHOR');
		?></strong></td>

		<td class="kreview_header" align="center"><strong><?php
		echo JText::_('COM_KUNENA_GEN_MESSAGE');
		?></strong></td>
	</tr>

	<?php
		$k = 0;
		$kunena_emoticons = smile::getEmoticons ( 1 );

		foreach ( $messages as $mes ) {
			$k = 1 - $k;
			$mes->name = kunena_htmlspecialchars ( $mes->name );
			$mes->email = kunena_htmlspecialchars ( $mes->email );
			$mes->subject = kunena_htmlspecialchars ( $mes->subject );

			$fb_message_txt = stripslashes ( ($mes->message) );
			$fb_message_txt = smile::smileReplace ( $fb_message_txt, 1, $kunena_config->disemoticons, $kunena_emoticons );
			$fb_message_txt = nl2br ( $fb_message_txt );
			$fb_message_txt = str_replace ( "__FBTAB__", "\t", $fb_message_txt );

			?>

	<tr>
		<td class="kreview_body<?php
			echo $k;
			?>" valign="top"><?php
			echo stripslashes ( $mes->name );
			?>
		</td>

		<td class="kreview_body<?php
			echo $k;
			?>">
		<div class="msgtext"><?php
			$fb_message_txt = str_replace ( "</P><br />", "</P>", $fb_message_txt );
			$fb_message_txt = CKunenaTools::prepareContent ( $fb_message_txt );

			echo $fb_message_txt;
			?>
		</div>
		</td>
	</tr>

	<?php
		}
		?>
</table>

<?php
	} //else: this is a new topic so there can't be a history
}
?>
<!-- Begin: Forum Jump -->
<div class="k_bt_cvr1">
<div class="k_bt_cvr2">
<div class="k_bt_cvr3">
<div class="k_bt_cvr4">
<div class="k_bt_cvr5">
<table class="kblocktable" id="kbottomarea">
	<thead>
		<tr>
			<th class="th-right"><?php
			//(JJ) FINISH: CAT LIST BOTTOM
			if ($kunena_config->enableforumjump) {
				CKunenaTools::loadTemplate('/forumjump.php');
			}
			?>
			</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td></td>
		</tr>
	</tbody>
</table>
</div>
</div>
</div>
</div>
</div>
<!-- Finish: Forum Jump -->
