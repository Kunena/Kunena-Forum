<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 **/
// Dont allow direct linking
defined ( '_JEXEC' ) or die ();

class CKunenaPost {
	public $allow = 0;

	function __construct() {
		$this->do = JRequest::getCmd ( 'do', '' );
		$this->action = JRequest::getCmd ( 'action', '' );

		$this->_app = & JFactory::getApplication ();
		$this->_config = & CKunenaConfig::getInstance ();
		$this->_session = & CKunenaSession::getInstance ();
		$this->_db = &JFactory::getDBO ();
		$this->document = JFactory::getDocument();

		$this->my = &JFactory::getUser ();

		$subject = JRequest::getVar ( 'subject', '', 'POST', 'string', JREQUEST_ALLOWRAW );
		$message = JRequest::getVar ( 'message', '', 'POST', 'string', JREQUEST_ALLOWRAW );
		$authorname = JRequest::getVar ( 'authorname', '' );
		$anonymous = JRequest::getInt ( 'anonymous', 0 );
		$email = JRequest::getVar ( 'email', '' );
		$contentURL = JRequest::getVar ( 'contentURL', '' );
		$subscribeMe = JRequest::getVar ( 'subscribeMe', '' );
		$topic_emoticon = JRequest::getInt ( 'topic_emoticon', 0 );
		$polltitle = JRequest::getString ( 'poll_title', 0 );
		$optionsnumbers = JRequest::getInt ( 'number_total_options', '' );
		$polltimetolive = JRequest::getString ( 'poll_time_to_live', 0 );

		$this->id = JRequest::getInt ( 'id', 0 );
		$this->parentid = JRequest::getInt ( 'parentid', 0 );
		if (! $this->id) {
			// Support for old $replyto variable in post reply/quote
			$this->id = JRequest::getInt ( 'replyto', 0 );
		}
		$this->catid = JRequest::getInt ( 'catid', 0 );

		$this->msg_cat = null;
		if ($this->id || $this->parentid) {
			// Check that message and category exists and fill some information for later use
			$query = "SELECT m.*, (mm.locked OR c.locked) AS locked, c.locked AS catlocked, t.message,
					c.name AS catname, c.parent AS catparent, c.pub_access,
					c.review, c.class_sfx, p.id AS poll_id, c.allow_anonymous,
					c.post_anonymous, c.allow_polls
				FROM #__fb_messages AS m
				INNER JOIN #__fb_messages AS mm ON mm.id=m.thread
				INNER JOIN #__fb_messages_text AS t ON t.mesid=m.id
				INNER JOIN #__fb_categories AS c ON c.id=m.catid
				LEFT JOIN #__fb_polls AS p ON m.id=p.threadid
				WHERE m.id='" . ($this->parentid ? $this->parentid : $this->id) . "'";

			$this->_db->setQuery ( $query );
			$this->msg_cat = $this->_db->loadObject ();
			check_dberror ( 'Unable to check message.' );

			if (! $this->msg_cat) {
				echo JText::_ ( 'COM_KUNENA_POST_INVALID' );
				return;
			}

			// Make sure that category id is from the message (post may have been moved)
			if ($this->do != 'domovepostnow' && $this->do != 'domergepostnow' && $this->do != 'dosplit') {
				$this->catid = $this->msg_cat->catid;
			}
		} else if ($this->catid) {
			// Check that category exists and fill some information for later use
			$this->_db->setQuery ( "SELECT 0 AS id, 0 AS thread, id AS catid, name AS catname, parent AS catparent, pub_access, locked, locked AS catlocked, review, class_sfx, allow_anonymous, post_anonymous, allow_polls FROM #__fb_categories WHERE id='{$this->catid}'" );
			$this->msg_cat = $this->_db->loadObject ();
			check_dberror ( 'Unable to load category.' );
			if (! $this->msg_cat) {
				echo JText::_ ( 'COM_KUNENA_NO_ACCESS' );
				return;
			}
		}

		// Check user access rights
		if (($this->my->id == 0 && ! $this->_config->pubwrite) || (empty ( $this->msg_cat->catparent ) && $this->do != 'reply') && (! $this->_session->canRead ( $this->catid ) && ! CKunenaTools::isAdmin ())) {
			CKunenaTools::loadTemplate ( '/plugin/login/login.php' );
			return;
		}

		//reset variables used
		$this->kunena_editmode = 0;

		//ip for floodprotection, post logging, subscriptions, etcetera
		$this->ip = $_SERVER ["REMOTE_ADDR"];

		//Let's find out who we're dealing with if a registered user wants to make a post
		if ($this->my->id) {
			$this->email = $this->my->email;
			if (CKunenaTools::isModerator ( $this->my->id, $this->catid )) {
				if (! empty ( $email ))
					$this->email = $email;
			}
		} else {
			$this->email = (isset ( $email ) && ! empty ( $email )) ? $email : '';
		}

		$this->allow = 1;
	}

	protected function post() {
		$this->verifyCaptcha();

		if ($this->tokenProtection ())
			return false;
		if ($this->lockProtection ())
			return false;
		if ($this->floodProtection ())
			return false;

		$subject = JRequest::getVar ( 'subject', '', 'POST', 'string', JREQUEST_ALLOWRAW );
		$message = JRequest::getVar ( 'message', '', 'POST', 'string', JREQUEST_ALLOWRAW );
		$anonymous = JRequest::getInt ( 'anonymous', 0 );
		$email = JRequest::getVar ( 'email', '' );
		$contentURL = JRequest::getVar ( 'contentURL', '' );
		$subscribeMe = JRequest::getVar ( 'subscribeMe', '' );
		$topic_emoticon = JRequest::getInt ( 'topic_emoticon', 0 );
		$polltitle = JRequest::getString ( 'poll_title', 0 );
		$optionsnumbers = JRequest::getInt ( 'number_total_options', '' );
		$polltimetolive = JRequest::getString ( 'poll_time_to_live', 0 );

		$parent = ( int ) $this->parentid;
		$my_name = $this->getAuthorName ( '', $anonymous );
		jimport ( 'joomla.mail.helper' );
		if ($this->catid == 0 || empty ( $this->msg_cat )) {
			echo JText::_ ( 'COM_KUNENA_POST_ERROR_NO_CATEGORY' );
		} else if ($this->msg_cat->catparent == 0) {
			echo JText::_ ( 'COM_KUNENA_POST_ERROR_IS_SECTION' );
		} else if ($anonymous && ! $this->msg_cat->allow_anonymous) {
			echo JText::_ ( 'COM_KUNENA_POST_ERROR_ANONYMOUS_FORBITTEN' );
		} else if (empty ( $my_name )) {
			echo JText::_ ( 'COM_KUNENA_POST_FORGOT_NAME' );
		} else if (! $this->my->id && $this->_config->askemail && empty ( $this->email )) {
			echo JText::_ ( 'COM_KUNENA_POST_FORGOT_EMAIL' );
		} else if ($this->_config->askemail && ! JMailHelper::isEmailAddress ( $this->email )) {
			echo JText::_ ( 'COM_KUNENA_MY_EMAIL_INVALID' );
		} else if (empty ( $subject )) {
			echo JText::_ ( 'COM_KUNENA_POST_FORGOT_SUBJECT' );
		} else if (empty ( $message )) {
			echo JText::_ ( 'COM_KUNENA_POST_FORGOT_MESSAGE' );
		} else {
			if ($parent == 0) {
				$thread = 0;
			}

			if ($this->msg_cat->id == 0) {
				// bad parent, create a new post
				$parent = 0;
				$thread = 0;
			} else {

				$thread = $this->msg_cat->parent == 0 ? $this->msg_cat->id : $this->msg_cat->thread;
			}

			$messagesubject = $subject; //before we add slashes and all... used later in mail


			$userid = $this->my->id;
			if ($anonymous) {
				// Anonymous post: remove all user information from the post
				$userid = 0;
				$this->email = '';
				$this->ip = '';
			}

			$authorname = addslashes ( JString::trim ( $my_name ) );
			$subject = addslashes ( JString::trim ( $subject ) );
			$message = addslashes ( JString::trim ( $message ) );
			$email = addslashes ( JString::trim ( $this->email ) );

			global $topic_emoticons;
			$topic_emoticon = (! isset ( $topic_emoticons [$topic_emoticon] )) ? 0 : $topic_emoticon;
			$posttime = CKunenaTimeformat::internalTime ();
			if ($contentURL) {
				$message = $contentURL . "\n\n" . $message;
			}

			//check if the post must be reviewed by a moderator prior to showing
			$holdPost = 0;
			if (! CKunenaTools::isModerator ( $this->my->id, $this->catid )) {
				$holdPost = $this->msg_cat->review;
			}

			// DO NOT PROCEED if there is an exact copy of the message already in the db
			$duplicatetimewindow = $posttime - $this->_config->fbsessiontimeout;
			$this->_db->setQuery ( "SELECT m.id FROM #__fb_messages AS m JOIN #__fb_messages_text AS t ON m.id=t.mesid WHERE m.userid='{$userid}' AND m.name='{$authorname}' AND m.email='{$email}' AND m.subject='{$subject}' AND m.ip='{$this->ip}' AND t.message='{$message}' AND m.time>='{$duplicatetimewindow}'" );
			$pid = ( int ) $this->_db->loadResult ();
			check_dberror ( 'Unable to load post.' );

			if ($pid) {
				// We get here in case we have detected a double post
				// We did not do any further processing and just display the failure message
				echo '<br /><br /><div align="center">' . JText::_ ( 'COM_KUNENA_POST_DUPLICATE_IGNORED' ) . '</div><br /><br />';
				echo CKunenaLink::GetLatestPostAutoRedirectHTML ( $this->_config, $pid, $this->_config->messages_per_page, $this->catid );
			} else {
				$this->_db->setQuery ( "INSERT INTO #__fb_messages
						(parent,thread,catid,name,userid,email,subject,time,ip,topic_emoticon,hold)
						VALUES('$parent','$thread','$this->catid'," . $this->_db->quote ( $authorname ) . ",'{$userid}'," . $this->_db->quote ( $email ) . "," . $this->_db->quote ( $subject ) . ",'$posttime','{$this->ip}','$topic_emoticon','$holdPost')" );

				if (! $this->_db->query ()) {
					echo JText::_ ( 'COM_KUNENA_POST_ERROR_MESSAGE' );
				} else {
					$pid = $this->_db->insertId ();
					//Insert in the database the informations for the poll and the options for the poll
					$poll_exist = null;
					if (! empty ( $optionsnumbers ) && ! empty ( $polltitle )) {
						$poll_exist = "1";
						//Begin Poll management options
						$optionvalue = array ();
						for($ioptions = 0; $ioptions < $optionsnumbers; $ioptions ++) {
							$optionvalue [] = JRequest::getString ( 'field_option' . $ioptions, null );
						}
					}

					if (! empty ( $polltitle ) && ! empty ( $optionsnumbers )) {
						CKunenaPolls::save_new_poll ( $polltimetolive, $polltitle, $pid, $optionvalue );
					}

					// now increase the #s in categories only case approved
					if ($holdPost == 0) {
						CKunenaTools::modifyCategoryStats ( $pid, $parent, $posttime, $this->catid );
					}

					$this->_db->setQuery ( "INSERT INTO #__fb_messages_text (mesid,message) VALUES('$pid'," . $this->_db->quote ( $message ) . ")" );
					$this->_db->query ();

					// A couple more tasks required...
					if ($thread == 0) {
						//if thread was zero, we now know to which id it belongs, so we can determine the thread and update it
						$this->_db->setQuery ( "UPDATE #__fb_messages SET thread='$pid' WHERE id='$pid'" );
						$this->_db->query ();

						// if JomScoial integration is active integrate user points and activity stream
						if ($this->_config->pm_component == 'jomsocial' || $this->_config->fb_profile == 'jomsocial' || $this->_config->avatar_src == 'jomsocial') {
							include_once (KUNENA_ROOT_PATH . DS . 'components/com_community/libraries/userpoints.php');

							CuserPoints::assignPoint ( 'com_kunena.thread.new' );

							// Check for permisions of the current category - activity only if public or registered
							if ($this->msg_cat->pub_access == 0 || $this->msg_cat->pub_access == - 1) {
								if ($this->_config->js_actstr_integration) {
									//activity stream  - new post
									$JSPostLink = CKunenaLink::GetThreadPageURL ( $this->_config, 'view', $this->catid, $pid, 1 );

									$kunena_emoticons = smile::getEmoticons ( 1 );
									$content = stripslashes ( $message );
									$content = smile::smileReplace ( $content, 0, $this->_config->disemoticons, $kunena_emoticons );
									$content = nl2br ( $content );

									$act = new stdClass ( );
									$act->cmd = 'wall.write';
									$act->actor = $userid;
									$act->target = 0; // no target
									$act->title = JText::_ ( '{actor} ' . JText::_ ( 'COM_KUNENA_JS_ACTIVITYSTREAM_CREATE_MSG1' ) . ' <a href="' . $JSPostLink . '">' . stripslashes ( $subject ) . '</a> ' . JText::_ ( 'COM_KUNENA_JS_ACTIVITYSTREAM_CREATE_MSG2' ) );
									$act->content = $content;
									$act->app = 'wall';
									$act->cid = 0;

									// jomsocial 0 = public, 20 = registered members
									if ($this->msg_cat->pub_access == 0) {
										$act->access = 0;
									} else {
										$act->access = 20;
									}

									CFactory::load ( 'libraries', 'activities' );
									CActivityStream::add ( $act );
								}
							}
						}

					} else {
						// if JomScoial integration is active integrate user points and activity stream
						if ($this->_config->pm_component == 'jomsocial' || $this->_config->fb_profile == 'jomsocial' || $this->_config->avatar_src == 'jomsocial') {
							include_once (KUNENA_ROOT_PATH . DS . 'components/com_community/libraries/userpoints.php');

							CuserPoints::assignPoint ( 'com_kunena.thread.reply' );

							// Check for permisions of the current category - activity only if public or registered
							if ($this->msg_cat->pub_access == 0 || $this->msg_cat->pub_access == - 1 && $this->_config->js_actstr_integration) {
								if ($this->_config->js_actstr_integration) {
									//activity stream - reply post
									$JSPostLink = CKunenaLink::GetThreadPageURL ( $this->_config, 'view', $this->catid, $thread, 1 );

									$content = stripslashes ( $message );
									$content = smile::smileReplace ( $content, 0, $this->_config->disemoticons, $kunena_emoticons );
									$content = nl2br ( $content );

									$act = new stdClass ( );
									$act->cmd = 'wall.write';
									$act->actor = $userid;
									$act->target = 0; // no target
									$act->title = JText::_ ( '{single}{actor}{/single}{multiple}{actors}{/multiple} ' . JText::_ ( 'COM_KUNENA_JS_ACTIVITYSTREAM_REPLY_MSG1' ) . ' <a href="' . $JSPostLink . '">' . stripslashes ( $subject ) . '</a> ' . JText::_ ( 'COM_KUNENA_JS_ACTIVITYSTREAM_REPLY_MSG2' ) );
									$act->content = $content;
									$act->app = 'wall';
									$act->cid = 0;

									// jomsocial 0 = public, 20 = registered members
									if ($this->msg_cat->pub_access == 0) {
										$act->access = 0;
									} else {
										$act->access = 20;
									}

									CFactory::load ( 'libraries', 'activities' );
									CActivityStream::add ( $act );
								}
							}
						}
					}
					// End Modify for activities stream


					CKunenaTools::markTopicRead ( $pid, $this->my->id );

					//update the user posts count
					if ($userid) {
						$this->_db->setQuery ( "UPDATE #__fb_users SET posts=posts+1 WHERE userid={$userid}" );
						$this->_db->query ();
					}

					//Update the attachments table if an image has been attached
					require_once (KUNENA_PATH_LIB . DS . 'kunena.attachments.class.php');
					$attachments = CKunenaAttachments::getInstance ();
					$attachments->assign ( $pid );
					$fileinfos = $attachments->multiupload ( $pid );
					foreach ( $fileinfos as $fileinfo ) {
						if (! $fileinfo ['status'])
							$this->_app->enqueueMessage ( JText::sprintf ( 'COM_KUNENA_UPLOAD_FAILED', $fileinfo [name] ) . ': ' . $fileinfo ['error'], 'error' );
					}

					// Perform proper page pagination for better SEO support
					// used in subscriptions and auto redirect back to latest post
					if ($thread == 0) {
						$querythread = $pid;
					} else {
						$querythread = $thread;
					}

					$this->_db->setQuery ( "SELECT * FROM #__fb_sessions WHERE readtopics LIKE '%$thread%' AND userid!={$this->my->id}" );
					$sessions = $this->_db->loadObjectList ();
					check_dberror ( "Unable to load sessions." );
					foreach ( $sessions as $session ) {
						$readtopics = $session->readtopics;
						$userid = $session->userid;
						$rt = explode ( ",", $readtopics );
						$key = array_search ( $thread, $rt );
						if ($key !== FALSE) {
							unset ( $rt [$key] );
							$readtopics = implode ( ",", $rt );
							$this->_db->setQuery ( "UPDATE #__fb_sessions SET readtopics='$readtopics' WHERE userid=$userid" );
							$this->_db->query ();
							check_dberror ( "Unable to update sessions." );
						}
					}

					$this->_db->setQuery ( "SELECT COUNT(*) AS totalmessages FROM #__fb_messages WHERE thread='{$querythread}'" );
					$result = $this->_db->loadObject ();
					check_dberror ( "Unable to load messages." );
					$threadPages = ceil ( $result->totalmessages / $this->_config->messages_per_page );
					//construct a useable URL (for plaintext - so no &amp; encoding!)
					jimport ( 'joomla.environment.uri' );
					$uri = & JURI::getInstance ( JURI::base () );
					$LastPostUrl = $uri->toString ( array ('scheme', 'host', 'port' ) ) . str_replace ( '&amp;', '&', CKunenaLink::GetThreadPageURL ( $this->_config, 'view', $this->catid, $querythread, $threadPages, $this->_config->messages_per_page, $pid ) );

					// start integration alphauserpoints component
					if ($this->_config->alphauserpointsrules) {
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
								if ($this->_config->alphauserpointsnumchars > 0) {
									// use if limit chars for a response
									if (JString::strlen ( $message ) > $this->_config->alphauserpointsnumchars) {
										AlphaUserPointsHelper::newpoints ( 'plgaup_reply_kunena', '', $pid, $datareference );
									} else {
										$this->_app->enqueueMessage ( JText::_ ( 'COM_KUNENA_AUP_MESSAGE_TOO_SHORT' ) );
									}
								} else {
									AlphaUserPointsHelper::newpoints ( 'plgaup_reply_kunena', '', $pid, $datareference );
								}
							}
						}
					}
					// end insertion AlphaUserPoints


					//get all subscriptions and moderators
					$emailToList = CKunenaTools::getEMailToList ( $this->catid, $querythread, $this->_config->allowsubscriptions && ! $holdPost, $this->_config->mailmod, $this->_config->mailadmin, $this->my->id );

					if (count ( $emailToList )) {
						if (! $this->_config->email || ! JMailHelper::isEmailAddress ( $this->_config->email )) {
							$this->_app->enqueueMessage ( JText::_ ( 'COM_KUNENA_EMAIL_INVALID' ), 'error' );
						} else {
							// clean up the message for review
							$mailmessage = smile::purify ( stripslashes ( $message ) );

							$mailsender = JMailHelper::cleanAddress ( stripslashes ( $this->_config->board_title ) . " " . JText::_ ( 'COM_KUNENA_GEN_FORUM' ) );
							$mailsubject = JMailHelper::cleanSubject ( "[" . stripslashes ( $this->_config->board_title ) . " " . JText::_ ( 'COM_KUNENA_GEN_FORUM' ) . "] " . stripslashes ( $messagesubject ) . " (" . stripslashes ( $this->msg_cat->catname ) . ")" );

							foreach ( $emailToList as $emailTo ) {
								if (! $emailTo->email || ! JMailHelper::isEmailAddress ( $emailTo->email ))
									continue;

								if ($emailTo->subscription) {
									$msg1 = JText::_ ( 'COM_KUNENA_POST_EMAIL_NOTIFICATION1' );
									$msg2 = JText::_ ( 'COM_KUNENA_POST_EMAIL_NOTIFICATION2' );
								} else {
									$msg1 = JText::_ ( 'COM_KUNENA_POST_EMAIL_MOD1' );
									$msg2 = JText::_ ( 'COM_KUNENA_POST_EMAIL_MOD2' );
								}

								$msg = "$emailTo->name,\n\n";
								$msg .= $msg1 . " " . stripslashes ( $this->_config->board_title ) . " " . JText::_ ( 'COM_KUNENA_GEN_FORUM' ) . "\n\n";
								$msg .= JText::_ ( 'COM_KUNENA_GEN_SUBJECT' ) . ": " . stripslashes ( $messagesubject ) . "\n";
								$msg .= JText::_ ( 'COM_KUNENA_GEN_FORUM' ) . ": " . stripslashes ( $this->msg_cat->catname ) . "\n";
								$msg .= JText::_ ( 'COM_KUNENA_VIEW_POSTED' ) . ": " . stripslashes ( $authorname ) . "\n\n";
								$msg .= $msg2 . "\n";
								$msg .= "URL: $LastPostUrl\n\n";
								if ($this->_config->mailfull == 1) {
									$msg .= JText::_ ( 'COM_KUNENA_GEN_MESSAGE' ) . ":\n-----\n";
									$msg .= $mailmessage;
									$msg .= "\n-----";
								}
								$msg .= "\n\n";
								$msg .= JText::_ ( 'COM_KUNENA_POST_EMAIL_NOTIFICATION3' ) . "\n";
								$msg .= "\n\n\n\n";
								$msg .= "** Powered by Kunena! - http://www.Kunena.com **";
								$msg = JMailHelper::cleanBody ( $msg );

								if ($this->ip != "127.0.0.1") {
									JUtility::sendMail ( $this->_config->email, $mailsender, $emailTo->email, $mailsubject, $msg );
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

						$this->_db->setQuery ( "INSERT INTO #__fb_subscriptions (thread,userid) VALUES ('$fb_thread','{$this->my->id}')" );

						if (@$this->_db->query ()) {
							$redirectmsg .= JText::_ ( 'COM_KUNENA_POST_SUBSCRIBED_TOPIC' ) . '<br />';
						} else {
							$redirectmsg .= JText::_ ( 'COM_KUNENA_POST_NO_SUBSCRIBED_TOPIC' ) . '<br />';
						}
					}

					if ($holdPost == 1) {
						$redirectmsg .= JText::_ ( 'COM_KUNENA_POST_SUCCES_REVIEW' );
					} else {
						$redirectmsg .= JText::_ ( 'COM_KUNENA_POST_SUCCESS_POSTED' );
					}
					$this->_app->redirect ( CKunenaLink::GetLatestPageAutoRedirectURL ( $this->_config, $pid, $this->_config->messages_per_page, $this->catid ), $redirectmsg );
				}
			}
		}
	}

	protected function reply($do) {
		if ($this->lockProtection ())
			return false;
		if ($this->floodProtection ())
			return false;

		$parentid = 0;
		if ($this->catid && $this->msg_cat->id > 0) {
			$message = $this->msg_cat;
			$parentid = $message->id;
			if ($do == 'quote') {
				$this->message_text = "[b]" . kunena_htmlspecialchars ( stripslashes ( $message->name ) ) . " " . JText::_ ( 'COM_KUNENA_POST_WROTE' ) . ":[/b]\n";
				$this->message_text .= '[quote]' . kunena_htmlspecialchars ( stripslashes ( $message->message ) ) . "[/quote]";
			} else {
				$this->message_text = '';
			}
			$reprefix = JString::substr ( stripslashes ( $message->subject ), 0, JString::strlen ( JText::_ ( 'COM_KUNENA_POST_RE' ) ) ) != JText::_ ( 'COM_KUNENA_POST_RE' ) ? JText::_ ( 'COM_KUNENA_POST_RE' ) . ' ' : '';
			$this->subject = kunena_htmlspecialchars ( stripslashes ( $message->subject ) );
			$this->resubject = $reprefix . $this->subject;
		} else {
			$this->message_text = '';
			$this->resubject = '';

			$options = array ();
			if (empty ( $this->msg_cat->allow_anonymous ))
				$this->selectcatlist = CKunenaTools::forumSelectList ( 'postcatid', $this->catid, $options, '' );
		}
		$this->authorName = kunena_htmlspecialchars ( $this->getAuthorName () );
		$this->id = $this->id;
		$this->parentid = $parentid;
		$this->catid = $this->catid;
		$this->emoid = 0;
		$this->action = 'post';

		$this->allow_anonymous = ! empty ( $this->msg_cat->allow_anonymous ) && $this->my->id;
		$this->anonymous = ($this->allow_anonymous && ! empty ( $this->msg_cat->post_anonymous ));
		$this->allow_name_change = 0;
		if (! $this->my->id || $this->_config->changename || ! empty ( $this->msg_cat->allow_anonymous ) || CKunenaTools::isModerator ( $this->my->id, $this->catid )) {
			$this->allow_name_change = 1;
		}

		// check if this user is already subscribed to this topic but only if subscriptions are allowed
		$this->cansubscribe = 0;
		if ($this->my->id && $this->_config->allowsubscriptions == 1) {
			$this->cansubscribe = 1;
			if ($this->msg_cat && $this->msg_cat->thread) {
				$this->_db->setQuery ( "SELECT thread FROM #__fb_subscriptions WHERE userid='{$this->my->id}' AND thread='{$this->msg_cat->thread}'" );
				$subscribed = $this->_db->loadResult ();
				check_dberror ( "Unable to load subscriptions." );

				if ($subscribed) {
					$this->cansubscribe = 0;
				}
			}
		}


		if ($this->parentid) $this->title = JText::_('COM_KUNENA_POST_REPLY_TOPIC') . ' ' . $this->subject;
		else $this->title = JText::_('COM_KUNENA_POST_NEW_TOPIC');

		CKunenaTools::loadTemplate ( '/editor/form.php' );
	}

	protected function edit() {
		if ($this->lockProtection ())
			return false;

		$message = $this->msg_cat;

		$allowEdit = 0;
		if (CKunenaTools::isModerator ( $this->my->id, $this->catid )) {
			// Moderator can edit any message
			$allowEdit = 1;
		} else if ($this->my->id && $this->my->id == $message->userid) {
			$allowEdit = CKunenaTools::editTimeCheck ( $message->modified_time, $message->time );
		}

		if ($allowEdit == 1) {
			// Load attachments
			$attachments = array ();
			$query = "SELECT * FROM #__kunena_attachments
					WHERE mesid ='" . $message->id . "'";
			$this->_db->setQuery ( $query );
			$attachments = $this->_db->loadObjectList ();
			check_dberror ( 'Unable to load attachments' );

			$this->msg_html->attachments = array ();

			foreach ( $attachments as $attachment ) {
				// Check if file has been pre-processed
				if (is_null ( $attachment->hash )) {
					// This attachment has not been processed.
				// It migth be a legacy file, or the settings might have been reset.
				// Force recalculation ...


				// TODO: Perform image re-prosessing
				}

				// shorttype based on MIME type to determine if image for displaying purposes
				$attachment->shorttype = (stripos ( $attachment->filetype, 'image/' ) !== false) ? 'image' : $attachment->filetype;

				$this->msg_html->attachments [] = $attachment;
			}
			// End of load attachments


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
				$this->polldatasedit = CKunenaPolls::get_poll_data ( $this->id );
				if ($this->kunena_editmode) {
					$this->polloptionstotal = count ( $this->polldatasedit );
				}
			}

			$this->allow_anonymous = ! empty ( $this->msg_cat->allow_anonymous ) && $message->userid;
			$this->anonymous = 0;
			$this->allow_name_change = 0;
			if (! $this->my->id || $this->_config->changename || ! empty ( $this->msg_cat->allow_anonymous ) || CKunenaTools::isModerator ( $this->my->id, $this->catid )) {
				$this->allow_name_change = 1;
			}

			$this->title = JText::_('COM_KUNENA_POST_EDIT') . ' ' . $this->resubject;

			CKunenaTools::loadTemplate ( '/editor/form.php' );
		} else {
			$this->_app->redirect ( CKunenaLink::GetKunenaURL ( true ), JText::_ ( 'COM_KUNENA_POST_NOT_MODERATOR' ) );
		}
	}

	protected function editpostnow() {
		if ($this->tokenProtection ())
			return false;
		if ($this->lockProtection ())
			return false;

		$subject = JRequest::getVar ( 'subject', '', 'POST', 'string', JREQUEST_ALLOWRAW );
		$message = JRequest::getVar ( 'message', '', 'POST', 'string', JREQUEST_ALLOWRAW );
		$topic_emoticon = JRequest::getInt ( 'topic_emoticon', 0 );
		$anonymous = JRequest::getInt ( 'anonymous', 0 );

		$query = "SELECT a.*, b.*, p.id AS poll_id FROM #__fb_messages AS a
							LEFT JOIN #__fb_messages_text AS b ON a.id=b.mesid
							LEFT JOIN #__fb_polls AS p ON a.id=p.threadid
							WHERE a.id='$this->id'";
		$this->_db->setQuery ( $query );
		$mes = $this->_db->loadObject ();
		check_dberror ( "Unable to load message." );

		if (! $mes) {
			$this->_app->enqueueMessage ( JText::_ ( 'COM_KUNENA_POST_INVALID' ), 'error' );
			$this->_app->redirect ( CKunenaLink::GetKunenaURL ( true ) );
		}

		$userid = $mes->userid;

		$polltitle = JRequest::getString ( 'poll_title', 0 );
		$optionsnumbers = JRequest::getInt ( 'number_total_options', '' );
		$polltimetolive = JRequest::getString ( 'poll_time_to_live', 0 );

		$modified_reason = addslashes ( JRequest::getVar ( "modified_reason", null ) );
		$modified_by = $this->my->id;
		$modified_time = CKunenaTimeformat::internalTime ();

		//Check for a moderator or superadmin
		if (CKunenaTools::isModerator ( $this->my->id, $this->catid )) {
			$allowEdit = 1;
		} else if ($this->my->id && $this->my->id == $mes->userid) {
			// FIXME: consider taking account ( int ) $this->_config->useredittimegrace) or save last edit action to session
			$allowEdit = CKunenaTools::editTimeCheck ( $mes->modified_time, $mes->time );
		}

		if ($allowEdit == 1) {
			$authorname = $this->getAuthorName ( $mes->name, $anonymous );
			$subject = addslashes ( JString::trim ( $subject ) );
			$message = addslashes ( JString::trim ( $message ) );

			// check if the post must be reviewed by a Moderator prior to showing
			// doesn't apply to admin/moderator posts ;-)
			$holdPost = 0;
			if (! CKunenaTools::isModerator ( $this->my->id, $this->catid )) {
				$holdPost = $msg_cat->review;
			}

			//update the poll when an user edit his post
			if ($this->_config->pollenabled) {
				$optvalue = array ();
				for($i = 0; $i < $optionsnumbers; $i ++) {
					$optvalue [] = JRequest::getString ( 'field_option' . $i, null );
				}
				//need to check if the poll exist, if it's not the case the poll is insered like new poll
				if (! $mes->poll_id) {
					CKunenaPolls::save_new_poll ( $polltimetolive, $polltitle, $this->id, $optvalue );
				} else {
					if (empty ( $polltitle ) && empty ( $optionsnumbers )) {
						//The poll is deleted because the polltitle and the options are empty
						CKunenaPolls::delete_poll ( $this->id );
					} else {
						CKunenaPolls::update_poll_edit ( $polltimetolive, $this->id, $polltitle, $optvalue, $optionsnumbers );
					}
				}
			}

			// Get email address
			jimport ( 'joomla.mail.helper' );
			if (! $this->my->id || CKunenaTools::isModerator ( $this->my->id, $this->catid )) {
				$email = JRequest::getVar ( 'email', $mes->email );
			}
			if (empty ( $email ) || ! JMailHelper::isEmailAddress ( $email )) {
				$email = $mes->email;
			}

			// Default modified by information
			if (! $this->_config->editmarkup)
				$modified = "modified_by='0', modified_time='0' ,modified_reason='', ";
			else
				$modified = "modified_by='" . $modified_by . "', modified_time='" . $modified_time . "' ,modified_reason=" . $this->_db->quote ( $modified_reason ) . ", ";

			if ($anonymous) {
				if ($this->my->id == $mes->userid && $mes->modified_by == $mes->userid) {
					// I am the author and previous modification was made by me => delete modification information to hide my personality
					$modified = "modified_by='0', modified_time='0' ,modified_reason='', ";
				} else if ($this->my->id == $mes->userid) {
					// I am the author, but somebody else has modified the message => leave modification information as it is pointing to moderator
					$modified = "";
				}
				// Remove userid, email and ip address. Change author name if needed
				$query = "UPDATE #__fb_messages SET userid='0', name=" . $this->_db->quote ( $authorname ) . ", email='', ip='', " . $modified . " subject=" . $this->_db->quote ( $subject ) . ", topic_emoticon='" . $topic_emoticon . "', hold='" . (( int ) $holdPost) . "' WHERE id={$this->id}";
			} else {
				$query = "UPDATE #__fb_messages SET name=" . $this->_db->quote ( $authorname ) . ", email=" . $this->_db->quote ( addslashes ( $email ) ) . (($this->_config->editmarkup) ? " ,modified_by='" . $modified_by . "' ,modified_time='" . $modified_time . "' ,modified_reason=" . $this->_db->quote ( $modified_reason ) : "") . ", subject=" . $this->_db->quote ( $subject ) . ", topic_emoticon='" . $topic_emoticon . "', hold='" . (( int ) $holdPost) . "' WHERE id={$this->id}";
			}
			$this->_db->setQuery ( $query );
			$dbr_nameset = $this->_db->query ();
			$this->_db->setQuery ( "UPDATE #__fb_messages_text SET message=" . $this->_db->quote ( $message ) . " WHERE mesid='{$this->id}'" );

			if ($this->_db->query () && $dbr_nameset) {
				//Update the attachments table if an file has been attached
				require_once (KUNENA_PATH_LIB . DS . 'kunena.attachments.class.php');
				$attachments = CKunenaAttachments::getInstance ();
				$attachments->assign ( $this->id );
				$fileinfos = $attachments->multiupload ( $mes->id );
				foreach ( $fileinfos as $fileinfo ) {
					if (! $fileinfo ['status'])
						$this->_app->enqueueMessage ( JText::sprintf ( 'COM_KUNENA_UPLOAD_FAILED', $fileinfo [name] ) . ': ' . $fileinfo ['error'], 'error' );
				}

				$this->_app->redirect ( CKunenaLink::GetLatestPageAutoRedirectURL ( $this->_config, $this->id, $this->_config->messages_per_page, $this->catid ), JText::_ ( 'COM_KUNENA_POST_SUCCESS_EDIT' ) );
			} else {
				$this->_app->enqueueMessage ( JText::_ ( 'COM_KUNENA_POST_ERROR_MESSAGE_OCCURED' ), 'error' );
				$this->_app->redirect ( CKunenaLink::GetKunenaURL ( true ) );
			}
		} else {
			$this->_app->redirect ( CKunenaLink::GetKunenaURL ( true ), JText::_ ( 'COM_KUNENA_POST_NOT_MODERATOR' ) );
		}
	}

	protected function deleteownpost() {
		$delete = $delete = CKunenaTools::userOwnDelete ( $this->id );
		if (! $delete) {
			$message = JText::_ ( 'COM_KUNENA_POST_OWN_DELETE_ERROR' );
		} else {
			$message = JText::_ ( 'COM_KUNENA_POST_SUCCESS_DELETE' );
		}

		$this->_app->redirect ( CKunenaLink::GetCategoryURL ( 'showcat', $this->catid, true ), $message );
	}

	protected function delete() {
		if ($this->moderatorProtection ())
			return false;

		require_once (KUNENA_PATH_LIB . '/kunena.moderation.class.php');
		$kunena_mod = CKunenaModeration::getInstance ();

		$delete = $kunena_mod->deleteMessage ( $this->id );
		if (! $delete) {
			$message = $kunena_mod->getErrorMessage ();
		} else {
			$message = JText::_ ( 'COM_KUNENA_POST_SUCCESS_DELETE' );
		}

		$this->_app->redirect ( CKunenaLink::GetCategoryURL ( 'showcat', $this->catid, true ), $message );

	}

	protected function deletethread() {
		if ($this->moderatorProtection ())
			return false;

		require_once (KUNENA_PATH_LIB . '/kunena.moderation.class.php');
		$kunena_mod = CKunenaModeration::getInstance ();

		$delete = $kunena_mod->deleteThread ( $this->id );
		if (! $delete) {
			$message = $kunena_mod->getErrorMessage ();
		} else {
			$message = JText::_ ( 'COM_KUNENA_POST_SUCCESS_DELETE' );
		}

		$this->_app->redirect ( CKunenaLink::GetCategoryURL ( 'showcat', $this->catid, true ), $message );
	}

	protected function move() {
		if ($this->moderatorProtection ())
			return false;

		$options = array ();
		$this->selectlist = CKunenaTools::forumSelectList ( 'postmove', 0, $options, ' size="15" class="kmove_selectbox"' );
		$this->message = $this->msg_cat;

		CKunenaTools::loadTemplate ( '/moderate/topicmove.php' );
	}

	protected function domovepost() {
		if ($this->moderatorProtection ())
			return false;

		$leaveGhost = JRequest::getInt ( 'leaveGhost', 0 );
		require_once (KUNENA_PATH_LIB . '/kunena.moderation.class.php');
		$kunena_mod = CKunenaModeration::getInstance ();

		$move = $kunena_mod->moveThread ( $this->id, $this->catid, $leaveGhost );
		if (! $move) {
			$message = $kunena_mod->getErrorMessage ();
		} else {
			$message = JText::_ ( 'COM_KUNENA_POST_SUCCESS_MOVE' );
		}

		$this->_app->redirect ( CKunenaLink::GetCategoryURL ( 'showcat', $this->catid, true ), $message );
	}

	protected function mergethread() {
		if ($this->moderatorProtection ())
			return false;

		$this->message = $this->msg_cat;

		// Get list of latest messages:
		$query = "SELECT id,subject FROM #__fb_messages WHERE parent=0 AND hold=0 AND moved=0 AND catid='{$this->catid}' AND thread!='{$this->message->thread}' ORDER BY id DESC";
		$this->_db->setQuery ( $query, 0, 30 );
		$messagesList = $this->_db->loadObjectlist ();
		check_dberror ( "Unable to load messages." );

		foreach ( $messagesList as $mes ) {
			$messages [] = JHTML::_ ( 'select.option', $mes->id, kunena_htmlspecialchars ( stripslashes ( $mes->subject ) ) );
		}
		$this->selectlist = JHTML::_ ( 'select.genericlist', $messages, 'mergepost', 'class="inputbox" size="15"', 'value', 'text' );

		CKunenaTools::loadTemplate ( '/moderate/topicmerge.php' );
	}

	protected function domergethreadnow() {
		if ($this->moderatorProtection ())
			return false;

		$TargetSubject = JRequest::getVar ( 'messubject', null );
		$TargetThreadID = JRequest::getInt ( 'mergepost', null );
		require_once (KUNENA_PATH_LIB . '/kunena.moderation.class.php');
		$kunena_mod = &CKunenaModeration::getInstance ();

		$merge = $kunena_mod->moveMessageAndNewer ( $this->id, $this->catid, $TargetSubject, $TargetThreadID );
		if (! $merge) {
			$message = $kunena_mod->getErrorMessage ();
		} else {
			$message = JText::_ ( 'COM_KUNENA_POST_SUCCESS_MERGE' );
		}

		$this->_app->redirect ( CKunenaLink::GetLatestPageAutoRedirectURL ( $this->_config, $TargetThreadID, $this->_config->messages_per_page ), $message );

	}

	protected function merge() {
		if ($this->moderatorProtection ())
			return false;

		$this->message = $this->msg_cat;

		// Get list of latest messages:
		$query = "SELECT id,subject FROM #__fb_messages WHERE parent=0 AND hold=0 AND moved=0 AND catid='{$this->catid}' AND thread!='{$this->message->thread}' ORDER BY id DESC";
		$this->_db->setQuery ( $query, 0, 30 );
		$messagesList = $this->_db->loadObjectlist ();
		check_dberror ( "Unable to load messages." );

		foreach ( $messagesList as $mes ) {
			$messages [] = JHTML::_ ( 'select.option', $mes->id, kunena_htmlspecialchars ( stripslashes ( $mes->subject ) ) );
		}
		$this->selectlist = JHTML::_ ( 'select.genericlist', $messages, 'mergepost', 'class="inputbox" multiple="multiple" size="15"', 'value', 'text' );

		CKunenaTools::loadTemplate ( '/moderate/postmerge.php' );
	}

	protected function domergepostnow() {
		if ($this->moderatorProtection ())
			return false;

		$TargetThreadID = JRequest::getInt ( 'mergepost', null );
		$TargetSubject = JRequest::getInt ( 'subject', null );
		require_once (KUNENA_PATH_LIB . '/kunena.moderation.class.php');
		$kunena_mod = &CKunenaModeration::getInstance ();

		$merge = $kunena_mod->moveMessage ( $this->id, $this->catid, $TargetSubject, $TargetThreadID );
		if (! $merge) {
			$message = $kunena_mod->getErrorMessage ();
		} else {
			$message = JText::_ ( 'COM_KUNENA_POST_SUCCESS_MERGE' );
		}

		$this->_app->redirect ( CKunenaLink::GetLatestPageAutoRedirectURL ( $this->_config, $TargetThreadID, $this->_config->messages_per_page ), $message );
	}

	protected function split() {
		if ($this->moderatorProtection ())
			return false;

		$this->message = $this->msg_cat;

		$options = array ();
		$this->selectlist = CKunenaTools::forumSelectList ( 'targetcat', 0, $options, ' size="15" class="ksplit_selectbox"' );

		CKunenaTools::loadTemplate ( '/moderate/postsplit.php' );
	}

	protected function splitnow() {
		if ($this->moderatorProtection ())
			return false;

		require_once (KUNENA_PATH_LIB . '/kunena.moderation.class.php');
		$kunena_mod = &CKunenaModeration::getInstance ();

		$mode = JRequest::getVar ( 'split', null );
		$TargetSubject = JRequest::getVar ( 'messubject', null );

		if ($mode == 'splitpost') { // we split only the message specified
			$TargetCatID = JRequest::getVar ( 'targetcat', null );

			$splitpost = $kunena_mod->moveMessage ( $this->id, $TargetCatID, $TargetSubject );
			if (! $splitpost) {
				$message = $kunena_mod->getErrorMessage ();
			} else {
				$message = JText::_ ( 'COM_KUNENA_POST_SUCCESS_SPLIT' );
			}

		} else { // we split the message specified and the replies to this message
			$TargetCatID = JRequest::getVar ( 'targetcat2', null );
			$splitpost = $kunena_mod->moveMessageAndNewer ( $this->id, $TargetCatID, $TargetSubject );
			if (! $splitpost) {
				$message = $kunena_mod->getErrorMessage ();
			} else {
				$message = JText::_ ( 'COM_KUNENA_POST_SUCCESS_SPLIT' );
			}
		}

		$this->_app->redirect ( CKunenaLink::GetLatestPageAutoRedirectURL ( $this->_config, $this->id, $this->_config->messages_per_page ), $message );
	}

	protected function subscribe() {
		$success_msg = JText::_ ( 'COM_KUNENA_POST_NO_SUBSCRIBED_TOPIC' );
		$this->_db->setQuery ( "SELECT thread FROM #__fb_messages WHERE id='{$this->id}'" );
		if ($this->id && $this->my->id && $this->_db->query ()) {
			$thread = $this->_db->loadResult ();
			$this->_db->setQuery ( "INSERT INTO #__fb_subscriptions (thread,userid) VALUES ('{$thread}','{$this->my->id}')" );

			if (@$this->_db->query () && $this->_db->getAffectedRows () == 1) {
				$success_msg = JText::_ ( 'COM_KUNENA_POST_SUBSCRIBED_TOPIC' );
			}
		}
		$this->_app->redirect ( CKunenaLink::GetLatestPageAutoRedirectURL ( $this->_config, $this->id, $this->_config->messages_per_page ), $success_msg );
	}

	protected function unsubscribe() {
		$success_msg = JText::_ ( 'COM_KUNENA_POST_NO_UNSUBSCRIBED_TOPIC' );
		$this->_db->setQuery ( "SELECT MAX(thread) AS thread FROM #__fb_messages WHERE id='{$this->id}'" );
		if ($this->id && $this->my->id && $this->_db->query ()) {
			$thread = $this->_db->loadResult ();
			$this->_db->setQuery ( "DELETE FROM #__fb_subscriptions WHERE thread={$thread} AND userid={$this->my->id}" );

			if ($this->_db->query () && $this->_db->getAffectedRows () == 1) {
				$success_msg = JText::_ ( 'COM_KUNENA_POST_UNSUBSCRIBED_TOPIC' );
			}
		}
		$this->_app->redirect ( CKunenaLink::GetLatestPageAutoRedirectURL ( $this->_config, $this->id, $this->_config->messages_per_page ), $success_msg );
	}

	protected function favorite() {
		$success_msg = JText::_ ( 'COM_KUNENA_POST_NO_FAVORITED_TOPIC' );
		$this->_db->setQuery ( "SELECT thread FROM #__fb_messages WHERE id='{$this->id}'" );
		if ($this->id && $this->my->id && $this->_db->query ()) {
			$thread = $this->_db->loadResult ();
			$this->_db->setQuery ( "INSERT INTO #__fb_favorites (thread,userid) VALUES ('{$thread}','{$this->my->id}')" );

			if (@$this->_db->query () && $this->_db->getAffectedRows () == 1) {
				$success_msg = JText::_ ( 'COM_KUNENA_POST_FAVORITED_TOPIC' );
			}
		}
		$this->_app->redirect ( CKunenaLink::GetLatestPageAutoRedirectURL ( $this->_config, $this->id, $this->_config->messages_per_page ), $success_msg );
	}

	protected function unfavorite() {
		$success_msg = JText::_ ( 'COM_KUNENA_POST_NO_UNFAVORITED_TOPIC' );
		$this->_db->setQuery ( "SELECT MAX(thread) AS thread FROM #__fb_messages WHERE id='{$this->id}'" );
		if ($this->id && $this->my->id && $this->_db->query ()) {
			$thread = $this->_db->loadResult ();
			$this->_db->setQuery ( "DELETE FROM #__fb_favorites WHERE thread={$thread} AND userid={$this->my->id}" );

			if ($this->_db->query () && $this->_db->getAffectedRows () == 1) {
				$success_msg = JText::_ ( 'COM_KUNENA_POST_UNFAVORITED_TOPIC' );
			}
		}
		$this->_app->redirect ( CKunenaLink::GetLatestPageAutoRedirectURL ( $this->_config, $this->id, $this->_config->messages_per_page ), $success_msg );
	}

	protected function sticky() {
		if ($this->moderatorProtection ())
			return false;

		$success_msg = JText::_ ( 'COM_KUNENA_POST_STICKY_NOT_SET' );
		$this->_db->setQuery ( "update #__fb_messages set ordering=1 where id={$this->id}" );
		if ($this->id && $this->_db->query () && $this->_db->getAffectedRows () == 1) {
			$success_msg = JText::_ ( 'COM_KUNENA_POST_STICKY_SET' );
		}
		$this->_app->redirect ( CKunenaLink::GetLatestPageAutoRedirectURL ( $this->_config, $this->id, $this->_config->messages_per_page ), $success_msg );
	}

	protected function unsticky() {
		if ($this->moderatorProtection ())
			return false;

		$success_msg = JText::_ ( 'COM_KUNENA_POST_STICKY_NOT_UNSET' );
		$this->_db->setQuery ( "update #__fb_messages set ordering=0 where id={$this->id}" );
		if ($this->id && $this->_db->query () && $this->_db->getAffectedRows () == 1) {
			$success_msg = JText::_ ( 'COM_KUNENA_POST_STICKY_UNSET' );
		}
		$this->_app->redirect ( CKunenaLink::GetLatestPageAutoRedirectURL ( $this->_config, $this->id, $this->_config->messages_per_page ), $success_msg );
	}

	protected function lock() {
		if ($this->moderatorProtection ())
			return false;

		$success_msg = JText::_ ( 'COM_KUNENA_POST_LOCK_NOT_SET' );
		$this->_db->setQuery ( "update #__fb_messages set locked=1 where id={$this->id}" );
		if ($this->id && $this->_db->query () && $this->_db->getAffectedRows () == 1) {
			$success_msg = JText::_ ( 'COM_KUNENA_POST_LOCK_SET' );
		}
		$this->_app->redirect ( CKunenaLink::GetLatestPageAutoRedirectURL ( $this->_config, $this->id, $this->_config->messages_per_page ), $success_msg );
	}

	protected function unlock() {
		if ($this->moderatorProtection ())
			return false;

		$success_msg = JText::_ ( 'COM_KUNENA_POST_LOCK_NOT_UNSET' );
		$this->_db->setQuery ( "update #__fb_messages set locked=0 where id={$this->id}" );
		if ($this->id && $this->_db->query () && $this->_db->getAffectedRows () == 1) {
			$success_msg = JText::_ ( 'COM_KUNENA_POST_LOCK_UNSET' );
		}
		$this->_app->redirect ( CKunenaLink::GetLatestPageAutoRedirectURL ( $this->_config, $this->id, $this->_config->messages_per_page ), $success_msg );
	}

	protected function approve() {
		if ($this->moderatorProtection ())
			return false;

		$success_msg = JText::_ ( 'COM_KUNENA_MODERATE_1APPROVE_FAIL' );
		$this->_db->setQuery ( "UPDATE #__fb_messages SET hold=0 WHERE id={$this->id}" );
		if ($this->id && $this->_db->query () && $this->_db->getAffectedRows () == 1) {
			$success_msg = JText::_ ( 'COM_KUNENA_MODERATE_1APPROVE_SUCCESS' );
		}
		$this->_app->redirect ( CKunenaLink::GetLatestPageAutoRedirectURL ( $this->_config, $this->id, $this->_config->messages_per_page ), $success_msg );
	}

	function hasThreadHistory() {
		if (! $this->_config->showhistory || $this->id == 0)
			return false;
		return true;
	}

	function displayThreadHistory() {
		if (! $this->_config->showhistory || $this->id == 0)
			return;

		//get all the messages for this thread
		$query = "SELECT * FROM #__fb_messages AS m LEFT JOIN #__fb_messages_text AS t ON m.id=t.mesid
			WHERE thread='{$this->msg_cat->thread}' AND hold='0' ORDER BY time DESC";
		$this->_db->setQuery ( $query, 0, $this->_config->historylimit );
		$this->messages = $this->_db->loadObjectList ();
		check_dberror ( "Unable to load messages." );

		$this->subject = stripslashes ( $this->msg_cat->subject );

		CKunenaTools::loadTemplate ( '/editor/history.php' );
	}

	protected function getAuthorName($authorname = '', $anonymous = false) {
		static $name = false;

		if (empty ( $name )) {
			if (! $this->my->id || $anonymous) {
				jimport ( 'joomla.user.helper' );

				// By default use authorname (from edited message etc)
				$nickname = JRequest::getVar ( 'authorname', $authorname );
				$nickused = $nickname ? JUserHelper::getUserId ( $nickname ) : 0;

				// Do not allow empty name or existing username
				if (! $nickname || $nickused || $nickname == $this->my->name) {
					$name = JText::_ ( 'COM_KUNENA_USERNAME_ANONYMOUS' );
				} else {
					$name = $nickname;
				}
			} else {
				// By default use authorname (from edited message etc)
				if (! empty ( $authorname ))
					$name = $authorname;
				else
					$name = $this->_config->username ? $this->my->username : $this->my->name;

				// Check if changing name is allowed
				$moderator = CKunenaTools::isModerator ( $this->my->id, $this->catid );
				$nickname = JRequest::getVar ( 'authorname', '' );
				if (! empty ( $nickname ) && ($moderator || $this->_config->changename)) {
					jimport ( 'joomla.user.helper' );
					$nickused = $moderator ? 0 : JUserHelper::getUserId ( $nickname );
					if (! $nickused || $nickused == $this->my->id)
						$name = $nickname;
				}
			}
		}

		return $name;
	}

	protected function moderatorProtection() {
		if (! CKunenaTools::isModerator ( $this->my->id, $this->catid )) {
			$this->_app->enqueueMessage ( JText::_ ( 'COM_KUNENA_POST_NOT_MODERATOR' ), 'notice' );
			return true;
		}
		return false;
	}

	protected function tokenProtection() {
		// get the token put in the message form to check that the form has been valided successfully
		if (JRequest::checkToken () == false) {
			$this->_app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			return true;
		}
		return false;
	}

	protected function lockProtection() {
		if ($this->msg_cat && $this->msg_cat->locked && ! CKunenaTools::isModerator ( $this->my->id, $this->catid )) {
			if ($this->msg_cat->catlocked)
				$this->_app->enqueueMessage ( JText::_ ( 'COM_KUNENA_POST_ERROR_CATEGORY_LOCKED' ), 'error' );
			else
				$this->_app->enqueueMessage ( JText::_ ( 'COM_KUNENA_POST_ERROR_TOPIC_LOCKED' ), 'error' );
			return true;
		}
		return false;
	}

	protected function floodProtection() {
		// Flood protection
		if ($this->_config->floodprotection && ! CKunenaTools::isModerator ( $this->my->id, $this->catid )) {
			$this->_db->setQuery ( "SELECT MAX(time) FROM #__fb_messages WHERE ip='{$this->ip}'" );
			$lastPostTime = $this->_db->loadResult ();
			check_dberror ( "Unable to load max time for current request from IP: {$this->ip}" );

			if ($lastPostTime + $this->_config->floodprotection > CKunenaTimeformat::internalTime ()) {
				echo JText::_ ( 'COM_KUNENA_POST_TOPIC_FLOOD1' ) . ' ' . $this->_config->floodprotection . ' ' . JText::_ ( 'COM_KUNENA_POST_TOPIC_FLOOD2' ) . '<br />';
				echo JText::_ ( 'COM_KUNENA_POST_TOPIC_FLOOD3' );
				return true;
			}
		}
		return false;
	}

	function display() {
		if (!$this->allow) return;
		if ($this->action == "post") {
			$this->post ();
			return;
		} else if ($this->action == "cancel") {
			$this->_app->enqueueMessage ( JText::_ ( 'COM_KUNENA_SUBMIT_CANCEL' ) );
			return;
		}

		switch ($this->do) {
			case 'reply' :
			case 'quote' :
				$this->reply ( $this->do );
				break;

			case 'newFromBot' :
				$this->newFromBot ();
				break;

			case 'edit' :
				$this->edit ();
				break;

			case 'editpostnow' :
				$this->editpostnow ();
				break;

			case 'deleteownpost' :
				$this->deleteownpost ();
				break;

			case 'delete' :
				$this->delete ();
				break;

			case 'deletethread' :
				$this->deletethread ();
				break;

			case 'move' :
				$this->move ();
				break;

			case 'domovepost' :
				$this->domovepost ();
				break;

			case 'mergethread' :
				$this->mergethread ();
				break;

			case 'domergethreadnow' :
				$this->domergethreadnow ();
				break;

			case 'merge' :
				$this->merge ();
				break;

			case 'domergepostnow' :
				$this->domergepostnow ();
				break;

			case 'split' :
				$this->split ();
				break;

			case 'splitnow' :
				$this->splitnow ();
				break;

			case 'subscribe' :
				$this->subscribe ();
				break;

			case 'unsubscribe' :
				$this->unsubscribe ();
				break;

			case 'favorite' :
				$this->favorite ();
				break;

			case 'unfavorite' :
				$this->unfavorite ();
				break;

			case 'sticky' :
				$this->sticky ();
				break;

			case 'unsticky' :
				$this->unsticky ();
				break;

			case 'lock' :
				$this->lock ();
				break;

			case 'unlock' :
				$this->unlock ();
				break;

			case 'approve' :
				$this->approve ();
				break;
		}
	}

	function setTitle($title) {
		$this->document->setTitle ( $title . ' - ' . stripslashes ( $this->_config->board_title ) );
	}

	function hasCaptcha() {
		if ($this->_config->captcha == 1 && $this->my->id < 1) return true;
		return false;
	}

	function displayCaptcha() {
		if (!$this->hasCaptcha()) return;
		if (!JPluginHelper::isEnabled('system', 'jezReCaptcha')) {
			echo JText::_ ( 'reCAPTCHA is not properly configured.' );
			return;
		}
		$lang = explode('-',$this->document->getLanguage());
JApplication::addCustomHeadTag('<script type="text/javascript">
<!--
var RecaptchaOptions = {
	lang : "'.$lang.'"
};
//-->
</script>');
		JPluginHelper::importPlugin( 'jezReCaptcha' );
		$dispatcher =& JDispatcher::getInstance();
		$dispatcher->trigger('onCaptchaDisplay');
	}

	function verifyCaptcha() {
		if (!$this->hasCaptcha()) return;
		if (!JPluginHelper::isEnabled('system', 'jezReCaptcha')) {
			$this->_app->enqueueMessage ( JText::_ ( 'Cannot verify security code: reCAPTCHA is not properly configured.' ), 'error' );
			$this->redirectBack();
		}
		JPluginHelper::importPlugin( 'jezReCaptcha' );
		$dispatcher =& JDispatcher::getInstance();
		$dispatcher->trigger('onCaptchaConfirm');
	}

	function redirectBack() {
		$httpReferer = JRequest::getVar('HTTP_REFERER', JURI::base(true), 'server');
		$this->_app->redirect($httpReferer);
	}
}
