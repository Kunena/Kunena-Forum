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
// Dont allow direct linking
defined( '_JEXEC' ) or die('Restricted access');

$app =& JFactory::getApplication();
$fbConfig =& CKunenaConfig::getInstance();
$fbSession =& CKunenaSession::getInstance();

global $is_Moderator;

$catid = JRequest::getInt('catid', 0);
$id = JRequest::getInt('id', 0);
// Support for old $replyto variable in post reply/quote
if (!$id) $id = JRequest::getInt('replyto', 0);

if ($id && $do != 'domovepost' && $do != 'domergepost' && $do != 'dosplit')
{
	// If message exists, override catid to be sure that user can post there
	$kunena_db->setQuery("SELECT catid FROM #__fb_messages WHERE id='{$id}'");
	$msgcat = $kunena_db->loadResult();
	check_dberror('Unable to check message.');
	if ($msgcat) $catid = $msgcat;
}

//get the allowed forums and turn it into an array
$allow_forum = ($fbSession->allowed <> '')?explode(',', $fbSession->allowed):array();

if (!in_array($catid, $allow_forum))
{
	echo _KUNENA_NO_ACCESS;
	return;
}

//
//ob_start();
$pubwrite = (int)$fbConfig->pubwrite;
//ip for floodprotection, post logging, subscriptions, etcetera
$ip = $_SERVER["REMOTE_ADDR"];

//reset variables used
// ERROR: mixed global $editmode
global $editmode;
$kunena_my = &JFactory::getUser();
$kunena_acl = &JFactory::getACL();
$kunena_db = &JFactory::getDBO();
$editmode = 0;
$message = JRequest::getVar('message', '', 'REQUEST', 'string', JREQUEST_ALLOWRAW);
$resubject = JRequest::getVar('resubject', '', 'REQUEST', 'string');
$anonymous = JRequest::getInt('anonymous' , 0);

$attachfile 	= JRequest::getVar('attachfile', NULL, 'FILES', 'array');
$attachimage 	= JRequest::getVar('attachimage', NULL, 'FILES', 'array');

//ip for floodprotection, post logging, subscriptions, etcetera
$ip = $_SERVER ["REMOTE_ADDR"];

//flood protection
$fbConfig->floodprotection = (int)$fbConfig->floodprotection;

// Flood protection
if ($fbConfig->floodprotection && ($action == "post" || $do == 'quote' || $do == 'reply') && ! $is_admin) {
	$kunena_db->setQuery ( "SELECT MAX(time) FROM #__fb_messages WHERE ip='{$ip}'" );
 	$lastPostTime = $kunena_db->loadResult ();
	check_dberror ( "Unable to load max time for current request from IP: $ip" );

	if ($lastPostTime + $fbConfig->floodprotection > $systime) {
		echo _POST_TOPIC_FLOOD1 . ' ' . $fbConfig->floodprotection . ' ' . _POST_TOPIC_FLOOD2 . '<br />';
 	  	echo _POST_TOPIC_FLOOD3;
 	  	return;
	}
}

// Begin captcha
if ($fbConfig->captcha == 1 && $kunena_my->id < 1) {
    $number = JRequest::getVar ( 'txtNumber', '', 'POST' );

    if ($message != NULL)
    {
	$session =& JFactory::getSession();
	$rand = $session->get('fb_image_random_value');
	unset($session);

    	if (md5($number) != $rand)
        {
            $mess = _KUNENA_CAPERR;
            echo "<script language='javascript' type='text/javascript'>alert('" . $mess . "')</script>";
            echo "<script language='javascript' type='text/javascript'>window.history.back()</script>";
            return;
            $app->close();
            //break;
        }
    }
}

// Finish captcha

//Let's find out who we're dealing with if a registered user wants to make a post
if ($kunena_my->id) {
	$my_name = $fbConfig->username ? $kunena_my->username : $kunena_my->name;
    $user_email = $kunena_my->email;
    $registeredUser = 1;
	if ($is_Moderator) {
		if (!empty($fb_authorname)) $my_name = $fb_authorname;
		if(!empty($email))
			$user_email = $email;
	}
} else {
	$my_name = $fb_authorname;
	$user_email = (isset($email) && !empty($email))? $email:'';
	$registeredUser = 0;
}

//Now find out the forumname to which the user wants to post (for reference only)
$kunena_db->setQuery("SELECT * FROM #__fb_categories WHERE id='{$catid}'");
$kunena_db->query() or trigger_dberror('Unable to load category.');

$objCatInfo = $kunena_db->loadObject();
$catName = $objCatInfo->name;

if (empty($objCatInfo)) {
    // Do not allow actions in categories that do not exist
    echo _KUNENA_POST_ERROR_NO_CATEGORY;
    return;
} else if ($objCatInfo->parent == 0) {
    // Do not allow actions in sections
    echo _KUNENA_POST_ERROR_IS_SECTION;
    return;
}

?>

<table border = "0" cellspacing = "0" cellpadding = "0" width = "100%" align = "center">
    <tr>
        <td>
            <?php
            if (file_exists(KUNENA_ABSTMPLTPATH . '/fb_pathway.php')) {
                require_once (KUNENA_ABSTMPLTPATH . '/fb_pathway.php');
            }
            else {
                require_once (KUNENA_PATH_TEMPLATE_DEFAULT .DS. 'fb_pathway.php');
            }

            if ($action == "post" && (hasPostPermission($kunena_db, $catid, $parentid, $kunena_my->id, $fbConfig->pubwrite, $is_Moderator)))
            {
            ?>

                <table border = "0" cellspacing = "1" cellpadding = "3" width = "70%" align = "center" class = "contentpane">
                    <tr>
                        <td>
                            <?php
                            $parent = (int)$parentid;
							if ($fbConfig->askemail) jimport( 'joomla.mail.helper' );
                            if ($anonymous && !$objCatInfo->allow_anonymous) {
                                echo _KUNENA_POST_ERROR_ANONYMOUS_FORBITTEN;
                            } else if (empty($my_name)) {
                                echo _POST_FORGOT_NAME;
                            } else if ($fbConfig->askemail && empty($user_email)) {
                                echo _POST_FORGOT_EMAIL;
                            } else if (!$kunena_my->id && $fbConfig->askemail && ! JMailHelper::isEmailAddress($user_email)) {
 	  	                    	echo _KUNENA_MY_EMAIL_INVALID;
                            } else if (empty($subject)) {
                                echo _POST_FORGOT_SUBJECT;
                            }
                            else if (empty($message)) {
                                echo _POST_FORGOT_MESSAGE;
                            }
                            else
                            {
                                if ($parent == 0) {
                                    $thread = $parent = 0;
                                }

                                $kunena_db->setQuery("SELECT id, thread, parent FROM #__fb_messages WHERE id='{$parent}'");
                                $kunena_db->query() or trigger_dberror('Unable to load parent post.');
                                unset($m);
                                $m = $kunena_db->loadObject();

                                if (count($m) < 1)
                                {
                                    // bad parent, create a new post
                                    $parent = 0;
                                    $thread = 0;
                                }
                                else
                                {

                                    $thread = $m->parent == 0 ? $m->id : $m->thread;
                                }

                                if ($catid == 0) {
                                    $catid = 1; //make sure there's a proper category
                                }

                                if (is_array($attachfile) && $attachfile['error'] != UPLOAD_ERR_NO_FILE)
                                {
                                    include (KUNENA_PATH_LIB .DS. 'kunena.file.upload.php');
                                }
                                if (is_array($attachimage) && $attachimage['error'] != UPLOAD_ERR_NO_FILE)
                                {
                                    include (KUNENA_PATH_LIB .DS. 'kunena.image.upload.php');
                                }

                                $messagesubject = $subject; //before we add slashes and all... used later in mail

                                $userid = $kunena_my->id;
                                if ($anonymous) {
                                    // Anonymous post: remove all user information from the post
                                    $userid = 0;
                                    jimport('joomla.user.helper');
                                    if ($my_name == $kunena_my->name || $my_name == $kunena_my->username || JUserHelper::getUserId($my_name)) {
                                        $my_name = _KUNENA_USERNAME_ANONYMOUS;
                                    }
                                    $user_email = '';
                                    $ip = '';
                                }

                                $fb_authorname = trim(addslashes($my_name));
                                $subject = trim(addslashes($subject));
                                $message = trim(addslashes($message));

                                if ($contentURL != "empty") {
                                    $message = $contentURL . '\n\n' . $message;
                                }

                                //--
                                $email = trim(addslashes($user_email));
                                $topic_emoticon = (int)$topic_emoticon;
                                $topic_emoticon = ($topic_emoticon < 0 || $topic_emoticon > 7) ? 0 : $topic_emoticon;
                                $posttime = CKunenaTools::fbGetInternalTime();
                                //check if the post must be reviewed by a Moderator prior to showing
                                //doesn't apply to admin/moderator posts ;-)
                                $holdPost = 0;

                                if (!$is_Moderator)
                                {
                                    $kunena_db->setQuery("SELECT review FROM #__fb_categories WHERE id='{$catid}'");
                                    $kunena_db->query() or trigger_dberror('Unable to load review flag from categories.');
                                    $holdPost = $kunena_db->loadResult();
                                }

                                //
                                // Final chance to check whether or not to proceed
                                // DO NOT PROCEED if there is an exact copy of the message already in the db
                                //
                                $duplicatetimewindow = $posttime - $fbConfig->fbsessiontimeout;
                                unset($existingPost);
                                $kunena_db->setQuery("SELECT m.id FROM #__fb_messages AS m JOIN #__fb_messages_text AS t ON m.id=t.mesid WHERE m.userid='{$userid}' AND m.name='{$fb_authorname}' AND m.email='{$email}' AND m.subject='{$subject}' AND m.ip='{$ip}' AND t.message='{$message}' AND m.time>='{$duplicatetimewindow}'");
                                $kunena_db->query() or trigger_dberror('Unable to load post.');

                                $existingPost = $kunena_db->loadObject();
                                unset($pid);
                                if ($existingPost !== null) $pid = $existingPost->id;

                                if (!isset($pid))
                                {
                                    $kunena_db->setQuery("INSERT INTO #__fb_messages
                                    						(parent,thread,catid,name,userid,email,subject,time,ip,topic_emoticon,hold)
                                    						VALUES('$parent','$thread','$catid',".$kunena_db->quote($fb_authorname).",'{$userid}',".$kunena_db->quote($email).",".$kunena_db->quote($subject).",'$posttime','$ip','$topic_emoticon','$holdPost')");

    			                    if ($kunena_db->query())
                                    {
                                        $pid = $kunena_db->insertId();

                                        // now increase the #s in categories only case approved
                                        if($holdPost==0) {
                                          CKunenaTools::modifyCategoryStats($pid, $parent, $posttime, $catid);
                                        }

                                        $kunena_db->setQuery("INSERT INTO #__fb_messages_text (mesid,message) VALUES('$pid',".$kunena_db->quote($message).")");
                                        $kunena_db->query();

                                        // A couple more tasks required...
                                        if ($thread == 0) {
                                            //if thread was zero, we now know to which id it belongs, so we can determine the thread and update it
                                            $kunena_db->setQuery("UPDATE #__fb_messages SET thread='$pid' WHERE id='$pid'");
                                            $kunena_db->query();

                                            // if JomScoial integration is active integrate user points and activity stream
                                            if ($fbConfig->pm_component == 'jomsocial' || $fbConfig->fb_profile == 'jomsocial' || $fbConfig->avatar_src == 'jomsocial')
                                            {
												include_once(KUNENA_ROOT_PATH .DS. 'components/com_community/libraries/userpoints.php');

												CuserPoints::assignPoint('com_kunena.thread.new');

												// Check for permisions of the current category - activity only if public
												if ($thisCat->getPubAccess() == 0)
												{

													if ($fbConfig->js_actstr_integration) {
														//activity stream  - new post
														$JSPostLink = CKunenaLink::GetThreadPageURL($fbConfig, 'view', $catid, $pid, 1);

														$content = stripslashes($message);
														$content = smile::smileReplace($content, 0, $fbConfig->disemoticons, $smileyList);
														$content = nl2br($content);

														$act = new stdClass();
														$act->cmd    = 'wall.write';
														$act->actor  = $userid;
														$act->target = 0; // no target
														$act->title  = JText::_('{actor} '._KUNENA_JS_ACTIVITYSTREAM_CREATE_MSG1.' <a href="'.$JSPostLink.'">'.stripslashes($subject).'</a> '._KUNENA_JS_ACTIVITYSTREAM_CREATE_MSG2);
														$act->content= $content;
														$act->app    = 'wall';
														$act->cid    = 0;

														CFactory::load('libraries', 'activities');
														CActivityStream::add($act);
													}
												}
                                            }

										}
										else
										{
                                            // if JomScoial integration is active integrate user points and activity stream
                                            if ($fbConfig->pm_component == 'jomsocial' || $fbConfig->fb_profile == 'jomsocial' || $fbConfig->avatar_src == 'jomsocial')
                                            {
                                            	include_once(KUNENA_ROOT_PATH .DS. 'components/com_community/libraries/userpoints.php');

												CuserPoints::assignPoint('com_kunena.thread.reply');

												// Check for permisions of the current category - activity only if public
												if ($thisCat->getPubAccess() == 0 && $fbConfig->js_actstr_integration)
												{
													if ($fbConfig->js_actstr_integration) {
														//activity stream - reply post
														$JSPostLink = CKunenaLink::GetThreadPageURL($fbConfig, 'view', $catid, $thread, 1);

														$content = stripslashes($message);
														$content = smile::smileReplace($content, 0, $fbConfig->disemoticons, $smileyList);
														$content = nl2br($content);

														$act = new stdClass();
														$act->cmd    = 'wall.write';
														$act->actor  = $userid;
														$act->target = 0; // no target
														$act->title  = JText::_('{single}{actor}{/single}{multiple}{actors}{/multiple} '._KUNENA_JS_ACTIVITYSTREAM_REPLY_MSG1.' <a href="'.$JSPostLink.'">'.stripslashes($subject).'</a> '._KUNENA_JS_ACTIVITYSTREAM_REPLY_MSG2);
														$act->content= $content;
														$act->app    = 'wall';
														$act->cid    = 0;

														CFactory::load('libraries', 'activities');
														CActivityStream::add($act);
													}
												}
                                            }
										}
										// End Modify for activities stream

                                        //update the user posts count
                                        if ($userid)
                                        {
                                            $kunena_db->setQuery("UPDATE #__fb_users SET posts=posts+1 WHERE userid={$userid}");
                                            $kunena_db->query();
                                        }

                                        //Update the attachments table if an image has been attached
                                        if (!empty($imageLocation) && file_exists($imageLocation))
                                        {
                                            $kunena_db->setQuery("INSERT INTO #__fb_attachments (mesid, filelocation) values ('$pid',".$kunena_db->quote($imageLocation).")");

                                            if (!$kunena_db->query()) {
                                                echo "<script> alert('Storing image failed: " . $kunena_db->getErrorMsg() . "'); </script>\n";
                                            }
                                        }

                                        //Update the attachments table if an file has been attached
                                        if (!empty($fileLocation) && file_exists($fileLocation))
                                        {
                                            $kunena_db->setQuery("INSERT INTO #__fb_attachments (mesid, filelocation) values ('$pid',".$kunena_db->quote($fileLocation).")");

                                            if (!$kunena_db->query()) {
                                                echo "<script> alert('Storing file failed: " . $kunena_db->getErrorMsg() . "'); </script>\n";
                                            }
                                        }

                                        // Perform proper page pagination for better SEO support
                                        // used in subscriptions and auto redirect back to latest post
                                        if ($thread == 0) {
                                            $querythread = $pid;
                                        }
                                        else {
                                            $querythread = $thread;
                                        }

                                        $kunena_db->setQuery("SELECT * FROM #__fb_sessions WHERE readtopics LIKE '%$thread%' AND userid!={$kunena_my->id}");
                                        $sessions = $kunena_db->loadObjectList();
                                        	check_dberror("Unable to load sessions.");
                                        foreach ($sessions as $session)
                                        {
                                            $readtopics = $session->readtopics;
                                            $userid = $session->userid;
                                            $rt = explode(",", $readtopics);
                                            $key = array_search($thread, $rt);
                                            if ($key !== FALSE)
                                            {
                                                unset($rt[$key]);
                                                $readtopics = implode(",", $rt);
                                                $kunena_db->setQuery("UPDATE #__fb_sessions SET readtopics='$readtopics' WHERE userid=$userid");
                                                $kunena_db->query();
                                                	check_dberror("Unable to update sessions.");
                                            }
                                        }

                                        unset($result);
                                        $kunena_db->setQuery("SELECT COUNT(*) AS totalmessages FROM #__fb_messages WHERE thread='{$querythread}'");
                                        $result = $kunena_db->loadObject();
                                        	check_dberror("Unable to load messages.");
                                        $threadPages = ceil($result->totalmessages / $fbConfig->messages_per_page);
                                        //construct a useable URL (for plaintext - so no &amp; encoding!)
                                        jimport('joomla.environment.uri');
                                        $uri =& JURI::getInstance(JURI::base());
                                        $LastPostUrl = $uri->toString(array('scheme', 'host', 'port')) . str_replace('&amp;', '&', CKunenaLink::GetThreadPageURL($fbConfig, 'view', $catid, $querythread, $threadPages, $fbConfig->messages_per_page, $pid));

										// start integration alphauserpoints component
										if ( $fbConfig->alphauserpointsrules ) {
											// Insert AlphaUserPoints rules
											$api_AUP = JPATH_SITE.DS.'components'.DS.'com_alphauserpoints'.DS.'helper.php';
											$datareference = '<a href="' . $LastPostUrl . '">' . $subject . '</a>';
											if ( file_exists($api_AUP))
											{
											  require_once ($api_AUP);
											  if ( $thread == 0 )
											  {
												// rule for post a new topic
												AlphaUserPointsHelper::newpoints( 'plgaup_newtopic_kunena', '', $pid,  $datareference );
											  }
											  else
											  {
											  	// rule for post a reply to a topic
												if ( $fbConfig->alphauserpointsnumchars>0 ) {
													// use if limit chars for a response
													if ( strlen($message)>$fbConfig->alphauserpointsnumchars ) {
														AlphaUserPointsHelper::newpoints( 'plgaup_reply_kunena', '', $pid, $datareference );
													} else {
														$app->enqueueMessage( _KUNENA_AUP_MESSAGE_TOO_SHORT );
													}
												} else {
													AlphaUserPointsHelper::newpoints( 'plgaup_reply_kunena', '', $pid, $datareference );
												}
											  }
											}
										}
										// end insertion AlphaUserPoints

										//clean up the message for review
										$mailmessage = smile::purify ( stripslashes ( $message ) );

										//get all subscriptions and moderators
		  	                            $emailToList = CKunenaTools::getEMailToList($catid, $querythread, $fbConfig->allowsubscriptions && !$holdPost,
                                           $fbConfig->mailmod, $fbConfig->mailadmin, $kunena_my->id);

                                        //Now manage the subscriptions (only if subscriptions are allowed)
                                        if (count($emailToList)) {
                                            jimport ( 'joomla.mail.helper' );
                                        	if (! $fbConfig->email  || ! JMailHelper::isEmailAddress($fbConfig->email)) {
                                            	$app->enqueueMessage (_KUNENA_EMAIL_INVALID, 'error' );
                                            } else {

                                            	$mailsender = JMailHelper::cleanAddress( stripslashes ( $board_title ) . " " . _GEN_FORUM );
                                               	$mailsubject = JMailHelper::cleanSubject("[" . stripslashes ( $board_title ) . " " . _GEN_FORUM . "] " . stripslashes ( $messagesubject ) . " (" . stripslashes ( $catName ) . ")");

												foreach ( $emailToList as $emailTo ) {
													if (! $emailTo->email || ! JMailHelper::isEmailAddress($emailTo->email)) continue;

													if ($emailTo->subscription) {
														$msg1 = $_COM_A_NOTIFICATION1;
														$msg2 = $_COM_A_NOTIFICATION2;
													} else {
														$msg1 = $_COM_A_NOT_MOD1;
														$msg2 = $_COM_A_NOT_MOD2;
													}

                                                    $msg = "$emailTo->name,\n\n";
                                                    $msg .=  $msg1 . " " . stripslashes ( $board_title ) . " " . _GEN_FORUM . "\n\n";
                                                    $msg .= _GEN_SUBJECT." : " . stripslashes($messagesubject) . "\n";
                                                    $msg .= _GEN_FORUM." : " . stripslashes($catName) . "\n";
                                                    $msg .= _VIEW_POSTED." : " . stripslashes($fb_authorname) . "\n\n";
                                                    $msg .= $msg2 . "\n";
                                                    $msg .= "URL: $LastPostUrl\n\n";
                                                    if ($fbConfig->mailfull == 1) {
                                                        $msg .= _GEN_MESSAGE.":\n-----\n";
                                                        $msg .= $mailmessage;
                                                        $msg .= "\n-----";
                                                    }
                                                    $msg .= "\n\n";
                                                    $msg .= "$_COM_A_NOTIFICATION3\n";
                                                    $msg .= "\n\n\n\n";
                                                    $msg .= "** Powered by Kunena! - http://www.Kunena.com **";
                                                    $msg = JMailHelper::cleanBody($msg);

                                                    JUtility::sendMail($fbConfig->email, $mailsender, $emailTo->email, $mailsubject, $msg);
                                                }
                                            }
                                        }

                                        //now try adding any new subscriptions if asked for by the poster
                                        if ($subscribeMe == 1)
                                        {
                                            if ($thread == 0) {
                                                $fb_thread = $pid;
                                            }
                                            else {
                                                $fb_thread = $thread;
                                            }

                                            $kunena_db->setQuery("INSERT INTO #__fb_subscriptions (thread,userid) VALUES ('$fb_thread','{$kunena_my->id}')");

                                            if (@$kunena_db->query()) {
                                                echo '<br /><br /><div align="center">' . _POST_SUBSCRIBED_TOPIC . '</div><br /><br />';
                                            }
                                            else {
                                                echo '<br /><br /><div align="center">' . _POST_NO_SUBSCRIBED_TOPIC . '</div><br /><br />';
                                            }
                                        }

                                        if ($holdPost == 1)
                                        {
                                            echo '<br /><br /><div align="center">' . _POST_SUCCES_REVIEW . '</div><br /><br />';
                                           	echo CKunenaLink::GetLatestPostAutoRedirectHTML($fbConfig, $pid, $fbConfig->messages_per_page, $catid);

                                        }
                                        else
                                        {
                                            echo '<br /><br /><div align="center">' . _POST_SUCCESS_POSTED . '</div><br /><br />';
                                            echo CKunenaLink::GetLatestPostAutoRedirectHTML($fbConfig, $pid, $fbConfig->messages_per_page, $catid);
                                        }
                                    }
                                    else {
                                        echo _POST_ERROR_MESSAGE;
                                    }
                                }
                                else
                                // We get here in case we have detected a double post
                                // We did not do any further processing and just display the success message
                                {
                                    echo '<br /><br /><div align="center">' . _POST_DUPLICATE_IGNORED . '</div><br /><br />';
                                    echo CKunenaLink::GetLatestPostAutoRedirectHTML($fbConfig, $pid, $fbConfig->messages_per_page, $catid);
                                }
                            }
                            ?>
                        </td>
                    </tr>
                </table>

            <?php
            }
            else if ($action == "cancel")
            {
                echo '<br /><br /><div align="center">' . _SUBMIT_CANCEL . "</div><br />";
                echo CKunenaLink::GetLatestPostAutoRedirectHTML($fbConfig, $pid, $fbConfig->messages_per_page, $catid);
            }
            else
            {
                if ($do == "quote" && (hasPostPermission($kunena_db, $catid, $id, $kunena_my->id, $fbConfig->pubwrite, $is_Moderator)))
                { //reply do quote
                    $parentid = 0;
                    $id = (int)$id;

                    if ($id > 0)
                    {
                        $kunena_db->setQuery("SELECT m.*, t.mesid, t.message FROM #__fb_messages AS m, #__fb_messages_text AS t WHERE m.id='{$id}' AND t.mesid='{$id}'");
                        $kunena_db->query();

                        if ($kunena_db->getNumRows() > 0)
                        {
                            unset($message);
                            $message = $kunena_db->loadObject();

                            // don't forget stripslashes
                            //$message->message=smile::smileReplace($message->message,0);
                            $table = array_flip(get_html_translation_table(HTML_ENTITIES, ENT_QUOTES));
                            //$quote = strtr($message->message, $table);
                            $quote = stripslashes($message->message);

                            $htmlText = "[b]" . stripslashes($message->name) . " " . _POST_WROTE . ":[/b]\n";
                            $htmlText .= '[quote]' . $quote . "[/quote]";

                            $resubject = strtr($message->subject, $table);
                            $resubject = strtolower(substr($resubject, 0, strlen(_POST_RE))) == strtolower(_POST_RE) ? stripslashes($resubject) : _POST_RE .' '. stripslashes($resubject);
                            $resubject = kunena_htmlspecialchars($resubject);
                            $parentid = $message->id;
                            $authorName = $my_name;
                        }
                    }
                    $allow_anonymous = !empty($objCatInfo->allow_anonymous) && $kunena_my->id;
                    $anonymous = ($allow_anonymous && !empty($objCatInfo->post_anonymous));
                    ?>

                    <form action = "<?php echo JRoute::_(KUNENA_LIVEURLREL.'&amp;func=post'); ?>" method = "post" name = "postform" enctype = "multipart/form-data">
                        <input type = "hidden" name = "parentid" value = "<?php echo $parentid;?>"/>

                        <input type = "hidden" name = "catid" value = "<?php echo $catid;?>"/>

                        <input type = "hidden" name = "action" value = "post"/>

                        <input type = "hidden" name = "contentURL" value = "empty"/>

                        <?php
                        //get the writing stuff in:
                        $no_upload = "0"; //only edit mode should disallow this

                        if (file_exists(KUNENA_ABSTMPLTPATH . '/fb_write.html.php')) {
                            include (KUNENA_ABSTMPLTPATH . '/fb_write.html.php');
                        }
                        else {
                            include (KUNENA_PATH_TEMPLATE_DEFAULT .DS. 'fb_write.html.php');
                        }
                        //--
                        //echo "</form>";
                }
                else if ($do == "reply" && (hasPostPermission($kunena_db, $catid, $id, $kunena_my->id, $fbConfig->pubwrite, $is_Moderator)))
                { // reply no quote
                    $parentid = 0;
                    $id = (int)$id;
                    $setFocus = 0;

                    if ($id > 0)
                    {
                        $kunena_db->setQuery("SELECT m.*, t.mesid, t.message FROM #__fb_messages AS m, #__fb_messages_text AS t WHERE m.id='{$id}' AND t.mesid='{$id}'");
                        $kunena_db->query();

                        if ($kunena_db->getNumRows() > 0)
                        {
                            unset($message);
                            $message = $kunena_db->loadObject();
                            $table = array_flip(get_html_translation_table(HTML_ENTITIES));
                            $resubject = strtr($message->subject, $table);
                            $resubject = strtolower(substr($resubject, 0, strlen(_POST_RE))) == strtolower(_POST_RE) ? stripslashes($resubject) : _POST_RE .' '. stripslashes($resubject);
                            $resubject = kunena_htmlspecialchars($resubject);
                            $parentid = $message->id;
                            $htmlText = "";
                        }
                    }

                    $authorName = $my_name;
                    $allow_anonymous = !empty($objCatInfo->allow_anonymous) && $kunena_my->id;
                    $anonymous = ($allow_anonymous && !empty($objCatInfo->post_anonymous));
                    ?>

                    <form action = "<?php echo JRoute::_(KUNENA_LIVEURLREL . '&amp;func=post'); ?>" method = "post" name = "postform" enctype = "multipart/form-data">
                        <input type = "hidden" name = "parentid" value = "<?php echo $parentid;?>"/>

                        <input type = "hidden" name = "catid" value = "<?php echo $catid;?>"/>

                        <input type = "hidden" name = "action" value = "post"/>

                        <input type = "hidden" name = "contentURL" value = "empty"/>

                        <?php
                        //get the writing stuff in:
                        if (file_exists(KUNENA_ABSTMPLTPATH . '/fb_write.html.php')) {
                            include (KUNENA_ABSTMPLTPATH . '/fb_write.html.php');
                        }
                        else {
                            include (KUNENA_PATH_TEMPLATE_DEFAULT .DS. 'fb_write.html.php');
                        }
                        //--
                        //echo "</form>";
                }
                else if ($do == "newFromBot" && (hasPostPermission($kunena_db, $catid, $id, $kunena_my->id, $fbConfig->pubwrite, $is_Moderator)))
                { // The Mosbot "discuss on forums" has detected an unexisting thread and wants to create one
                    $parentid = 0;
                    $id = (int)$id;
                    $setFocus = 0;
                    //                $resubject = base64_decode($resubject); //per mf#6100  -- jdg 16/07/2005
                    $resubject = base64_decode(strtr($resubject, "()", "+/"));
                    $resubject = str_replace("%20", " ", $resubject);
                    $resubject = preg_replace('/%32/', '&', $resubject);
                    $resubject = preg_replace('/%33/', ';', $resubject);
                    $resubject = preg_replace("/\'/", '&#039;', $resubject);
                    $resubject = preg_replace("/\"/", '&quot;', $resubject);
                    //$table = array_flip(get_html_translation_table(HTML_ENTITIES));
                    //$resubject = strtr($resubject, $table);
                    $fromBot = 1; //this new topic comes from the discuss mambot
                    $authorName = kunena_htmlspecialchars($my_name);
                    $rowid = JRequest::getInt('rowid', 0);
                    $rowItemid = JRequest::getInt('rowItemid', 0);

                    if ($rowItemid) {
                        $contentURL = JRoute::_('index.php?option=com_content&amp;task=view&amp;Itemid=' . $rowItemid . '&amp;id=' . $rowid);
                    }
                    else {
                        $contentURL = JRoute::_('index.php?option=com_content&amp;task=view&amp;Itemid=1&amp;id=' . $rowid);
                    }

                    $contentURL = _POST_DISCUSS . ': [url=' . $contentURL . ']' . $resubject . '[/url]';
                        ?>

                    <form action = "<?php echo JRoute::_(KUNENA_LIVEURLREL."&amp;func=post");?>" method = "post" name = "postform" enctype = "multipart/form-data">
                        <input type = "hidden" name = "parentid" value = "<?php echo $parentid;?>"/>

                        <input type = "hidden" name = "catid" value = "<?php echo $catid;?>"/>

                        <input type = "hidden" name = "action" value = "post"/>

                        <input type = "hidden" name = "contentURL" value = "<?php echo $contentURL ;?>"/>

                        <?php
                        //get the writing stuff in:
                        if (file_exists(KUNENA_ABSTMPLTPATH . '/fb_write.html.php')) {
                            include (KUNENA_ABSTMPLTPATH . '/fb_write.html.php');
                        }
                        else {
                            include (KUNENA_PATH_TEMPLATE_DEFAULT .DS. 'fb_write.html.php');
                        }
                        //--
                        //echo "</form>";
                }
                else if ($do == "edit")
                {
                    $allowEdit = 0;
                    $id = (int)$id;
                    $kunena_db->setQuery("SELECT * FROM #__fb_messages AS m LEFT JOIN #__fb_messages_text AS t ON m.id=t.mesid WHERE m.id='{$id}'");
                    $message1 = $kunena_db->loadObjectList();
                    	check_dberror("Unable to load message.");
                    $mes = $message1[0];

                    $userID = $mes->userid;

                    //Check for a moderator or superadmin
                    if ($is_Moderator) {
                        $allowEdit = 1;
                    }

                    if ($fbConfig->useredit == 1 && $kunena_my->id != "")
                    {
                        //Now, if the author==viewer and the viewer is allowed to edit his/her own post the let them edit
                        if ($kunena_my->id == $userID) {
                            if(((int)$fbConfig->useredittime)==0) {
                                $allowEdit = 1;
                            }
                            else {
                                //Check whether edit is in time
                                $modtime = $mes->modified_time;
                                if(!$modtime) {
                                    $modtime = $mes->time;
                                }
                                if(($modtime + ((int)$fbConfig->useredittime)) >= CKunenaTools::fbGetInternalTime()) {
                                    $allowEdit = 1;
                                }
                            }
                        }
                    }

                    if ($allowEdit == 1)
                    {
                        //we're now in edit mode
                        $editmode = 1;

                        /*foreach ($message1 as $mes)
                        {*/

                        //$htmlText = smile::fbStripHtmlTags($mes->message);
                        $htmlText = stripslashes($mes->message);
                        $table = array_flip(get_html_translation_table(HTML_ENTITIES));
                        $user_email = kunena_htmlspecialchars ( stripslashes ( $mes->email ) );

                        //$htmlText = strtr($htmlText, $table);

                        //$htmlText = smile::fbHtmlSafe($htmlText);
                        $resubject = kunena_htmlspecialchars(stripslashes($mes->subject));
                        $authorName = kunena_htmlspecialchars($mes->name);

                        $allow_anonymous = !empty($objCatInfo->allow_anonymous) && $kunena_my->id;
                        $anonymous = ($allow_anonymous && !empty($objCatInfo->post_anonymous));
                        ?>

                        <form action = "<?php echo JRoute::_(KUNENA_LIVEURLREL."&amp;catid=$catid&amp;func=post"); ?>" method = "post" name = "postform" enctype = "multipart/form-data"/>

                        <input type = "hidden" name = "id" value = "<?php echo $mes->id;?>"/>

                        <input type = "hidden" name = "do" value = "editpostnow"/>

                        <?php
                        //get the writing stuff in:
                        //first check if there is an uploaded image or file already for this post (no new ones allowed)
                        $no_file_upload = 0;
                        $no_image_upload = 0;
                        /*
                        $kunena_db->setQuery("SELECT filelocation FROM #__fb_attachments WHERE mesid='{$id}'");
                        $attachments = $kunena_db->loadObjectList();
                        	check_dberror("Unable to load attachements.");

                        if (count($attachments > 0))
                        {
                            foreach ($attachments as $att)
                            {
                                if (preg_match("&/fbfiles/files/&si", $att->filelocation)) {
                                    $no_file_upload = "1";
                                }

                                if (preg_match("&/fbfiles/images/&si", $att->filelocation)) {
                                    $no_image_upload = "1";
                                }
                            }
                        }
                        */

                        if (file_exists(KUNENA_ABSTMPLTPATH . '/fb_write.html.php')) {
                            include (KUNENA_ABSTMPLTPATH . '/fb_write.html.php');
                        }
                        else {
                            include (KUNENA_PATH_TEMPLATE_DEFAULT .DS. 'fb_write.html.php');
                        }
                        //echo "</form>";
                        //}
                    }
                    else {
			$app->redirect(htmlspecialchars_decode(JRoute::_(KUNENA_LIVEURLREL)), _POST_NOT_MODERATOR);
                    }
                }
                else if ($do == "editpostnow")
                {
                    $modified_reason = addslashes(JRequest::getVar("modified_reason", null));
                    $modified_by = $kunena_my->id;
                    $modified_time = CKunenaTools::fbGetInternalTime();
                    $id  = (int) $id;

                    $kunena_db->setQuery("SELECT * FROM #__fb_messages AS m LEFT JOIN #__fb_messages_text AS t ON m.id=t.mesid WHERE m.id='{$id}'");
                    $message1 = $kunena_db->loadObjectList();
                    	check_dberror("Unable to load messages.");
                    $mes = $message1[0];
                    $userid = $mes->userid;

                    //Check for a moderator or superadmin
                    if ($is_Moderator) {
                        $allowEdit = 1;
                    }

                    if ($fbConfig->useredit == 1 && $kunena_my->id != "")
                    {
                        //Now, if the author==viewer and the viewer is allowed to edit his/her own post the let them edit
                        if ($kunena_my->id == $userid) {
                            if(((int)$fbConfig->useredittime)==0) {
                                $allowEdit = 1;
                            }
                            else {
                                $modtime = $mes->modified_time;
                                if(!$modtime) {
                                    $modtime = $mes->time;
                                }
                                if(($modtime + ((int)$fbConfig->useredittime) + ((int)$fbConfig->useredittimegrace)) >= CKunenaTools::fbGetInternalTime()) {
                                    $allowEdit = 1;
                                }
                            }
                        }
                    }

                    if ($allowEdit == 1)
                    {
                        if (is_array($attachfile) && $attachfile['error'] != UPLOAD_ERR_NO_FILE) {
                            include KUNENA_PATH_LIB .DS. 'kunena.file.upload.php';
                        }

                        if (is_array($attachimage) && $attachimage['error'] != UPLOAD_ERR_NO_FILE) {
                            include KUNENA_PATH_LIB .DS. 'kunena.image.upload.php';
                        }

                        //$message = trim(kunena_htmlspecialchars(addslashes($message)));
                        $message = trim(addslashes($message));

                        //parse the message for some preliminary bbcode and stripping of HTML
                        //$message = smile::bbencode_first_pass($message);

                        if (count($message1) > 0)
                        {
                        	// Re-check the hold. If post gets edited and review is set to ON for this category

                        	// check if the post must be reviewed by a Moderator prior to showing
                        	// doesn't apply to admin/moderator posts ;-)
                        	$holdPost = 0;

                        	if (!$is_Moderator)
                        	{
                        		$kunena_db->setQuery("SELECT review FROM #__fb_categories WHERE id='{$catid}'");
                        		$kunena_db->query() or trigger_dberror('Unable to load review flag from categories.');
                        		$holdPost = $kunena_db->loadResult();
                        	}

                        	if (!$fbConfig->askemail){
	 	                    	if (empty($email)) {
	 	                        	$email = $mes->email;
	 	                         }
	 	                  	}

	 	                  	if ( $mes->topic_emoticon == $topic_emoticon ) {
								$topic_emoticon = $mes->topic_emoticon;
	 	                  	}

                            if ($anonymous) {
                                // Anonymous post: remove all user information from the post
                                jimport ( 'joomla.user.helper' );
                                if (JUserHelper::getUserId ( $fb_authorname )) {
                                    $fb_authorname = _KUNENA_USERNAME_ANONYMOUS;
                                }
                                $kunena_db->setQuery ( "UPDATE #__fb_messages SET userid='0', name=" . $kunena_db->quote ( $fb_authorname ) . ", email='', ip='', modified_by='0', modified_time='0' ,modified_reason='', subject=" . $kunena_db->quote ( addslashes($subject) ) . ", topic_emoticon='" . (int)$topic_emoticon . "', hold='" . (( int ) $holdPost) . "' WHERE id={$id}" );
                            } else {
                                $kunena_db->setQuery(
                                "UPDATE #__fb_messages SET name=".$kunena_db->quote($fb_authorname).", email=".$kunena_db->quote(addslashes($email))
                                . (($fbConfig->editmarkup) ? " ,modified_by='" . $modified_by
                                . "' ,modified_time='" . $modified_time . "' ,modified_reason=" . $kunena_db->quote($modified_reason) : "") . ", subject=" . $kunena_db->quote(addslashes($subject)) . ", topic_emoticon='" . ((int)$topic_emoticon) .  "', hold='" . ((int)$holdPost) . "' WHERE id={$id}");
                            }
                            $dbr_nameset = $kunena_db->query();
                            $kunena_db->setQuery("UPDATE #__fb_messages_text SET message=".$kunena_db->quote($message)." WHERE mesid='{$id}'");

                            if ($kunena_db->query() && $dbr_nameset)
                            {
                                //Update the attachments table if an image has been attached
                                if (!empty($imageLocation) && file_exists($imageLocation))
                                {
                                    $imageLocation = addslashes($imageLocation);
                                    $kunena_db->setQuery("INSERT INTO #__fb_attachments (mesid, filelocation) VALUES ('$id',".$kunena_db->quote($imageLocation).")");

                                    if (!$kunena_db->query()) {
                                        echo "<script> alert('Storing image failed: " . $kunena_db->getErrorMsg() . "'); </script>\n";
                                    }
                                }

                                //Update the attachments table if an file has been attached
                                if (!empty($fileLocation) && file_exists($fileLocation))
                                {
                                    $fileLocation = addslashes($fileLocation);
                                    $kunena_db->setQuery("INSERT INTO #__fb_attachments (mesid, filelocation) VALUES ('$id',".$kunena_db->quote($fileLocation).")");

                                    if (!$kunena_db->query()) {
                                        echo "<script> alert('Storing file failed: " . $kunena_db->getErrorMsg() . "'); </script>\n";
                                    }
                                }

                                echo '<br /><br /><div align="center">' . _POST_SUCCESS_EDIT . "</div><br />";
                                echo CKunenaLink::GetLatestPostAutoRedirectHTML($fbConfig, $id, $fbConfig->messages_per_page, $catid);
                            }
                            else {
                                echo _POST_ERROR_MESSAGE_OCCURED;
                            }
                        }
                        else {
                            echo _POST_INVALID;
                        }
                    }
                    else {
			$app->redirect(htmlspecialchars_decode(JRoute::_(KUNENA_LIVEURLREL)), _POST_NOT_MODERATOR);
                    }
                }
                else if ($do == "delete")
                {
                    if (!$is_Moderator) {
			$app->redirect(htmlspecialchars_decode(JRoute::_(KUNENA_LIVEURLREL)), _POST_NOT_MODERATOR);
                    }

                    $id = (int)$id;
                    $kunena_db->setQuery("SELECT * FROM #__fb_messages WHERE id='{$id}'");
                    $message = $kunena_db->loadObjectList();
                    	check_dberror("Unable to load messages.");

                    foreach ($message as $mes)
                    {
                        ?>

                        <form action = "<?php echo JRoute::_(KUNENA_LIVEURLREL."&amp;catid=$catid&amp;func=post"); ?>" method = "post" name = "myform">
                            <input type = "hidden" name = "do" value = "deletepostnow"/>

                            <input type = "hidden" name = "id" value = "<?php echo $mes->id;?>"/> <?php echo _POST_ABOUT_TO_DELETE; ?>: <strong><?php echo stripslashes(kunena_htmlspecialchars($mes->subject)); ?></strong>.

    <br/>

    <br/> <?php echo _POST_ABOUT_DELETE; ?><br/>

    <br/>

    <input type = "checkbox" checked name = "delAttachments" value = "delAtt"/> <?php echo _POST_DELETE_ATT; ?>

    <br/>

    <br/>

    <a href = "javascript:document.myform.submit();"><?php echo _GEN_CONTINUE; ?></a> | <a href = "<?php echo JRoute::_(KUNENA_LIVEURLREL."&amp;func=view&amp;catid=$catid;&amp;id=$id");?>"><?php echo _GEN_CANCEL; ?></a>
                        </form>

            <?php
                    }
                }
                else if ($do == "deletepostnow")
                {
                    if (!$is_Moderator) {
			$app->redirect(htmlspecialchars_decode(JRoute::_(KUNENA_LIVEURLREL)), _POST_NOT_MODERATOR);
                    }

                    $id = JRequest::getInt('id', 0);
                    $dellattach = JRequest::getVar('delAttachments', '') == 'delAtt' ? 1 : 0;
                    $thread = fb_delete_post($kunena_db, $id, $dellattach);

                    CKunenaTools::reCountBoards();

                    switch ($thread)
                    {
                        case -1:
                            echo _POST_ERROR_TOPIC . '<br />';

                            echo _KUNENA_POST_DEL_ERR_CHILD;
                            break;

                        case -2:
                            echo _POST_ERROR_TOPIC . '<br />';

                            echo _KUNENA_POST_DEL_ERR_MSG;
                            break;

                        case -3:
                            echo _POST_ERROR_TOPIC . '<br />';

                            $tmpstr = _KUNENA_POST_DEL_ERR_TXT;
                            $tmpstr = str_replace('%id%', $id, $tmpstr);
                            echo $tmpstr;
                            break;

                        case -4:
                            echo _POST_ERROR_TOPIC . '<br />';

                            echo _KUNENA_POST_DEL_ERR_USR;
                            break;

                        case -5:
                            echo _POST_ERROR_TOPIC . '<br />';

                            echo _KUNENA_POST_DEL_ERR_FILE;
                            break;

                        default:
                            echo '<br /><br /><div align="center">' . _POST_SUCCESS_DELETE . "</div><br />";

                            break;
                    }
                    echo CKunenaLink::GetLatestCategoryAutoRedirectHTML($catid);

                } //fi $do==deletepostnow
                else if ($do == "move")
                {
                    if (!$is_Moderator) {
			$app->redirect(htmlspecialchars_decode(JRoute::_(KUNENA_LIVEURLREL)), _POST_NOT_MODERATOR);
                    }
                    // get topic subject:
                    $kunena_db->setQuery("SELECT subject, id FROM #__fb_messages WHERE id='{$id}'");
                    $topicSubject = $kunena_db->loadResult();
                    	check_dberror("Unable to load messages.");

					$options = array ();
					$catlist = CKunenaTools::KSelectList ( 'catid', $options, ' size="15" class="fb_move_selectbox"' );
            ?>
                    <form action = "<?php echo JRoute::_(KUNENA_LIVEURLREL."&amp;func=post"); ?>" method = "post" name = "myform">
                        <input type = "hidden" name = "do" value = "domovepost"/>

                        <input type = "hidden" name = "id" value = "<?php echo $id;?>"/>

                        <p>
<?php echo _GEN_TOPIC; ?>: <strong><?php echo kunena_htmlspecialchars(stripslashes($topicSubject)); ?></strong>

    <br/>

    <br/> <?php echo _POST_MOVE_TOPIC; ?>:

    <br/>

        <?php echo $catlist;
        ?>

    <br/>

    <input type = "checkbox" checked name = "leaveGhost" value = "1"/> <?php echo _POST_MOVE_GHOST; ?>

    <br/>

    <input type = "submit" class = "button" value = "<?php echo _GEN_MOVE;?>"/>
                    </form>

            <?php
                }
                else if ($do == "domovepost")
                {
                    $catid = (int)$catid;
                    $id = (int)$id;
                    $bool_leaveGhost = JRequest::getInt('leaveGhost', 0);
                    //get the some details from the original post for later
                    $kunena_db->setQuery("SELECT id, subject, catid, time AS timestamp FROM #__fb_messages WHERE id='{$id}'");
                    $oldRecord = $kunena_db->loadObjectList();
                    	check_dberror("Unable to load messages.");

                    $newCatObj = new jbCategory($kunena_db, $oldRecord[0]->catid);
		    if (!fb_has_moderator_permission($kunena_db, $newCatObj, $kunena_my->id, $is_admin)) {
			$app->redirect(htmlspecialchars_decode(JRoute::_(KUNENA_LIVEURLREL)), _POST_NOT_MODERATOR);
                    }

                    $newSubject = _MOVED_TOPIC . " " . $oldRecord[0]->subject;

                    $kunena_db->setQuery("SELECT MAX(time) AS timestamp FROM #__fb_messages WHERE thread='{$id}'");
                    $lastTimestamp = $kunena_db->loadResult();
                    	check_dberror("Unable to load last timestamp.");

                    if ($lastTimestamp == "") {
                        $lastTimestamp = $oldRecord[0]->timestamp;
                    }

                    //perform the actual move
                    //Move topic post first
                    $kunena_db->setQuery("UPDATE #__fb_messages SET `catid`='$catid' WHERE `id`='$id'");
                    $kunena_db->query() or trigger_dberror('Unable to move thread.');

                    $kunena_db->setQuery("UPDATE #__fb_messages set `catid`='$catid' WHERE `thread`='$id'");
                    $kunena_db->query() or trigger_dberror('Unable to move thread.');

                    // insert 'moved topic' notification in old forum if needed
                    if ($bool_leaveGhost)
                    {
                    	$kunena_db->setQuery("INSERT INTO #__fb_messages (`parent`, `subject`, `time`, `catid`, `moved`, `userid`, `name`) VALUES ('0',".$kunena_db->quote($newSubject).",'$lastTimestamp','{$oldRecord[0]->catid}','1', '{$kunena_my->id}', ".$kunena_db->quote(trim(addslashes($my_name))).")");
                    	$kunena_db->query() or trigger_dberror('Unable to insert ghost message.');

                    	//determine the new location for link composition
                    	$newId = $kunena_db->insertid();

                    	$newURL = "catid=" . $catid . "&id=" . $id;
                    	$kunena_db->setQuery("INSERT INTO #__fb_messages_text (`mesid`, `message`) VALUES ('$newId', ".$kunena_db->quote($newURL).")");
                    	$kunena_db->query() or trigger_dberror('Unable to insert ghost message.');

                    	//and update the thread id on the 'moved' post for the right ordering when viewing the forum..
                    	$kunena_db->setQuery("UPDATE #__fb_messages SET `thread`='$newId' WHERE `id`='$newId'");
                    	$kunena_db->query() or trigger_dberror('Unable to move thread.');
                    }
                    //move succeeded
                    CKunenaTools::reCountBoards();

                    echo '<br /><br /><div align="center">' . _POST_SUCCESS_MOVE . "</div><br />";
                    echo CKunenaLink::GetLatestPostAutoRedirectHTML($fbConfig, $id, $fbConfig->messages_per_page, $catid);
                }
                //begin merge function
                else if ($do == "merge")
                {
                    if (!$is_Moderator)
                    {
			$app->redirect(htmlspecialchars_decode(JRoute::_(KUNENA_LIVEURLREL)), _POST_NOT_MODERATOR);
                    }

                    $catid = (int)$catid;
                    $id = (int)$id;
                    //get list of available threads in same forum
                    $kunena_db->setQuery("SELECT id, subject FROM #__fb_messages WHERE parent='0' AND catid='{$catid}' AND id!='{$id}'");
                    //$kunena_db->setQuery("SELECT a.*, b.name AS category" . "\nFROM #__fb_categories AS a" . "\nLEFT JOIN #__fb_categories AS b ON b.id = a.parent" . "\nWHERE a.parent != '0'" . "\nORDER BY parent, ordering");
                    $threadlist = $kunena_db->loadObjectList();
                    	check_dberror("Unable to load categories.");
                    // get topic subject:
                    $kunena_db->setQuery("SELECT subject, id FROM #__fb_messages WHERE id='{$id}'");
                    $topicSubject = $kunena_db->loadResult();
                    	check_dberror("Unable to load messages.");
            ?>

                    <form action = "<?php echo JRoute::_(KUNENA_LIVEURLREL."&func=post"); ?>" method = "post" name = "myform">
                        <input type = "hidden" name = "do" value = "domergepost"/>

                        <input type = "hidden" name = "id" value = "<?php echo $id;?>"/>
			   <input type = "hidden" name = "catid" value = "<?php echo $catid;?>"/>

                        <p>
<?php echo _GEN_TOPIC; ?>: <strong><?php echo kunena_htmlspecialchars(stripslashes($topicSubject)); ?></strong>

    <br/>
			<span title="<?php echo _POST_MERGE_TITLE; ?>"><input type = "radio" name = "how" value = "0" CHECKED ><?php echo _POST_MERGE; ?></span>

            <span title="<?php echo _POST_INVERSE_MERGE_TITLE; ?>"><input type = "radio" name = "how" value = "1" ><?php echo _POST_INVERSE_MERGE; ?></span>

    <br/>

    <br/> <?php echo _POST_MERGE_TOPIC; ?>:

    <br/>

    <select name = "threadid" size = "15" class = "fb_move_selectbox">
        <?php
                    foreach ($threadlist as $thread)
                    {
                        echo "<OPTION value=\"".$thread->id."\" >". stripslashes($thread->subject). "</OPTION>";
                    }
        ?>
    </select>

    <br/>

    <input type = "checkbox" checked name = "leaveGhost" value = "1"/> <?php echo _POST_MERGE_GHOST; ?>

    <br/>

    <input type = "submit" class = "button" value = "<?php echo _GEN_MERGE;?>"/>
                    </form>

            <?php
                }
                else if ($do == "domergepost")
                {
                    if (!$is_Moderator) {
			$app->redirect(htmlspecialchars_decode(JRoute::_(KUNENA_LIVEURLREL)), _POST_NOT_MODERATOR);
                    }

                    $catid = (int)$catid;
                    $id = (int)$id;
                    $target = JRequest::getInt('threadid', 0);
                    $how = JRequest::getInt('how', 0);
                    $bool_leaveGhost = JRequest::getInt('leaveGhost', 0);


                    switch ($how)
                    {
                    case '0' :  //attach first post in source to first post in target - merge (default)
                    default  :
                            $attachid=$target;
                            $targetid=$target;
                            $sourceid=$id;
                            break;
                    case '1' :  //attach first post in target to first post in source - inverse merge
                            $attachid=$id;
                            $sourceid=$target;
                            $targetid=$id;
                            break;
                    }

                    //get the some details from the original post for later
                    $kunena_db->setQuery("SELECT subject, catid, ordering, time AS timestamp FROM #__fb_messages WHERE id='{$sourceid}'");
                    $oldRecord = $kunena_db->loadObjectList();
                    	check_dberror("Unable to load messages.");
                    $newSubject = _MOVED_TOPIC . " " . $oldRecord[0]->subject;
                    $kunena_db->setQuery("SELECT MAX(time) AS timestamp FROM #__fb_messages WHERE thread='{$sourceid}'");
                    $lastTimestamp = $kunena_db->loadResult();
                    	check_dberror("Unable to load messages.");
                    $kunena_db->setQuery("SELECT MAX(ordering) AS timestamp FROM #__fb_messages WHERE thread='{$targetid}'");
                    $maxordering = $kunena_db->loadResult();
                    	check_dberror("Unable to get max(ordering) from messages.");

                    if ($lastTimestamp == "")
                    {
                        $lastTimestamp = $oldRecord[0]->timestamp;
                    }

                    //perform the actual merge
                    //see if you can attach
                    $kunena_db->setQuery("UPDATE #__fb_messages set `parent`='$attachid' WHERE `id`='$sourceid'");
                    if ($kunena_db->query())
                    { //succeeded; start moving posts
                        //make sure default merged threads get sorted correcty
                        $kunena_db->setQuery("UPDATE #__fb_messages set ordering='$maxordering' WHERE thread='$sourceid'");
                        $kunena_db->query();

                        //Now move first post
                        $kunena_db->setQuery("UPDATE #__fb_messages SET `thread`='$targetid' WHERE `id`='$sourceid'");
                        if ($kunena_db->query())
                        {
                            //Move the rest of the messages
                            $kunena_db->setQuery("UPDATE #__fb_messages set `thread`='$targetid' WHERE `thread`='$sourceid'");
                            $kunena_db->query();

                            // insert 'moved topic' notification in old forum if needed
                            if ($bool_leaveGhost)
                            {
                                $kunena_db->setQuery("INSERT INTO #__fb_messages (`parent`, `subject`, `time`, `catid`, `moved`) VALUES ('0',".$kunena_db->quote($newSubject).",'" . $lastTimestamp . "','" . $oldRecord[0]->catid . "','1')");

                                if ($kunena_db->query())
                                {
                                    //determine the new location for link composition
                                    $newId = $kunena_db->insertid();
                                    $newURL = "catid=" . $catid . "&id=" . $sourceid;
                                    $kunena_db->setQuery("INSERT INTO #__fb_messages_text (`mesid`, `message`) VALUES ('$newId', ".$kunena_db->quote($newURL).")");

                                    if (!$kunena_db->query())
                                    {
                                        $kunena_db->stderr(true);
                                    }

                                    //and update the thread id on the 'moved' post for the right ordering when viewing the forum..
                                    $kunena_db->setQuery("UPDATE #__fb_messages SET `thread`='$newId' WHERE `id`='$newId'");

                                    if (!$kunena_db->query())
                                    {
                                        $kunena_db->stderr(true);
                                    }

                                }
                                else
                                    echo '<p style="text-align:center">' . _POST_GHOST_FAILED . '</p>';
                            }

                            //merge succeeded
                            CKunenaTools::reCountBoards();

                            echo '<br /><br /><div align="center">' . _POST_SUCCESS_MERGE . "</div><br />";
                            echo CKunenaLink::GetLatestPostAutoRedirectHTML($fbConfig, $targetid, $fbConfig->messages_per_page, $catid);
                        }
                        else
                        {
                            echo "Severe database error. Update your database manually so the replies to the topic are matched to the new forum as well";
                            //this is severe.. takes a lot of coding to programatically correct it. Won't do that.
                            //chances of this happening are very slim. Disclaimer: this is software as-is *lol*;
                            //go read the GPL and the header of this file..
                        }
                    }
                    else
                    {
                        echo '<br /><br /><div align="center">' . _POST_TOPIC_NOT_MERGED . "</div><br />";
                        echo CKunenaLink::GetLatestPostAutoRedirectHTML($fbConfig, $id, $fbConfig->messages_per_page);
                    }

		        }
// end merge function
// begin split function
                else if ($do == "split")
                {
                    if (!$is_Moderator) {
			$app->redirect(htmlspecialchars_decode(JRoute::_(KUNENA_LIVEURLREL)), _POST_NOT_MODERATOR);
                    }

                    $error = JRequest::getInt('error', 0);
                    $id = (int)$id;
                    $catid = (int)$catid;

					// TODO: Enable split when it's fixed
                    $app->redirect(CKunenaLink::GetLatestPageAutoRedirectURL($fbConfig, $id, $fbConfig->messages_per_page, $catid), 'Split has been disabled');

                    //get list of posts in thread
                    $kunena_db->setQuery("SELECT * FROM #__fb_messages AS a "
                    ." LEFT JOIN #__fb_messages_text AS b ON a.id=b.mesid WHERE (a.thread='{$id}' OR a.id='{$id}') AND a.hold='0' AND a.catid='{$catid}' ORDER BY a.parent ASC, a.ordering, a.time");
                    $postlist = $kunena_db->loadObjectList();
                    	check_dberror("Unable to load messages.");
                    // get topic id:
                    $kunena_db->setQuery("SELECT id FROM #__fb_messages WHERE id='{$id}' AND parent='0'");
                    $id = (int)$kunena_db->loadResult();
                    	check_dberror("Unable to load messages.");

            ?>

                    <form action = "<?php echo JRoute::_(KUNENA_LIVEURLREL."&func=post"); ?>" method = "post" name = "myform">
                        <input type = "hidden" name = "do" value = "dosplit"/>

                        <input type = "hidden" name = "id" value = "<?php echo $id;?>"/>
			   <input type = "hidden" name = "catid" value = "<?php echo $catid;?>"/>

<?php
                    if (!$error) $error = _POST_SPLIT_HINT;
                    echo $error;
?>
                        <p>

	     <span title="<?php echo _POST_LINK_ORPHANS_TOPIC_TITLE; ?>"><input type = "radio" name = "how" value = "0" CHECKED ><?php echo _POST_LINK_ORPHANS_TOPIC; ?></span>
            <span title="<?php echo _POST_LINK_ORPHANS_PREVPOST_TITLE; ?>"><input type = "radio" name = "how" value = "1" ><?php echo _POST_LINK_ORPHANS_PREVPOST; ?></span>
    <br/><br/>

    <input type = "submit" class = "button" value = "<?php echo _GEN_DOSPLIT; ?>"/>

        <table border = "0" cellspacing = "1" cellpadding = "3" width = "100%" class = "fb_review_table">
            <tr>
                <td class = "fb_review_header" width = "26px" align = "center">
                    <strong><?php echo _GEN_SPLIT; ?></strong>
                </td>
                <td class = "fb_review_header" width = "34px" align = "center">
                    <strong><?php echo _GEN_TOPIC; ?></strong>
                </td>
                <td class = "fb_review_header" width = "15%" align = "center">
                    <strong><?php echo _GEN_AUTHOR; ?></strong>
                </td>
                <td class = "fb_review_header" width = "20%" align = "center">
                    <strong><?php echo _GEN_SUBJECT; ?></strong>
                </td>
                <td class = "fb_review_header" align = "center">
                    <strong><?php echo _GEN_MESSAGE; ?></strong>
                </td>
            </tr>

            <?php
                    $k = 0;
                    $smileyList = smile::getEmoticons(1);

                    foreach ($postlist as $mes)
                    {
                        $k = 1 - $k;
                        $mes->name = kunena_htmlspecialchars($mes->name);
                        $mes->subject = kunena_htmlspecialchars($mes->subject);
                        $mes->message = smile::smileReplace($mes->message, 1, $fbConfig->disemoticons, $smileyList);
            ?>

                <tr>
                    <td class = "fb_review_body<?php echo $k;?>" valign = "top">
<?php
                        if ($mes->id==$id)
                        {

                        }
                        else
                        {
?>
		<div align="center"><input type="checkbox" name="tosplit[]" value="<?php echo $mes->id;?>"></div>
<?php
                        }
?>
                    </td>

                    <td class = "fb_review_body<?php echo $k;?>" valign = "top">
<?php
                        if ($mes->id==$id)
                        {

                        }
                        else
                        {
?>
		<div align="center"><input type = "radio" name = "to_topic" value = "<?php echo $mes->id;?>"></div>
<?php
     }
?>
                    </td>

                    <td class = "fb_review_body<?php echo $k;?>" valign = "top"><?php echo stripslashes($mes->name); ?>
                    </td>
                    <td class = "fb_review_body<?php echo $k;?>" valign = "top"><?php echo stripslashes($mes->subject); ?>
                    </td>

                    <td class = "fb_review_body<?php echo $k;?>">
                        <?php
                        $fb_message_txt = stripslashes(nl2br($mes->message));
                        $fb_message_txt = str_replace("</P><br />", "</P>", $fb_message_txt);
                        //Long Words Wrap:
                        $fb_message_txt = smile::htmlwrap($fb_message_txt, $fbConfig->wrap);

                        echo $fb_message_txt;
                        ?>
                    </td>
                </tr>

            <?php
                    }
            ?>
        </table>

    <br/>
    <input type = "submit" class = "button" value = "<?php echo _KUNENA_GO;?>"/>

                    </form>

            <?php
                }
                else if ($do == "dosplit")
                {
                    if (!$is_Moderator)
                    {
			$app->redirect(htmlspecialchars_decode(JRoute::_(KUNENA_LIVEURLREL)), _POST_NOT_MODERATOR);
                    }

                    $catid = (int)$catid;
                    $id = JRequest::getInt('id', 0);
                    $to_split = JRequest::getInt('to_split', 0);
                    $how = JRequest::getInt('how', 0);
                    $new_topic = JRequest::getInt('to_topic', 0);
                    $topic_change = 0;

					// TODO: Enable split when it's fixed
                    $app->redirect(CKunenaLink::GetLatestPageAutoRedirectURL($fbConfig, $id, $fbConfig->messages_per_page, $catid), 'Split has been disabled');

                    if (!$to_split)
                    {
                        if ($new_topic != 0 && $id != $new_topic)
                        {
                            $topic_change = 1;
                            $to_split = array();
                            array_push($to_split, $new_topic);
                        }
                        else
                        {
                            echo '<br /><b> Select at least one post to split.</b></br>';
                            return;
                        }
                    }

                    //store sticky bit from old topic
                    $kunena_db->setQuery("SELECT ordering FROM #__fb_messages WHERE id='{$id}'");
                    $sticky_bit = (int)$kunena_db->loadResult();

                    //enter topic change only sequence
                    if (in_array($id, $to_split) || $topic_change == 1)
                    {
                        echo '<div align="center"><br />Assuming that you want to change topic post.</br></div>';
                        if ($new_topic != 0 && $id != $new_topic)
                        {
                            //select all posts in thread regardless of earlier selection
                            $kunena_db->setQuery("SELECT id FROM #__fb_messages WHERE thread='{$id}'");
                            $to_split = $kunena_db->loadResultArray();

                            $split_string=implode(",",$to_split);

                            //old topic id adopted by new one: the new parent will appear after child unless sorting var added in view.php
                            $kunena_db->setQuery("UPDATE #__fb_messages set parent=$new_topic WHERE id=$id");
                            $kunena_db->query();

                            //assign new thread ids
                            $kunena_db->setQuery("UPDATE #__fb_messages set thread=$new_topic WHERE id IN ($split_string)");
                            $kunena_db->query();

                            //set new topic
                            $kunena_db->setQuery("UPDATE #__fb_messages set parent=0 WHERE id=$new_topic");
                            $kunena_db->query();

                            //copy over hits from old topic
                            $kunena_db->setQuery("SELECT hits FROM #__fb_messages WHERE id='{$id}'");
                            $hits = (int)$kunena_db->loadResult();
                            $kunena_db->setQuery("UPDATE #__fb_messages set hits=$hits WHERE id=$new_topic");
                            $kunena_db->query();


                            $kunena_db->setQuery("UPDATE #__fb_messages set ordering='2' WHERE id=$id");
                            $kunena_db->query();

                            //move new topic to top regardless of viewing preferences and set sticky
                            $kunena_db->setQuery("UPDATE #__fb_messages set ordering='$sticky_bit' WHERE id=$new_topic AND parent=0");
                            $kunena_db->query();

                            echo '<br /><br /><div align="center">' . _POST_SUCCESS_SPLIT_TOPIC_CHANGED . "</div><br />";
                            echo CKunenaLink::GetLatestPostAutoRedirectHTML($fbConfig, $new_topic, $fbConfig->messages_per_page, $catid);

                            return;
                        }
                        else
                        {
                            echo '<br /><br /><div align="center">' . _POST_SPLIT_TOPIC_NOT_CHANGED . "</div><br />";
                            echo CKunenaLink::GetLatestPostAutoRedirectHTML($fbConfig, $id, $fbConfig->messages_per_page);

                            echo '<div align="center"><br />Topic change failed.</br></div>';
                            return;
                        }

                    } //end topic change

                    if (count($to_split) == 1)
                    { //single split post automatically becomes topic
                        if ($to_split[0] != $id)
                        {
                            $new_topic=$to_split[0];
                        }
                        else return;
                    }

                    if (!$new_topic)
                    {
                        echo '<br /><b> Select new topic.</b></br>';
                        return;
                    }

                    if (!in_array($new_topic, $to_split))
                    {
                        array_push($to_split, $new_topic);
                        echo '<div align="center"><br />Selected topic post has been force-added to split group.</br></div>';
                    }

                    $split_string=implode(",",$to_split);

                    //assign new thread ids
                    $kunena_db->setQuery("UPDATE #__fb_messages set thread='$new_topic' WHERE id IN ($split_string)");
                    $kunena_db->query();

                    foreach ($to_split as $split_id)
                    { //assign new parents to topic and orphaned posts
                        $kunena_db->setQuery("SELECT parent FROM #__fb_messages WHERE id='{$split_id}'");
                        $parent = (int)$kunena_db->loadResult();

                        if ($split_id == $new_topic)
                        { //set new topic
                            $linkup = 0;
                        }
                        else if (!in_array($parent, $to_split))
                        { //detected orphan
                            if ($how) $linkup = $new_topic; //orphans adopted by new topic post
                            else
                            { //orphans adopted by lowest neighboring post id
                                $closest = $split_id-1;
                                while (!in_array($closest, $to_split))
                                {
                                    $closest--;
                                }
                                if (in_array($closest, $to_split)) $linkup = $closest;
                                else $linkup = $new_topic;
                            }
                        }
                        else //reset existing parent
                        $linkup=$parent;

                        $kunena_db->setQuery("UPDATE #__fb_messages set parent='$linkup' WHERE id=$split_id");
                        $kunena_db->query();
                    } //end parenting foreach loop


                    //inherit hits from old topic
                    $kunena_db->setQuery("SELECT hits FROM #__fb_messages WHERE id='{$id}'");
                    $hits = (int)$kunena_db->loadResult();
                    $kunena_db->setQuery("UPDATE #__fb_messages set hits=$hits WHERE id=$new_topic");
                    $kunena_db->query();

                    //set the highest sorting for old topic
                    $kunena_db->setQuery("UPDATE #__fb_messages set ordering='2' WHERE id=$id");
                    $kunena_db->query();

                    //copy over sticky bit to new topic
                    $kunena_db->setQuery("UPDATE #__fb_messages set ordering='$sticky_bit' WHERE id=$new_topic AND parent=0");
                    $kunena_db->query();

                    //split succeeded
                    CKunenaTools::reCountBoards();

                    echo '<br /><br /><div align="center">' . _POST_SUCCESS_SPLIT . "</div><br />";
                    echo CKunenaLink::GetLatestPostAutoRedirectHTML($fbConfig, $new_topic, $fbConfig->messages_per_page, $catid);
		        }
// end split function
                else if ($do == "subscribe")
                {
                    $catid = (int)$catid;
                    $id = (int)$id;
                    $success_msg = _POST_NO_SUBSCRIBED_TOPIC;
                    $kunena_db->setQuery("SELECT thread, catid from #__fb_messages WHERE id='{$id}'");
                    if ($id && $kunena_my->id && $kunena_db->query())
                    {
						$row = $kunena_db->loadObject();

						//check for permission
						if (!$is_Moderator) {
							if ($fbSession->allowed != "na")
								$allow_forum = explode(',', $fbSession->allowed);
							else
								$allow_forum = array ();

								$obj_fb_cat = new jbCategory($kunena_db, $row->catid);
								if (!fb_has_read_permission($obj_fb_cat, $allow_forum, $aro_group->id, $kunena_acl)) {
									$app->redirect(htmlspecialchars_decode(JRoute::_(KUNENA_LIVEURLREL)), _POST_NOT_MODERATOR);
								return;
							}
						}

                        $thread = $row->thread;
                        $kunena_db->setQuery("INSERT INTO #__fb_subscriptions (thread,userid) VALUES ('$thread','$kunena_my->id')");

                        if (@$kunena_db->query() && $kunena_db->getAffectedRows()==1) {
                            $success_msg = _POST_SUBSCRIBED_TOPIC;
                        }
                    }
                    $app->redirect(CKunenaLink::GetLatestPageAutoRedirectURL($fbConfig, $id, $fbConfig->messages_per_page), $success_msg);
                }
                else if ($do == "unsubscribe")
                {
                    $catid = (int)$catid;
                    $id = (int)$id;
                    $success_msg = _POST_NO_UNSUBSCRIBED_TOPIC;
                    $kunena_db->setQuery("SELECT MAX(thread) AS thread FROM #__fb_messages WHERE id='{$id}'");
                    if ($id && $kunena_my->id && $kunena_db->query())
                    {
                        $thread = $kunena_db->loadResult();
                        $kunena_db->setQuery("DELETE FROM #__fb_subscriptions WHERE thread=$thread AND userid=$kunena_my->id");

                        if ($kunena_db->query() && $kunena_db->getAffectedRows()==1)
                        {
                            $success_msg = _POST_UNSUBSCRIBED_TOPIC;
                        }
                    }
                    $app->redirect(CKunenaLink::GetLatestPageAutoRedirectURL($fbConfig, $id, $fbConfig->messages_per_page), $success_msg);
                }
                else if ($do == "favorite")
                {
                    $catid = (int)$catid;
                    $id = (int)$id;
                    $success_msg = _POST_NO_FAVORITED_TOPIC;
                    $kunena_db->setQuery("SELECT MAX(thread) AS thread FROM #__fb_messages WHERE id='{$id}'");
                    if ($id && $kunena_my->id && $kunena_db->query())
                    {
                        $thread = $kunena_db->loadResult();
                        $kunena_db->setQuery("INSERT INTO #__fb_favorites (thread,userid) VALUES ('$thread','$kunena_my->id')");

                        if (@$kunena_db->query() && $kunena_db->getAffectedRows()==1)
                        {
                             $success_msg = _POST_FAVORITED_TOPIC;
                        }
                    }
                    $app->redirect(CKunenaLink::GetLatestPageAutoRedirectURL($fbConfig, $id, $fbConfig->messages_per_page), $success_msg);
                }
                else if ($do == "unfavorite")
                {
                    $catid = (int)$catid;
                    $id = (int)$id;
                    $success_msg = _POST_NO_UNFAVORITED_TOPIC;
                    $kunena_db->setQuery("SELECT MAX(thread) AS thread FROM #__fb_messages WHERE id='{$id}'");
                    if ($id && $kunena_my->id && $kunena_db->query())
                    {
                        $thread = $kunena_db->loadResult();
                        $kunena_db->setQuery("DELETE FROM #__fb_favorites WHERE thread=$thread AND userid=$kunena_my->id");

                        if ($kunena_db->query() && $kunena_db->getAffectedRows()==1)
                        {
                            $success_msg = _POST_UNFAVORITED_TOPIC;
                        }
                    }
                    $app->redirect(CKunenaLink::GetLatestPageAutoRedirectURL($fbConfig, $id, $fbConfig->messages_per_page), $success_msg);
                }
                else if ($do == "sticky")
                {
                    if (!$is_Moderator) {
			$app->redirect(htmlspecialchars_decode(JRoute::_(KUNENA_LIVEURLREL)), _POST_NOT_MODERATOR);
                    }

                    $id = (int)$id;
                    $success_msg = _POST_STICKY_NOT_SET;
                    $kunena_db->setQuery("update #__fb_messages set ordering=1 where id=$id");
                    if ($id && $kunena_db->query() && $kunena_db->getAffectedRows()==1) {
                        $success_msg = _POST_STICKY_SET;
                    }
                    $app->redirect(CKunenaLink::GetLatestPageAutoRedirectURL($fbConfig, $id, $fbConfig->messages_per_page), $success_msg);
                }
                else if ($do == "unsticky")
                {
                    if (!$is_Moderator) {
			$app->redirect(htmlspecialchars_decode(JRoute::_(KUNENA_LIVEURLREL)), _POST_NOT_MODERATOR);
                    }

                    $id = (int)$id;
                    $success_msg = _POST_STICKY_NOT_UNSET;
                    $kunena_db->setQuery("update #__fb_messages set ordering=0 where id=$id");
                    if ($id && $kunena_db->query() && $kunena_db->getAffectedRows()==1) {
                        $success_msg = _POST_STICKY_UNSET;
                    }
                    $app->redirect(CKunenaLink::GetLatestPageAutoRedirectURL($fbConfig, $id, $fbConfig->messages_per_page), $success_msg);
                }
                else if ($do == "lock")
                {
                    if (!$is_Moderator) {
			$app->redirect(htmlspecialchars_decode(JRoute::_(KUNENA_LIVEURLREL)), _POST_NOT_MODERATOR);
                    }

                    $id = (int)$id;
                    $success_msg = _POST_LOCK_NOT_SET;
                    $kunena_db->setQuery("update #__fb_messages set locked=1 where id=$id");
                    if ($id && $kunena_db->query() && $kunena_db->getAffectedRows()==1) {
                        $success_msg = _POST_LOCK_SET;
                    }
                    $app->redirect(CKunenaLink::GetLatestPageAutoRedirectURL($fbConfig, $id, $fbConfig->messages_per_page), $success_msg);
                }
                else if ($do == "unlock")
                {
                    if (!$is_Moderator) {
			$app->redirect(htmlspecialchars_decode(JRoute::_(KUNENA_LIVEURLREL)), _POST_NOT_MODERATOR);
                    }

                    $id = (int)$id;
                    $success_msg = _POST_LOCK_NOT_UNSET;
                    $kunena_db->setQuery("update #__fb_messages set locked=0 where id=$id");
                    if ($id && $kunena_db->query() && $kunena_db->getAffectedRows()==1) {
                        $success_msg = _POST_LOCK_UNSET;
                    }
                    $app->redirect(CKunenaLink::GetLatestPageAutoRedirectURL($fbConfig, $id, $fbConfig->messages_per_page), $success_msg);
                }
            }
            ?>
        </td>
    </tr>
</table>

<?php
/**
 * Checks if a user has postpermission in given thread
 * @param database object
 * @param int
 * @param int
 * @param boolean
 * @param boolean
 */
function hasPostPermission($kunena_db, $catid, $id, $userid, $pubwrite, $ismod)
{
    $fbConfig =& CKunenaConfig::getInstance();
    $app =& JFactory::getApplication();

    $topicLock = 0;
    if ($id != 0)
    {
        $kunena_db->setQuery("SELECT thread FROM #__fb_messages WHERE id='{$id}'");
        $topicID = $kunena_db->loadResult();
        $lockedWhat = _GEN_TOPIC;

        if ($topicID != 0) //message replied to is not the topic post; check if the topic post itself is locked
        {
            $sql = "SELECT locked FROM #__fb_messages WHERE id='{$topicID}'";
        }
        else {
            $sql = "SELECT locked FROM #__fb_messages WHERE id='{$id}'";
        }

        $kunena_db->setQuery($sql);
        $topicLock = $kunena_db->loadResult();
    }

    if ($topicLock == 0)
    { //topic not locked; check if forum is locked
        $kunena_db->setQuery("SELECT locked FROM #__fb_categories WHERE id='{$catid}'");
        $topicLock = $kunena_db->loadResult();
        $lockedWhat = _GEN_FORUM;
    }

    if (($userid != 0 || $pubwrite) && ($topicLock == 0 || $ismod)) {
        return 1;
    }
    else
    {
        //user is not allowed to write a post
        if ($topicLock)
        {
            echo "<p align=\"center\">$lockedWhat " . _POST_LOCKED . "<br />";
            echo _POST_NO_NEW . "<br /><br /></p>";
        }
        else
        {
        	$app->enqueueMessage(_POST_NO_PUBACCESS1, 'notice');
        	$app->enqueueMessage(_POST_NO_PUBACCESS2, 'notice');

            $app->redirect( CKunenaLink::GetShowLatestURL());
        }

        return 0;
    }
}
/**
 * Function to delete posts
 *
 * @param database object
 * @param int the id if the post to be deleted
 * @param boolean determines if we need to delete attachements as well
 *
 * @return int returns thread id if all went well, -1 to -4 are error numbers
**/
function fb_delete_post(&$kunena_db, $id, $dellattach)
{
    $kunena_db->setQuery("SELECT id, catid, parent, thread, subject, userid FROM #__fb_messages WHERE id='{$id}'");

    if (!$kunena_db->query()) {
        return -2;
    }

    unset($mes);
    $mes = $kunena_db->loadObject();
    $thread = $mes->thread;

    $userid_array = array ();
    if ($mes->parent == 0)
    {
        // this is the forum topic; if removed, all children must be removed as well.
        $children = array ();
        $kunena_db->setQuery("SELECT userid, id, catid FROM #__fb_messages WHERE thread='{$id}' OR id='{$id}'");

        foreach ($kunena_db->loadObjectList() as $line)
        {
            $children[] = $line->id;

            if ($line->userid > 0) {
                $userid_array[] = $line->userid;
            }
        }

        $children = implode(',', $children);
        $userids = implode(',', $userid_array);
    }
    else
    {
        //this is not the forum topic, so delete it and promote the direct children one level up in the hierarchy
        $kunena_db->setQuery('UPDATE #__fb_messages SET parent=\'' . $mes->parent . '\' WHERE parent=\'' . $id . '\'');

        if (!$kunena_db->query()) {
            return -1;
        }

        $children = $id;
        $userids = $mes->userid > 0 ? $mes->userid : '';
    }

    //Delete the post (and it's children when it's the first post)
    $kunena_db->setQuery('DELETE FROM #__fb_messages WHERE id=' . $id . ' OR thread=' . $id);

    if (!$kunena_db->query()) {
        return -2;
    }

    //Delete message text(s)
    $kunena_db->setQuery('DELETE FROM #__fb_messages_text WHERE mesid IN (' . $children . ')');

    if (!$kunena_db->query()) {
        return -3;
    }

    //Update user post stats
    if (count($userid_array) > 0)
    {
        $kunena_db->setQuery('UPDATE #__fb_users SET posts=posts-1 WHERE userid IN (' . $userids . ')');

        if (!$kunena_db->query()) {
            return -4;
        }
    }

    //Delete (possible) ghost post
    $kunena_db->setQuery("SELECT mesid FROM #__fb_messages_text WHERE message='catid={$mes->catid}&amp;id={$id}'");
    $int_ghost_id = $kunena_db->loadResult();

    if ($int_ghost_id > 0)
    {
        $kunena_db->setQuery('DELETE FROM #__fb_messages WHERE id=' . $int_ghost_id);
        $kunena_db->query();
        $kunena_db->setQuery('DELETE FROM #__fb_messages_text WHERE mesid=' . $int_ghost_id);
        $kunena_db->query();
    }

    //Delete attachments
    if ($dellattach)
    {
        $errorcode = 0;
        $kunena_db->setQuery('SELECT filelocation FROM #__fb_attachments WHERE mesid IN (' . $children . ')');
        $fileList = $kunena_db->loadObjectList();
        	check_dberror("Unable to load attachments.");

        if (count($fileList) > 0)
        {
            foreach ($fileList as $fl) {
		if (file_exists($fl->filelocation))
		{
			unlink($fl->filelocation);
		} else {
			$errorcode = -5;
		}
            }

            $kunena_db->setQuery('DELETE FROM #__fb_attachments WHERE mesid IN (' . $children . ')');
            $kunena_db->query();
       	    check_dberror("Unable to delete attachements.");
	    if ($errorcode) return $errorcode;
        }
    }

// Already done outside - see dodelete code above
//    CKunenaTools::reCountBoards();

    return $thread; // all went well :-)
}

function listThreadHistory($id, $fbConfig, $kunena_db)
{
    if ($id != 0)
    {
        //get the parent# for the post on which 'reply' or 'quote' is chosen
        $kunena_db->setQuery("SELECT parent FROM #__fb_messages WHERE id='{$id}'");
        $this_message_parent = $kunena_db->loadResult();
        //Get the thread# for the same post
        $kunena_db->setQuery("SELECT thread FROM #__fb_messages WHERE id='{$id}'");
        $this_message_thread = $kunena_db->loadResult();

        //determine the correct thread# for the entire thread
        if ($this_message_parent == 0) {
            $thread = $id;
        }
        else {
            $thread = $this_message_thread;
        }

        //get all the messages for this thread
        $kunena_db->setQuery("SELECT * FROM #__fb_messages AS m LEFT JOIN #__fb_messages_text AS t ON m.id=t.mesid WHERE (thread='{$thread}' OR id='{$thread}') AND hold='0' ORDER BY time DESC LIMIT " . $fbConfig->historylimit);
        $messages = $kunena_db->loadObjectList();
        	check_dberror("Unable to load messages.");
        //and the subject of the first thread (for reference)
        $kunena_db->setQuery("SELECT subject FROM #__fb_messages WHERE id='{$thread}' and parent='0'");
        $this_message_subject = $kunena_db->loadResult();
        	check_dberror("Unable to load messages.");
        echo "<b>" . _POST_TOPIC_HISTORY . ":</b> " . kunena_htmlspecialchars(stripslashes($this_message_subject)) . " <br />" . _POST_TOPIC_HISTORY_MAX . " $fbConfig->historylimit " . _POST_TOPIC_HISTORY_LAST . "<br />";
?>

        <table border = "0" cellspacing = "1" cellpadding = "3" width = "100%" class = "fb_review_table">
            <tr>
                <td class = "fb_review_header" width = "20%" align = "center">
                    <strong><?php echo _GEN_AUTHOR; ?></strong>
                </td>

                <td class = "fb_review_header" align = "center">
                    <strong><?php echo _GEN_MESSAGE; ?></strong>
                </td>
            </tr>

            <?php
            $k = 0;
            $smileyList = smile::getEmoticons(1);

            foreach ($messages as $mes)
            {
                $k = 1 - $k;
                $mes->name = kunena_htmlspecialchars($mes->name);
                $mes->email = kunena_htmlspecialchars($mes->email);
                $mes->subject = kunena_htmlspecialchars($mes->subject);


                $fb_message_txt = stripslashes(($mes->message));
                $fb_message_txt = smile::smileReplace($fb_message_txt, 1, $fbConfig->disemoticons, $smileyList);
                $fb_message_txt = nl2br($fb_message_txt);
                $fb_message_txt = str_replace("__FBTAB__", "\t", $fb_message_txt);

            ?>

                <tr>
                    <td class = "fb_review_body<?php echo $k;?>" valign = "top">
                        <?php echo stripslashes($mes->name); ?>
                    </td>

                    <td class = "fb_review_body<?php echo $k;?>">
                    	 <div class="msgtext">
                        <?php
                        $fb_message_txt = str_replace("</P><br />", "</P>", $fb_message_txt);
                        //Long Words Wrap:
                        $fb_message_txt = smile::htmlwrap($fb_message_txt, $fbConfig->wrap);

						$fb_message_txt = CKunenaTools::prepareContent($fb_message_txt);

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
<div class="<?php echo $boardclass; ?>_bt_cvr1">
<div class="<?php echo $boardclass; ?>_bt_cvr2">
<div class="<?php echo $boardclass; ?>_bt_cvr3">
<div class="<?php echo $boardclass; ?>_bt_cvr4">
<div class="<?php echo $boardclass; ?>_bt_cvr5">
<table class = "fb_blocktable" id = "fb_bottomarea" border = "0" cellspacing = "0" cellpadding = "0" width="100%">
    <thead>
        <tr>
            <th class = "th-right">
                <?php
                //(JJ) FINISH: CAT LIST BOTTOM
                if ($fbConfig->enableforumjump) {
                    require_once (KUNENA_PATH_LIB .DS. 'kunena.forumjump.php');
                }
                ?>
            </th>
        </tr>
    </thead>
    <tbody><tr><td></td></tr></tbody>
</table>
</div>
</div>
</div>
</div>
</div>
<!-- Finish: Forum Jump -->
