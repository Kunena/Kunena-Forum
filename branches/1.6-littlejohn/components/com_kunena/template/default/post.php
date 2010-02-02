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

global $imageLocation, $fileLocation, $board_title, $topic_emoticons;

$kunena_my = &JFactory::getUser ();
$kunena_db = &JFactory::getDBO ();
$kunena_config =& CKunenaConfig::getInstance();

$subject = JRequest::getVar ( 'subject', '', 'POST', 'string', JREQUEST_ALLOWRAW );
$message = JRequest::getVar ( 'message', '', 'POST', 'string', JREQUEST_ALLOWRAW );
$attachfile = JRequest::getVar ( 'attachfile', NULL, 'FILES', 'array' );
$attachimage = JRequest::getVar ( 'attachimage', NULL, 'FILES', 'array' );
$authorname = JRequest::getVar ( 'authorname', '' );
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

if ($id || $parentid) {
	// Check that message and category exists and fill some information for later use
	$query = "SELECT m.*, (mm.locked OR c.locked) AS locked, t.message,
					c.name AS catname, c.parent AS catparent, c.pub_access,
					c.review, c.class_sfx, p.id AS poll_id, c.allow_polls
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
		echo _POST_INVALID;
		return;
	}
	// Make sure that category id is from the message (post may have been moved)
	if ($do != 'domovepost' && $do != 'domergepost' && $do != 'dosplit') {
		$catid = $msg_cat->catid;
	}
} else if ($catid) {
	// Check that category exists and fill some information for later use
	$kunena_db->setQuery ( "SELECT 0 AS id, id AS catid, name AS catname, parent AS catparent, pub_access, locked, review, class_sfx, allow_polls FROM #__fb_categories WHERE id='{$catid}'" );
	$msg_cat = $kunena_db->loadObject ();
	check_dberror ( 'Unable to load category.' );
	if (! $msg_cat) {
		echo _KUNENA_NO_ACCESS;
		return;
	}
}

// Check user access rights
$kunena_is_admin = CKunenaTools::isAdmin ();
$allow_forum = ($kunena_session->allowed != '') ? explode ( ',', $kunena_session->allowed ) : array ();
if (($kunena_my->id==0 && !$kunena_config->pubwrite) || (empty($msg_cat->catparent) && $do != 'reply') && (! in_array ( $catid, $allow_forum ) && ! $kunena_is_admin)) {
	//echo _KUNENA_NO_ACCESS;
		if (file_exists ( KUNENA_ABSTMPLTPATH . DS . 'plugin' . DS . 'login' . DS . 'login.php')) {
			include (KUNENA_ABSTMPLTPATH . DS . 'plugin' . DS . 'login' . DS . 'login.php');
		} else {
			include (KUNENA_PATH_TEMPLATE_DEFAULT . DS . 'plugin' . DS . 'login' . DS . 'login.php');
		}
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
if ($kunena_config->floodprotection && ($action == "post" || $do == 'quote' || $do == 'reply') && ! $kunena_is_admin) {
	$kunena_db->setQuery ( "SELECT MAX(time) FROM #__fb_messages WHERE ip='{$ip}'" );
	$lastPostTime = $kunena_db->loadResult ();
	check_dberror ( "Unable to load max time for current request from IP: $ip" );

	if ($lastPostTime + $kunena_config->floodprotection > CKunenaTimeformat::internalTime()) {
		echo _POST_TOPIC_FLOOD1 . ' ' . $kunena_config->floodprotection . ' ' . _POST_TOPIC_FLOOD2 . '<br />';
		echo _POST_TOPIC_FLOOD3;
		return;
	}
}

//Let's find out who we're dealing with if a registered user wants to make a post
if ($kunena_my->id) {
	$my_name = $kunena_config->username ? $kunena_my->username : $kunena_my->name;
	$this->email = $kunena_my->email;
	$this->kunena_registered_user = 1;
	if (CKunenaTools::isModerator ( $kunena_my->id, $catid )) {
		if (! empty ( $authorname ))
			$my_name = $authorname;
		if (! empty ( $email ))
			$this->email = $email;
	}
} else {
	$my_name = $authorname;
	$this->email = (isset ( $email ) && ! empty ( $email )) ? $email : '';
	$this->kunena_registered_user = 0;
}
?>

<table border="0" cellspacing="0" cellpadding="0" width="100%"
	align="center">
	<tr>
		<td><?php
		if (file_exists ( KUNENA_ABSTMPLTPATH . '/pathway.php' )) {
			require_once (KUNENA_ABSTMPLTPATH . '/pathway.php');
		} else {
			require_once (KUNENA_PATH_TEMPLATE_DEFAULT . DS . 'pathway.php');
		}

		if ($action == "post" && (!$msg_cat->locked || CKunenaTools::isModerator ( $kunena_my->id, $catid ) )) {
			?>

		<table border="0" cellspacing="1" cellpadding="3" width="70%"
			align="center" class="contentpane">
			<tr>
				<td><?php
			$parent = ( int ) $parentid;

			if (empty ( $my_name )) {
				echo _POST_FORGOT_NAME;
			} else if ($kunena_config->askemail && empty ( $this->email )) {
				echo _POST_FORGOT_EMAIL;
			} else if (empty ( $subject )) {
				echo _POST_FORGOT_SUBJECT;
			} else if (empty ( $message )) {
				echo _POST_FORGOT_MESSAGE;
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

				if ($catid == 0) {
					echo "POST: INTERNAL ERROR: catid=0";
					return;
				}

				if (is_array ( $attachfile ) && $attachfile ['error'] != UPLOAD_ERR_NO_FILE) {
					include (KUNENA_PATH_LIB . DS . 'kunena.file.upload.php');
				}
				if (is_array ( $attachimage ) && $attachimage ['error'] != UPLOAD_ERR_NO_FILE) {
					include (KUNENA_PATH_LIB . DS . 'kunena.image.upload.php');
				}

				$messagesubject = $subject; //before we add slashes and all... used later in mail


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
				$kunena_db->setQuery ( "SELECT m.id FROM #__fb_messages AS m JOIN #__fb_messages_text AS t ON m.id=t.mesid WHERE m.userid='{$kunena_my->id}' AND m.name='{$authorname}' AND m.email='{$email}' AND m.subject='{$subject}' AND m.ip='{$ip}' AND t.message='{$message}' AND m.time>='{$duplicatetimewindow}'" );
				$pid = ( int ) $kunena_db->loadResult ();
				check_dberror ( 'Unable to load post.' );

				if ($pid) {
					// We get here in case we have detected a double post
					// We did not do any further processing and just display the failure message
					echo '<br /><br /><div align="center">' . _POST_DUPLICATE_IGNORED . '</div><br /><br />';
					echo CKunenaLink::GetLatestPostAutoRedirectHTML ( $kunena_config, $pid, $kunena_config->messages_per_page, $catid );
				} else {
					$kunena_db->setQuery ( "INSERT INTO #__fb_messages
                                    						(parent,thread,catid,name,userid,email,subject,time,ip,topic_emoticon,hold)
                                    						VALUES('$parent','$thread','$catid'," . $kunena_db->quote ( $authorname ) . ",'{$kunena_my->id}'," . $kunena_db->quote ( $email ) . "," . $kunena_db->quote ( $subject ) . ",'$posttime','$ip','$topic_emoticon','$holdPost')" );

					if (! $kunena_db->query ()) {
						echo _POST_ERROR_MESSAGE;
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
										$act->actor = $kunena_my->id;
										$act->target = 0; // no target
										$act->title = JText::_ ( '{actor} ' . _KUNENA_JS_ACTIVITYSTREAM_CREATE_MSG1 . ' <a href="' . $JSPostLink . '">' . stripslashes ( $subject ) . '</a> ' . _KUNENA_JS_ACTIVITYSTREAM_CREATE_MSG2 );
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
										$act->actor = $kunena_my->id;
										$act->target = 0; // no target
										$act->title = JText::_ ( '{single}{actor}{/single}{multiple}{actors}{/multiple} ' . _KUNENA_JS_ACTIVITYSTREAM_REPLY_MSG1 . ' <a href="' . $JSPostLink . '">' . stripslashes ( $subject ) . '</a> ' . _KUNENA_JS_ACTIVITYSTREAM_REPLY_MSG2 );
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
						if ($kunena_my->id) {
							$kunena_db->setQuery ( "UPDATE #__fb_users SET posts=posts+1 WHERE userid={$kunena_my->id}" );
							$kunena_db->query ();
						}

						//Update the attachments table if an image has been attached
						if (! empty ( $imageLocation ) && file_exists ( $imageLocation )) {
							$kunena_db->setQuery ( "INSERT INTO #__fb_attachments (mesid, filelocation) values ('$pid'," . $kunena_db->quote ( $imageLocation ) . ")" );

							if (! $kunena_db->query ()) {
								echo "<script> alert('Storing image failed: " . $kunena_db->getErrorMsg () . "'); </script>\n";
							}
						}

						//Update the attachments table if an file has been attached
						if (! empty ( $fileLocation ) && file_exists ( $fileLocation )) {
							$kunena_db->setQuery ( "INSERT INTO #__fb_attachments (mesid, filelocation) values ('$pid'," . $kunena_db->quote ( $fileLocation ) . ")" );

							if (! $kunena_db->query ()) {
								echo "<script> alert('Storing file failed: " . $kunena_db->getErrorMsg () . "'); </script>\n";
							}
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
											$kunena_app->enqueueMessage ( _KUNENA_AUP_MESSAGE_TOO_SHORT );
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
							// clean up the message for review
							$mailmessage = smile::purify ( stripslashes ( $message ) );

							foreach ( $emailToList as $emailTo ) {
								if ($emailTo->subscription) {
									$msg1 = _KUNENA_POST_EMAIL_NOTIFICATION1;
									$msg2 = _KUNENA_POST_EMAIL_NOTIFICATION2;
								} else {
									$msg1 = _KUNENA_POST_EMAIL_MOD1;
									$msg2 = _KUNENA_POST_EMAIL_MOD2;
								}

								$mailsender = stripslashes ( $board_title ) . " " . _GEN_FORUM;
								$mailsubject = "[" . stripslashes ( $board_title ) . " " . _GEN_FORUM . "] " . stripslashes ( $messagesubject ) . " (" . stripslashes ( $msg_cat->catname ) . ")";

								$msg = "$emailTo->name,\n\n";
								$msg .=  $msg1 . " " . stripslashes ( $board_title ) . " " . _GEN_FORUM . "\n\n";
								$msg .= _GEN_SUBJECT . ": " . stripslashes ( $messagesubject ) . "\n";
								$msg .= _GEN_FORUM . ": " . stripslashes ( $msg_cat->catname ) . "\n";
								$msg .= _VIEW_POSTED . ": " . stripslashes ( $authorname ) . "\n\n";
								$msg .= $msg2 . "\n";
								$msg .= "URL: $LastPostUrl\n\n";
								if ($kunena_config->mailfull == 1) {
									$msg .= _GEN_MESSAGE . ":\n-----\n";
									$msg .= $mailmessage;
									$msg .= "\n-----";
								}
								$msg .= "\n\n";
								$msg .= _KUNENA_POST_EMAIL_NOTIFICATION3 . "\n";
								$msg .= "\n\n\n\n";
								$msg .= "** Powered by Kunena! - http://www.Kunena.com **";

								if ($ip != "127.0.0.1") {
									JUtility::sendMail ( $kunena_config->email, $mailsender, $emailTo->email, $mailsubject, $msg );
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
								$redirectmsg .= _POST_SUBSCRIBED_TOPIC . '<br />';
							} else {
								$redirectmsg .= _POST_NO_SUBSCRIBED_TOPIC . '<br />';
							}
						}

						if ($holdPost == 1) {
							$redirectmsg .= _POST_SUCCES_REVIEW;
						} else {
							$redirectmsg .= _POST_SUCCESS_POSTED;
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
			echo '<br /><br /><div align="center">' . _SUBMIT_CANCEL . "</div><br />";
			echo CKunenaLink::GetLatestPostAutoRedirectHTML ( $kunena_config, $pid, $kunena_config->messages_per_page, $catid );
		} else {
			if (($do == 'quote' || $do == 'reply')  && (!$catid || !$msg_cat->locked || CKunenaTools::isModerator ( $kunena_my->id, $catid ) )) {
				$parentid = 0;
				if ($catid && $msg_cat->id > 0) {
					$message = $msg_cat;
					$parentid = $message->id;
					if ($do == 'quote') {
						$this->message_text = "[b]" . kunena_htmlspecialchars ( stripslashes ( $message->name ) ) . " " . _POST_WROTE . ":[/b]\n";
						$this->message_text .= '[quote]' . kunena_htmlspecialchars ( stripslashes ( $message->message ) ) . "[/quote]";
					} else {
						$this->message_text = '';
					}
					$reprefix = JString::substr ( stripslashes ( $message->subject ), 0, JString::strlen ( _POST_RE ) ) != _POST_RE ? _POST_RE . ' ' : '';
					$this->subject = kunena_htmlspecialchars ( stripslashes ( $message->subject ) );
					$this->resubject = $reprefix . $this->subject;
				} else {
					$this->message_text = '';
					$this->resubject = '';

					$options = array();
					$this->selectcatlist = CKunenaTools::forumSelectList('postcatid', $catid, $options, '');
				}
				$this->authorName = kunena_htmlspecialchars ( $my_name );
				$this->id = $id;
				$this->parentid = $parentid;
				$this->catid = $catid;
				$this->emoid = 0;

				if (file_exists ( KUNENA_ABSTMPLTPATH . '/write.html.php' )) {
					include (KUNENA_ABSTMPLTPATH . '/write.html.php');
				} else {
					include (KUNENA_PATH_TEMPLATE_DEFAULT . DS . 'write.html.php');
				}
			} else if ($do == "newFromBot" && (!$msg_cat->locked || CKunenaTools::isModerator ( $kunena_my->id, $catid ) )) {
				// The Mosbot "discuss on forums" has detected an unexisting thread and wants to create one
				$parentid = 0;
				$id = ( int ) $id;
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

				$this->contentURL = _POST_DISCUSS . ': [url=' . $contentURL . ']' . $resubject . '[/url]';
				$this->id = $id;
				$this->parentid = $parentid;
				$this->catid = $catid;
				$this->emoid = 0;

				//get the writing stuff in:
				if (file_exists ( KUNENA_ABSTMPLTPATH . '/write.html.php' )) {
					include (KUNENA_ABSTMPLTPATH . '/write.html.php');
				} else {
					include (KUNENA_PATH_TEMPLATE_DEFAULT . DS . 'write.html.php');
				}
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

					//save the options for query after and load the text options, the number options is for create the fields in the form after
                	if ($message->poll_id) {
						$polldatasedit = CKunenaPolls::get_poll_data($id);
						if ($this->kunena_editmode) {
							$polloptionstotal = count($polldatasedit);
						}
                	}

					//get the writing stuff in:
					if (file_exists ( KUNENA_ABSTMPLTPATH . '/write.html.php' )) {
						include (KUNENA_ABSTMPLTPATH . '/write.html.php');
					} else {
						include (KUNENA_PATH_TEMPLATE_DEFAULT . DS . 'write.html.php');
					}
				} else {
					$kunena_app->redirect ( CKunenaLink::GetKunenaURL(true), _POST_NOT_MODERATOR );
				}
			} else if ($do == "editpostnow" && (!$msg_cat->locked || CKunenaTools::isModerator ( $kunena_my->id, $catid ) )) {
				$modified_reason = addslashes ( JRequest::getVar ( "modified_reason", null ) );
				$modified_by = $kunena_my->id;
				$modified_time = CKunenaTimeformat::internalTime();
				$id = ( int ) $id;

				$query = "SELECT a.*, b.*, p.id AS poll_id FROM #__fb_messages AS a
							LEFT JOIN #__fb_messages_text AS b ON a.id=b.mesid
							LEFT JOIN #__fb_polls AS p ON a.id=p.threadid
							WHERE a.id='$id'";

				$kunena_db->setQuery ( $query );

				$message1 = $kunena_db->loadObjectList ();
				check_dberror ( "Unable to load messages." );
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
					if (is_array ( $attachfile ) && $attachfile ['error'] != UPLOAD_ERR_NO_FILE) {
						include KUNENA_PATH_LIB . DS . 'kunena.file.upload.php';
					}

					if (is_array ( $attachimage ) && $attachimage ['error'] != UPLOAD_ERR_NO_FILE) {
						include KUNENA_PATH_LIB . DS . 'kunena.image.upload.php';
					}

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

						if (!$kunena_config->askemail){
                        	   if (empty($email)) {
                                $email = $mes->email;
                              }
                          }

						$kunena_db->setQuery ( "UPDATE #__fb_messages SET name=" . $kunena_db->quote ( $authorname ) . ", email=" . $kunena_db->quote ( addslashes ( $email ) ) . (($kunena_config->editmarkup) ? " ,modified_by='" . $modified_by . "' ,modified_time='" . $modified_time . "' ,modified_reason=" . $kunena_db->quote ( $modified_reason ) : "") . ", subject=" . $kunena_db->quote ( $subject ) . ", topic_emoticon='" . $topic_emoticon . "', hold='" . (( int ) $holdPost) . "' WHERE id={$id}" );

						$dbr_nameset = $kunena_db->query ();
						$kunena_db->setQuery ( "UPDATE #__fb_messages_text SET message=" . $kunena_db->quote ( $message ) . " WHERE mesid='{$id}'" );

						if ($kunena_db->query () && $dbr_nameset) {
							//Update the attachments table if an image has been attached
							if (! empty ( $imageLocation ) && file_exists ( $imageLocation )) {
								$imageLocation = addslashes ( $imageLocation );
								$kunena_db->setQuery ( "INSERT INTO #__fb_attachments (mesid, filelocation) VALUES ('$id'," . $kunena_db->quote ( $imageLocation ) . ")" );

								if (! $kunena_db->query ()) {
									$kunena_app->enqueueMessage ( 'Storing image failed: " . $kunena_db->getErrorMsg () . "', 'error' );
								}
							}

							//Update the attachments table if an file has been attached
							if (! empty ( $fileLocation ) && file_exists ( $fileLocation )) {
								$fileLocation = addslashes ( $fileLocation );
								$kunena_db->setQuery ( "INSERT INTO #__fb_attachments (mesid, filelocation) VALUES ('$id'," . $kunena_db->quote ( $fileLocation ) . ")" );

								if (! $kunena_db->query ()) {
									$kunena_app->enqueueMessage ( 'Storing file failed: " . $kunena_db->getErrorMsg () . "', 'error' );
								}
							}
							$kunena_app->redirect ( CKunenaLink::GetLatestPageAutoRedirectURL ( $kunena_config, $id, $kunena_config->messages_per_page, $catid), _POST_SUCCESS_EDIT );
						} else {
							$kunena_app->enqueueMessage ( _POST_ERROR_MESSAGE_OCCURED, 'error' );
							$kunena_app->redirect ( CKunenaLink::GetKunenaURL(true));
						}
					} else {
						$kunena_app->enqueueMessage ( _POST_INVALID, 'error' );
						$kunena_app->redirect ( CKunenaLink::GetKunenaURL(true));
					}
				} else {
					$kunena_app->redirect ( CKunenaLink::GetKunenaURL(true), _POST_NOT_MODERATOR );
				}
			} else if ($do == "delete") {
				if (! CKunenaTools::isModerator ( $kunena_my->id, $catid )) {
					$kunena_app->redirect ( CKunenaLink::GetKunenaURL(true), _POST_NOT_MODERATOR );
				}

				$id = ( int ) $id;
				$kunena_db->setQuery ( "SELECT * FROM #__fb_messages WHERE id='{$id}'" );
				$message = $kunena_db->loadObjectList ();
				check_dberror ( "Unable to load messages." );

				foreach ( $message as $mes ) {
					?>

		<form
			action="<?php
					echo CKunenaLink::GetPostURL($catid);
					?>"
			method="post" name="myform"><input type="hidden" name="do"
			value="deletepostnow" /> <input type="hidden" name="id"
			value="<?php
					echo $mes->id;
					?>" /> <?php
					echo _POST_ABOUT_TO_DELETE;
					?>:
		<strong><?php
					echo stripslashes ( kunena_htmlspecialchars ( $mes->subject ) );
					?></strong>. <br />

		<br />
		<?php
					echo _POST_ABOUT_DELETE;
					?><br />

		<br />

		<input type="checkbox" checked name="delAttachments" value="delAtt" />
		<?php
					echo _POST_DELETE_ATT;
					?> <br />

		<br />

		<a href="javascript:document.myform.submit();"><?php
					echo _GEN_CONTINUE;
					?></a> | <?php
					echo CKunenaLink::GetThreadLink('view', $catid, $id, _GEN_CANCEL, _GEN_CANCEL, 'nofollow');
					?></form>

		<?php
				}
			} else if ($do == "deletepostnow") {
				if (! CKunenaTools::isModerator ( $kunena_my->id, $catid )) {
					$kunena_app->redirect ( CKunenaLink::GetKunenaURL(true), _POST_NOT_MODERATOR );
				}

				$id = JRequest::getInt ( 'id', 0 );
				$dellattach = JRequest::getVar ( 'delAttachments', '' ) == 'delAtt' ? 1 : 0;
				$thread = fb_delete_post ( $kunena_db, $id, $dellattach );

				CKunenaTools::reCountBoards ();

				switch ($thread) {
					case - 1 :
						echo _POST_ERROR_TOPIC . '<br />';

						echo _KUNENA_POST_DEL_ERR_CHILD;
						break;

					case - 2 :
						echo _POST_ERROR_TOPIC . '<br />';

						echo _KUNENA_POST_DEL_ERR_MSG;
						break;

					case - 3 :
						echo _POST_ERROR_TOPIC . '<br />';

						$tmpstr = _KUNENA_POST_DEL_ERR_TXT;
						$tmpstr = str_replace ( '%id%', $id, $tmpstr );
						echo $tmpstr;
						break;

					case - 4 :
						echo _POST_ERROR_TOPIC . '<br />';

						echo _KUNENA_POST_DEL_ERR_USR;
						break;

					case - 5 :
						echo _POST_ERROR_TOPIC . '<br />';

						echo _KUNENA_POST_DEL_ERR_FILE;
						break;

					default :
						echo '<br /><br /><div align="center">' . _POST_SUCCESS_DELETE . "</div><br />";

						break;
				}
				echo CKunenaLink::GetLatestCategoryAutoRedirectHTML ( $catid );

			} //fi $do==deletepostnow
else if ($do == "move") {
				if (! CKunenaTools::isModerator ( $kunena_my->id, $catid )) {
					$kunena_app->redirect ( CKunenaLink::GetKunenaURL(true), _POST_NOT_MODERATOR );
				}

				$catid = ( int ) $catid;
				$id = ( int ) $id;
				$options = array();
				$selectlist = CKunenaTools::forumSelectList('postmove', 0, $options, ' size="15" class="kmove_selectbox"');

				// get topic subject:
				$kunena_db->setQuery ( "SELECT subject, id FROM #__fb_messages WHERE id='{$id}'" );
				$topicSubject = $kunena_db->loadResult ();
				check_dberror ( "Unable to load messages." );
				?>

		<form
			action="<?php
				echo CKunenaLink::GetPostURL();
				?>"
			method="post" name="myform"><input type="hidden" name="do"
			value="domovepost" /> <input type="hidden" name="id"
			value="<?php
				echo $id;
				?>" />

		<p><?php
				echo _GEN_TOPIC;
				?>: <strong><?php
				echo kunena_htmlspecialchars ( stripslashes ( $topicSubject ) );
				?></strong> <br />

		<br />
		<?php
				echo _POST_MOVE_TOPIC;
				?>: <br />

		<?php echo $selectlist; ?> <br />

		<input type="checkbox" checked name="leaveGhost" value="1" /> <?php
				echo _POST_MOVE_GHOST;
				?>

		<br />

		<input type="submit" class="button"
			value="<?php
				echo _GEN_MOVE;
				?>" /></p>
		</form>

		<?php
			} else if ($do == "domovepost") {
				$catid = ( int ) $catid;
				$id = ( int ) $id;
				$bool_leaveGhost = JRequest::getInt ( 'leaveGhost', 0 );
				//get the some details from the original post for later
				$kunena_db->setQuery ( "SELECT id, subject, catid, time AS timestamp FROM #__fb_messages WHERE id='{$id}'" );
				$oldRecord = $kunena_db->loadObjectList ();
				check_dberror ( "Unable to load messages." );

				if (! CKunenaTools::isModerator ( $kunena_my->id, $oldRecord [0]->catid )) {
					$kunena_app->redirect ( CKunenaLink::GetKunenaURL(true), _POST_NOT_MODERATOR );
				}

				$newSubject = _MOVED_TOPIC . " " . $oldRecord [0]->subject;

				$kunena_db->setQuery ( "SELECT MAX(time) AS timestamp FROM #__fb_messages WHERE thread='{$id}'" );
				$lastTimestamp = $kunena_db->loadResult ();
				check_dberror ( "Unable to load last timestamp." );

				if ($lastTimestamp == "") {
					$lastTimestamp = $oldRecord [0]->timestamp;
				}

				//perform the actual move
				//Move topic post first
				$kunena_db->setQuery ( "UPDATE #__fb_messages SET `catid`='$catid' WHERE `id`='$id'" );
				$kunena_db->query () or check_dberror ( 'Unable to move thread.' );

				$kunena_db->setQuery ( "UPDATE #__fb_messages set `catid`='$catid' WHERE `thread`='$id'" );
				$kunena_db->query () or check_dberror ( 'Unable to move thread.' );

				// insert 'moved topic' notification in old forum if needed
				if ($bool_leaveGhost) {
					$kunena_db->setQuery ( "INSERT INTO #__fb_messages (`parent`, `subject`, `time`, `catid`, `moved`, `userid`, `name`) VALUES ('0'," . $kunena_db->quote ( $newSubject ) . ",'$lastTimestamp','{$oldRecord[0]->catid}','1', '{$kunena_my->id}', " . $kunena_db->quote ( addslashes ( JString::trim ( $my_name ) ) ) . ")" );
					$kunena_db->query () or check_dberror ( 'Unable to insert ghost message.' );

					//determine the new location for link composition
					$newId = $kunena_db->insertid ();

					$newURL = "catid=" . $catid . "&id=" . $id;
					$kunena_db->setQuery ( "INSERT INTO #__fb_messages_text (`mesid`, `message`) VALUES ('$newId', " . $kunena_db->quote ( $newURL ) . ")" );
					$kunena_db->query () or check_dberror ( 'Unable to insert ghost message.' );

					//and update the thread id on the 'moved' post for the right ordering when viewing the forum..
					$kunena_db->setQuery ( "UPDATE #__fb_messages SET `thread`='$newId' WHERE `id`='$newId'" );
					$kunena_db->query () or check_dberror ( 'Unable to move thread.' );
				}
				//move succeeded
				CKunenaTools::reCountBoards ();

				echo '<br /><br /><div align="center">' . _POST_SUCCESS_MOVE . "</div><br />";
				echo CKunenaLink::GetLatestPostAutoRedirectHTML ( $kunena_config, $id, $kunena_config->messages_per_page, $catid );
			} else if ($do == "subscribe") {
				$catid = ( int ) $catid;
				$id = ( int ) $id;
				$success_msg = _POST_NO_SUBSCRIBED_TOPIC;
				$kunena_db->setQuery ( "SELECT thread FROM #__fb_messages WHERE id='{$id}'" );
				if ($id && $kunena_my->id && $kunena_db->query ()) {
					$thread = $kunena_db->loadResult ();
					$kunena_db->setQuery ( "INSERT INTO #__fb_subscriptions (thread,userid) VALUES ('$thread','$kunena_my->id')" );

					if (@$kunena_db->query () && $kunena_db->getAffectedRows () == 1) {
						$success_msg = _POST_SUBSCRIBED_TOPIC;
					}
				}
				$kunena_app->redirect ( CKunenaLink::GetLatestPageAutoRedirectURL ( $kunena_config, $id, $kunena_config->messages_per_page ), $success_msg );
			} else if ($do == "unsubscribe") {
				$catid = ( int ) $catid;
				$id = ( int ) $id;
				$success_msg = _POST_NO_UNSUBSCRIBED_TOPIC;
				$kunena_db->setQuery ( "SELECT MAX(thread) AS thread FROM #__fb_messages WHERE id='{$id}'" );
				if ($id && $kunena_my->id && $kunena_db->query ()) {
					$thread = $kunena_db->loadResult ();
					$kunena_db->setQuery ( "DELETE FROM #__fb_subscriptions WHERE thread=$thread AND userid=$kunena_my->id" );

					if ($kunena_db->query () && $kunena_db->getAffectedRows () == 1) {
						$success_msg = _POST_UNSUBSCRIBED_TOPIC;
					}
				}
				$kunena_app->redirect ( CKunenaLink::GetLatestPageAutoRedirectURL ( $kunena_config, $id, $kunena_config->messages_per_page ), $success_msg );
			} else if ($do == "favorite") {
				$catid = ( int ) $catid;
				$id = ( int ) $id;
				$success_msg = _POST_NO_FAVORITED_TOPIC;
				$kunena_db->setQuery ( "SELECT thread FROM #__fb_messages WHERE id='{$id}'" );
				if ($id && $kunena_my->id && $kunena_db->query ()) {
					$thread = $kunena_db->loadResult ();
					$kunena_db->setQuery ( "INSERT INTO #__fb_favorites (thread,userid) VALUES ('$thread','$kunena_my->id')" );

					if (@$kunena_db->query () && $kunena_db->getAffectedRows () == 1) {
						$success_msg = _POST_FAVORITED_TOPIC;
					}
				}
				$kunena_app->redirect ( CKunenaLink::GetLatestPageAutoRedirectURL ( $kunena_config, $id, $kunena_config->messages_per_page ), $success_msg );
			} else if ($do == "unfavorite") {
				$catid = ( int ) $catid;
				$id = ( int ) $id;
				$success_msg = _POST_NO_UNFAVORITED_TOPIC;
				$kunena_db->setQuery ( "SELECT MAX(thread) AS thread FROM #__fb_messages WHERE id='{$id}'" );
				if ($id && $kunena_my->id && $kunena_db->query ()) {
					$thread = $kunena_db->loadResult ();
					$kunena_db->setQuery ( "DELETE FROM #__fb_favorites WHERE thread=$thread AND userid=$kunena_my->id" );

					if ($kunena_db->query () && $kunena_db->getAffectedRows () == 1) {
						$success_msg = _POST_UNFAVORITED_TOPIC;
					}
				}
				$kunena_app->redirect ( CKunenaLink::GetLatestPageAutoRedirectURL ( $kunena_config, $id, $kunena_config->messages_per_page ), $success_msg );
			} else if ($do == "sticky") {
				if (! CKunenaTools::isModerator ( $kunena_my->id, $catid )) {
					$kunena_app->redirect ( CKunenaLink::GetKunenaURL(true), _POST_NOT_MODERATOR );
				}

				$id = ( int ) $id;
				$success_msg = _POST_STICKY_NOT_SET;
				$kunena_db->setQuery ( "update #__fb_messages set ordering=1 where id=$id" );
				if ($id && $kunena_db->query () && $kunena_db->getAffectedRows () == 1) {
					$success_msg = _POST_STICKY_SET;
				}
				$kunena_app->redirect ( CKunenaLink::GetLatestPageAutoRedirectURL ( $kunena_config, $id, $kunena_config->messages_per_page ), $success_msg );
			} else if ($do == "unsticky") {
				if (! CKunenaTools::isModerator ( $kunena_my->id, $catid )) {
					$kunena_app->redirect ( CKunenaLink::GetKunenaURL(true), _POST_NOT_MODERATOR );
				}

				$id = ( int ) $id;
				$success_msg = _POST_STICKY_NOT_UNSET;
				$kunena_db->setQuery ( "update #__fb_messages set ordering=0 where id=$id" );
				if ($id && $kunena_db->query () && $kunena_db->getAffectedRows () == 1) {
					$success_msg = _POST_STICKY_UNSET;
				}
				$kunena_app->redirect ( CKunenaLink::GetLatestPageAutoRedirectURL ( $kunena_config, $id, $kunena_config->messages_per_page ), $success_msg );
			} else if ($do == "lock") {
				if (! CKunenaTools::isModerator ( $kunena_my->id, $catid )) {
					$kunena_app->redirect ( CKunenaLink::GetKunenaURL(true), _POST_NOT_MODERATOR );
				}

				$id = ( int ) $id;
				$success_msg = _POST_LOCK_NOT_SET;
				$kunena_db->setQuery ( "update #__fb_messages set locked=1 where id=$id" );
				if ($id && $kunena_db->query () && $kunena_db->getAffectedRows () == 1) {
					$success_msg = _POST_LOCK_SET;
				}
				$kunena_app->redirect ( CKunenaLink::GetLatestPageAutoRedirectURL ( $kunena_config, $id, $kunena_config->messages_per_page ), $success_msg );
			} else if ($do == "unlock") {
				if (! CKunenaTools::isModerator ( $kunena_my->id, $catid )) {
					$kunena_app->redirect ( CKunenaLink::GetKunenaURL(true), _POST_NOT_MODERATOR );
				}

				$id = ( int ) $id;
				$success_msg = _POST_LOCK_NOT_UNSET;
				$kunena_db->setQuery ( "update #__fb_messages set locked=0 where id=$id" );
				if ($id && $kunena_db->query () && $kunena_db->getAffectedRows () == 1) {
					$success_msg = _POST_LOCK_UNSET;
				}
				$kunena_app->redirect ( CKunenaLink::GetLatestPageAutoRedirectURL ( $kunena_config, $id, $kunena_config->messages_per_page ), $success_msg );
			} else if ($do == "approve") {
				if (! CKunenaTools::isModerator ( $kunena_my->id, $catid )) {
					$kunena_app->redirect ( CKunenaLink::GetKunenaURL(true), _POST_NOT_MODERATOR );
				}

				$id = ( int ) $id;
				$success_msg = _POST_LOCK_NOT_UNSET;
				$kunena_db->setQuery ( "UPDATE #__fb_messages SET hold=0 WHERE id=$id" );
				if ($id && $kunena_db->query () && $kunena_db->getAffectedRows () == 1) {
					$success_msg = _KUNENA_MODERATE_1APPROVE_SUCCESS;
				}
				$kunena_app->redirect ( CKunenaLink::GetLatestPageAutoRedirectURL ( $kunena_config, $id, $kunena_config->messages_per_page ), $success_msg );
			}
		}
		?>
		</td>
	</tr>
</table>

<?php
/**
 * Function to delete posts
 *
 * @param database object
 * @param int the id if the post to be deleted
 * @param boolean determines if we need to delete attachements as well
 *
 * @return int returns thread id if all went well, -1 to -4 are error numbers
 **/
function fb_delete_post(&$kunena_db, $id, $dellattach) {
	$kunena_db->setQuery ( "SELECT id, catid, parent, thread, subject, userid FROM #__fb_messages WHERE id='{$id}'" );

	if (! $kunena_db->query ()) {
		return - 2;
	}

	$mes = $kunena_db->loadObject ();
	$thread = $mes->thread;

	$userid_array = array ();
	if ($mes->parent == 0) {
		// this is the forum topic; if removed, all children must be removed as well.
		$children = array ();
		$kunena_db->setQuery ( "SELECT userid, id, catid FROM #__fb_messages WHERE thread='{$id}' OR id='{$id}'" );

		foreach ( $kunena_db->loadObjectList () as $line ) {
			$children [] = $line->id;

			if ($line->userid > 0) {
				$userid_array [] = $line->userid;
			}
		}

		$children = implode ( ',', $children );
		$userids = implode ( ',', $userid_array );
	} else {
		//this is not the forum topic, so delete it and promote the direct children one level up in the hierarchy
		$kunena_db->setQuery ( 'UPDATE #__fb_messages SET parent=\'' . $mes->parent . '\' WHERE parent=\'' . $id . '\'' );

		if (! $kunena_db->query ()) {
			return - 1;
		}

		$children = $id;
		$userids = $mes->userid > 0 ? $mes->userid : '';
	}

	//Delete the post (and it's children when it's the first post)
	$kunena_db->setQuery ( 'UPDATE #__fb_messages SET hold=2 WHERE id=' . $id . ' OR thread=' . $id );

	if (! $kunena_db->query ()) {
		return - 2;
	}

	//Delete (possible) ghost post
	$kunena_db->setQuery ( "SELECT mesid FROM #__fb_messages_text WHERE message='catid={$mes->catid}&amp;id={$id}'" );
	$int_ghost_id = $kunena_db->loadResult ();

	if ($int_ghost_id > 0) {
		$kunena_db->setQuery ( 'UPDATE #__fb_messages SET hold=2 WHERE id=' . $int_ghost_id );
		$kunena_db->query ();
	}

	// Already done outside - see dodelete code above
	//    CKunenaTools::reCountBoards();


	return $thread; // all went well :-)
}

function listThreadHistory($id, $kunena_config, $kunena_db) {
	if ($id != 0) {
		//get the parent# for the post on which 'reply' or 'quote' is chosen
		$kunena_db->setQuery ( "SELECT parent FROM #__fb_messages WHERE id='{$id}'" );
		$this_message_parent = $kunena_db->loadResult ();
		//Get the thread# for the same post
		$kunena_db->setQuery ( "SELECT thread FROM #__fb_messages WHERE id='{$id}'" );
		$this_message_thread = $kunena_db->loadResult ();

		//determine the correct thread# for the entire thread
		if ($this_message_parent == 0) {
			$thread = $id;
		} else {
			$thread = $this_message_thread;
		}

		//get all the messages for this thread
		$kunena_db->setQuery ( "SELECT * FROM #__fb_messages AS m LEFT JOIN #__fb_messages_text AS t ON m.id=t.mesid WHERE (thread='{$thread}' OR id='{$thread}') AND hold='0' ORDER BY time DESC LIMIT " . $kunena_config->historylimit );
		$messages = $kunena_db->loadObjectList ();
		check_dberror ( "Unable to load messages." );
		//and the subject of the first thread (for reference)
		$kunena_db->setQuery ( "SELECT subject FROM #__fb_messages WHERE id='{$thread}' and parent='0'" );
		$this_message_subject = $kunena_db->loadResult ();
		check_dberror ( "Unable to load messages." );
		echo "<b>" . _POST_TOPIC_HISTORY . ":</b> " . kunena_htmlspecialchars ( stripslashes ( $this_message_subject ) ) . " <br />" . _POST_TOPIC_HISTORY_MAX . " $kunena_config->historylimit " . _POST_TOPIC_HISTORY_LAST . "<br />";
		?>

<table border="0" cellspacing="1" cellpadding="3" width="100%"
	class="kreview_table">
	<tr>
		<td class="kreview_header" width="20%" align="center"><strong><?php
		echo _GEN_AUTHOR;
		?></strong></td>

		<td class="kreview_header" align="center"><strong><?php
		echo _GEN_MESSAGE;
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
				if (file_exists ( KUNENA_ABSTMPLTPATH . DS . 'forumjump.php' )) {
					include (KUNENA_ABSTMPLTPATH . DS . 'forumjump.php');
				} else {
					include (KUNENA_PATH_TEMPLATE_DEFAULT . DS . 'forumjump.php');
				}
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
