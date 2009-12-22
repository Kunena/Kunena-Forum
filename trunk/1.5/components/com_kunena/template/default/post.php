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
$kunena_config =& CKunenaConfig::getInstance();
$fbSession =& CKunenaSession::getInstance();

global $boardclass;
global $kunena_is_moderator, $kunena_systime, $kunena_is_admin;
global $kunena_this_cat, $imageLocation, $fileLocation, $board_title;

$catid = JRequest::getInt('catid', 0);
$id    = JRequest::getInt('id', 0);
$do    = JRequest::getCmd('do', '');

$kunena_my  = &JFactory::getUser();
$kunena_acl = &JFactory::getACL();
$kunena_db  = &JFactory::getDBO();

$action 		= JRequest::getCmd('action', '');
$message   		= JRequest::getVar('message', '', 'REQUEST', 'string', JREQUEST_ALLOWRAW);
$resubject 		= JRequest::getVar('resubject', '', 'REQUEST', 'string');
$attachfile  	= JRequest::getVar('attachfile', NULL, 'FILES', 'array');
$attachimage 	= JRequest::getVar('attachimage', NULL, 'FILES', 'array');
$fb_authorname 	= JRequest::getVar('fb_authorname', '');
$email 			= JRequest::getVar('email', '');
$parentid 		= JRequest::getInt('parentid', 0);
$subject 		= JRequest::getVar('subject', '');
$contentURL 	= JRequest::getVar('contentURL', '');
$subscribeMe 	= JRequest::getVar('subscribeMe', '');

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
$pubwrite = (int)$kunena_config->pubwrite;
//ip for floodprotection, post logging, subscriptions, etcetera
$ip = $_SERVER["REMOTE_ADDR"];

//reset variables used
// ERROR: mixed global $editmode
global $editmode;
$editmode = 0;

// Begin captcha
if ($kunena_config->captcha == 1 && $kunena_my->id < 1) {
    $number = $_POST['txtNumber'];

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

//flood protection
$kunena_config->floodprotection = (int)$kunena_config->floodprotection;

if ($kunena_config->floodprotection != 0)
{
    $kunena_db->setQuery("SELECT MAX(time) FROM #__fb_messages WHERE ip='{$ip}'");
    $kunena_db->query() or trigger_dberror("Unable to load max time for current request from IP: $ip");
    $lastPostTime = $kunena_db->loadResult();
}

if (($kunena_config->floodprotection != 0 && ((($lastPostTime + $kunena_config->floodprotection) < $kunena_systime) || $do == "edit" || $kunena_is_admin)) || $kunena_config->floodprotection == 0)
{
    //Let's find out who we're dealing with if a registered user wants to make a post
    if ($kunena_my->id)
    {
        $my_name = $kunena_config->username ? $kunena_my->username : $kunena_my->name;
        $my_email = $kunena_my->email;
        $registeredUser = 1;
	if ($kunena_is_moderator) {
		if (!empty($fb_authorname)) $my_name = $fb_authorname;
		if(isset($email) && !empty($email)) $my_email = $email;
	}
    } else {
        $my_name = $fb_authorname;
	$my_email = (isset($email) && !empty($email))? $email:'';
	$registeredUser = 0;
    }
}
else
{
    echo _POST_TOPIC_FLOOD1;
    echo $kunena_config->floodprotection . " " . _POST_TOPIC_FLOOD2 . "<br />";
    echo _POST_TOPIC_FLOOD3;
    return;
}

//Now find out the forumname to which the user wants to post (for reference only)
$kunena_db->setQuery("SELECT * FROM #__fb_categories WHERE id='{$catid}'");
$kunena_db->query() or trigger_dberror('Unable to load category.');

$objCatInfo = $kunena_db->loadObject();
$catName = $objCatInfo->name;
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

            if ($action == "post" && (hasPostPermission($kunena_db, $catid, $parentid, $kunena_my->id, $kunena_config->pubwrite, $kunena_is_moderator)))
            {
            ?>

                <table border = "0" cellspacing = "1" cellpadding = "3" width = "70%" align = "center" class = "contentpane">
                    <tr>
                        <td>
                            <?php
                            $parent = (int)$parentid;

                            if (empty($my_name)) {
                                echo _POST_FORGOT_NAME;
                            }
                            else if ($kunena_config->askemail && empty($my_email)) {
                                echo _POST_FORGOT_EMAIL;
                            }
                            else if (empty($subject)) {
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

                                $fb_authorname = trim(addslashes($my_name));
                                $subject = trim(addslashes($subject));
                                $message = trim(addslashes($message));

                                if ($contentURL != "empty") {
                                    $message = $contentURL . '\n\n' . $message;
                                }

                                //--
                                $email = trim(addslashes($my_email));
                                $topic_emoticon = (int)$topic_emoticon;
                                $topic_emoticon = ($topic_emoticon < 0 || $topic_emoticon > 7) ? 0 : $topic_emoticon;
                                $posttime = CKunenaTools::fbGetInternalTime();
                                //check if the post must be reviewed by a Moderator prior to showing
                                //doesn't apply to admin/moderator posts ;-)
                                $holdPost = 0;

                                if (!$kunena_is_moderator)
                                {
                                    $kunena_db->setQuery("SELECT review FROM #__fb_categories WHERE id='{$catid}'");
                                    $kunena_db->query() or trigger_dberror('Unable to load review flag from categories.');
                                    $holdPost = $kunena_db->loadResult();
                                }

                                //
                                // Final chance to check whether or not to proceed
                                // DO NOT PROCEED if there is an exact copy of the message already in the db
                                //
                                $duplicatetimewindow = $posttime - $kunena_config->fbsessiontimeout;
                                $kunena_db->setQuery("SELECT m.id FROM #__fb_messages AS m JOIN #__fb_messages_text AS t ON m.id=t.mesid WHERE m.userid='{$kunena_my->id}' AND m.name='{$fb_authorname}' AND m.email='{$email}' AND m.subject='{$subject}' AND m.ip='{$ip}' AND t.message='{$message}' AND m.time>='{$duplicatetimewindow}'");
                                $existingPost = $kunena_db->loadObject();
                                	check_dberror('Unable to load post.');
                                if ($existingPost !== null) $pid = $existingPost->id;

                                if (!isset($pid))
                                {
                                    $kunena_db->setQuery("INSERT INTO #__fb_messages
                                    						(parent,thread,catid,name,userid,email,subject,time,ip,topic_emoticon,hold)
                                    						VALUES('$parent','$thread','$catid',".$kunena_db->quote($fb_authorname).",'{$kunena_my->id}',".$kunena_db->quote($email).",".$kunena_db->quote($subject).",'$posttime','$ip','$topic_emoticon','$holdPost')");

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
                                            if ($kunena_config->pm_component == 'jomsocial' || $kunena_config->fb_profile == 'jomsocial' || $kunena_config->avatar_src == 'jomsocial')
                                            {
												include_once(KUNENA_ROOT_PATH .DS. 'components/com_community/libraries/userpoints.php');

												CuserPoints::assignPoint('com_kunena.thread.new');

												// Check for permisions of the current category - activity only if public
												if ($kunena_this_cat->getPubAccess() == 0)
												{
													//activity stream  - new post
													$JSPostLink = CKunenaLink::GetThreadPageURL($kunena_config, 'view', $catid, $pid, 1);

													$kunena_emoticons = smile::getEmoticons(1);
													$content = stripslashes($message);
													$content = smile::smileReplace($content, 0, $kunena_config->disemoticons, $kunena_emoticons);
													$content = nl2br($content);

													$act = new stdClass();
													$act->cmd    = 'wall.write';
													$act->actor  = $kunena_my->id;
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
										else
										{
                                            // if JomScoial integration is active integrate user points and activity stream
                                            if ($kunena_config->pm_component == 'jomsocial' || $kunena_config->fb_profile == 'jomsocial' || $kunena_config->avatar_src == 'jomsocial')
                                            {
                                            	include_once(KUNENA_ROOT_PATH .DS. 'components/com_community/libraries/userpoints.php');

												CuserPoints::assignPoint('com_kunena.thread.reply');

												// Check for permisions of the current category - activity only if public
												if ($kunena_this_cat->getPubAccess() == 0)
												{
													//activity stream - reply post
													$JSPostLink = CKunenaLink::GetThreadPageURL($kunena_config, 'view', $catid, $thread, 1);

													$content = stripslashes($message);
													$content = smile::smileReplace($content, 0, $kunena_config->disemoticons, $kunena_emoticons);
													$content = nl2br($content);

													$act = new stdClass();
													$act->cmd    = 'wall.write';
													$act->actor  = $kunena_my->id;
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
										// End Modify for activities stream

                                        //update the user posts count
                                        if ($kunena_my->id)
                                        {
                                            $kunena_db->setQuery("UPDATE #__fb_users SET posts=posts+1 WHERE userid={$kunena_my->id}");
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

                                        $kunena_db->setQuery("SELECT COUNT(*) AS totalmessages FROM #__fb_messages WHERE thread='{$querythread}'");
                                        $result = $kunena_db->loadObject();
                                        	check_dberror("Unable to load messages.");
                                        $threadPages = ceil($result->totalmessages / $kunena_config->messages_per_page);
                                        //construct a useable URL (for plaintext - so no &amp; encoding!)
                                        jimport('joomla.environment.uri');
                                        $uri =& JURI::getInstance(JURI::base());
                                        $LastPostUrl = $uri->toString(array('scheme', 'host', 'port')) . str_replace('&amp;', '&', CKunenaLink::GetThreadPageURL($kunena_config, 'view', $catid, $querythread, $threadPages, $kunena_config->messages_per_page, $pid));

										// start integration alphauserpoints component
										if ( $kunena_config->alphauserpointsrules ) {
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
												if ( $kunena_config->alphauserpointsnumchars>0 ) {
													// use if limit chars for a response
													if ( strlen($message)>$kunena_config->alphauserpointsnumchars ) {
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

                                        //Now manage the subscriptions (only if subscriptions are allowed)
                                        if ($kunena_config->allowsubscriptions == 1 && $holdPost == 0)
                                        { //they're allowed
                                            //get the proper user credentials for each subscription to this topic

                                            //clean up the message
                                            $mailmessage = smile::purify($message);
                                            $kunena_db->setQuery("SELECT * FROM #__fb_subscriptions AS a"
                                            . " LEFT JOIN #__users AS u ON a.userid=u.id "
                                            . " WHERE u.block='0' AND a.thread='{$querythread}'");

                                            $subsList = $kunena_db->loadObjectList();
                                            	check_dberror("Unable to load subscriptions.");

                                            if (count($subsList) > 0)
                                            {                                                     //we got more than 0 subscriptions
                                                require_once (KUNENA_PATH_LIB .DS. 'kunena.mail.php'); // include fbMail class for mailing

												$_catobj = new jbCategory($kunena_db, $catid);
                                                foreach ($subsList as $subs)
                                                {
													//check for permission
													if ($subs->id) {
														$_arogrp = $kunena_acl->getAroGroup($subs->id);
														$_arogrp->group_id = $_arogrp->id;
														$_isadm = (strtolower($_arogrp->name) == 'super administrator' || strtolower($_arogrp->name) == 'administrator');
													}
														if (!fb_has_moderator_permission($kunena_db, $_catobj, $subs->id, $_isadm)) {
															$allow_forum = array();
															if (!fb_has_read_permission($_catobj, $allow_forum, $_arogrp->group_id, $kunena_acl)) {
																//maybe remove record from subscription list?
																continue;
														}
													}

                                                    $mailsender = stripslashes($board_title)." "._GEN_FORUM;

                                                    $mailsubject = "[".stripslashes($board_title)." "._GEN_FORUM."] " . stripslashes($messagesubject) . " (" . stripslashes($catName) . ")";

                                                    $msg = "$subs->name,\n\n";
                                                    $msg .= trim(_COM_A_NOTIFICATION1)." ".stripslashes($board_title)." "._GEN_FORUM."\n\n";
                                                    $msg .= _GEN_SUBJECT.": " . stripslashes($messagesubject) . "\n";
                                                    $msg .= _GEN_FORUM.": " . stripslashes($catName) . "\n";
                                                    $msg .= _VIEW_POSTED.": " . stripslashes($fb_authorname) . "\n\n";
                                                    $msg .= _COM_A_NOTIFICATION2.'\n';
                                                    $msg .= "URL: $LastPostUrl\n\n";
                                                    if ($kunena_config->mailfull == 1) {
                                                        $msg .= _GEN_MESSAGE.":\n-----\n";
                                                        $msg .= stripslashes($mailmessage);
                                                        $msg .= "\n-----";
                                                    }
                                                    $msg .= "\n\n";
                                                    $msg .= _COM_A_NOTIFICATION3.'\n';
                                                    $msg .= "\n\n\n\n";
                                                    $msg .= "** Powered by Kunena! - http://www.Kunena.com **";

                                                    if ($ip != "127.0.0.1" && $kunena_my->id != $subs->id) { //don't mail yourself
                                                        JUtility::sendMail($kunena_config->email, $mailsender, $subs->email, $mailsubject, $msg);
                                                    }
                                                }
                                                unset($_catobj);
                                            }
                                        }

                                        //Now manage the mail for moderator or admins (only if configured)
                                        if($kunena_config->mailmod=='1'
                                        || $kunena_config->mailadmin=='1')
                                        { //they're configured
                                            //get the proper user credentials for each moderator for this forum
                                            $sql = "SELECT * FROM #__users AS u";
                                            if($kunena_config->mailmod==1) {
                                                $sql .= " LEFT JOIN #__fb_moderation AS a";
                                                $sql .= " ON a.userid=u.id";
                                                $sql .= "  AND a.catid='{$catid}'";
                                            }
                                            $sql .= " WHERE u.block='0'";
                                            $sql .= " AND (";
                                            // helper for OR condition
                                            $sql2 = '';
                                            if($kunena_config->mailmod==1) {
                                                $sql2 .= " a.userid IS NOT NULL";
                                            }
                                            if($kunena_config->mailadmin==1) {
                                                if(strlen($sql2)) { $sql2 .= " OR "; }
                                                $sql2 .= " u.gid IN (24, 25)";
                                            }
                                            $sql .= "".$sql2.")";

                                            $kunena_db->setQuery($sql);
                                            $modsList = $kunena_db->loadObjectList();
                                            	check_dberror("Unable to load moderators.");

                                            if (count($modsList) > 0)
                                            {                                                     //we got more than 0 moderators eligible for email
                                                require_once (KUNENA_PATH_LIB .DS. 'kunena.mail.php'); // include fbMail class for mailing

                                                foreach ($modsList as $mods)
                                                {
                                                    $mailsender = stripslashes($board_title)." "._GEN_FORUM;

                                                    $mailsubject = "[".stripslashes($board_title)." "._GEN_FORUM."] " . stripslashes($messagesubject) . " (" . stripslashes($catName) . ")";

                                                    $msg = "$mods->name,\n\n";
                                                    $msg .= trim(_COM_A_NOT_MOD1)." ".stripslashes($board_title)." ".trim(_GEN_FORUM)."\n\n";
                                                    $msg .= _GEN_SUBJECT.": " . stripslashes($messagesubject) . "\n";
                                                    $msg .= _GEN_FORUM.": " . stripslashes($catName) . "\n";
                                                    $msg .= _VIEW_POSTED.": " . stripslashes($fb_authorname) . "\n\n";
                                                    $msg .= _COM_A_NOT_MOD2.'\n';
                                                    $msg .= "URL: $LastPostUrl\n\n";
                                                    if ($kunena_config->mailfull == 1) {
                                                        $msg .= _GEN_MESSAGE.":\n-----\n";
                                                        $msg .= stripslashes($mailmessage);
                                                        $msg .= "\n-----";
                                                    }
                                                    $msg .= "\n\n";
                                                    $msg .= _COM_A_NOTIFICATION3.'\n';
                                                    $msg .= "\n\n\n\n";
                                                    $msg .= "** Powered by Kunena! - http://www.Kunena.com **";

                                                    if ($ip != "127.0.0.1" && $kunena_my->id != $mods->id) { //don't mail yourself
                                                        JUtility::sendMail($kunena_config->email, $mailsender, $mods->email, $mailsubject, $msg);
                                                    }
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
                                           	echo CKunenaLink::GetLatestPostAutoRedirectHTML($kunena_config, $pid, $kunena_config->messages_per_page, $catid);

                                        }
                                        else
                                        {
                                            echo '<br /><br /><div align="center">' . _POST_SUCCESS_POSTED . '</div><br /><br />';
                                            echo CKunenaLink::GetLatestPostAutoRedirectHTML($kunena_config, $pid, $kunena_config->messages_per_page, $catid);
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
                                    echo CKunenaLink::GetLatestPostAutoRedirectHTML($kunena_config, $pid, $kunena_config->messages_per_page, $catid);
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
                echo CKunenaLink::GetLatestPostAutoRedirectHTML($kunena_config, $pid, $kunena_config->messages_per_page, $catid);
            }
            else
            {
                if ($do == "quote" && (hasPostPermission($kunena_db, $catid, $id, $kunena_my->id, $kunena_config->pubwrite, $kunena_is_moderator)))
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
                            //$quote = smile::fbStripHtmlTags($quote);
                            $resubject = strtr($message->subject, $table);

                            $resubject = strtolower(substr($resubject, 0, strlen(_POST_RE))) == strtolower(_POST_RE) ? stripslashes($resubject) : _POST_RE . stripslashes($resubject);
                            //$resubject = kunena_htmlspecialchars($resubject);
                            $resubject = smile::fbStripHtmlTags($resubject);
                            //$resubject = smile::fbStripHtmlTags($resubject);
                            $parentid = $message->id;
                            $authorName = $my_name;
                        }
                    }
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
                        ?>
					</form>
						<?php
                }
                else if ($do == "reply" && (hasPostPermission($kunena_db, $catid, $id, $kunena_my->id, $kunena_config->pubwrite, $kunena_is_moderator)))
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
                            $resubject = kunena_htmlspecialchars(strtr($message->subject, $table));
                            $resubject = strtolower(substr($resubject, 0, strlen(_POST_RE))) == strtolower(_POST_RE) ? stripslashes($resubject) : _POST_RE . stripslashes($resubject);
                            $parentid = $message->id;
                            $htmlText = "";
                        }
                    }

                    $authorName = $my_name;
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
                        ?>
					</form>
						<?php
                                        }
                else if ($do == "newFromBot" && (hasPostPermission($kunena_db, $catid, $id, $kunena_my->id, $kunena_config->pubwrite, $kunena_is_moderator)))
                { // The Mosbot "discuss on forums" has detected an unexisting thread and wants to create one
                    $parentid = 0;
                    $id = (int)$id;
                    $setFocus = 0;
                    //                $resubject = base64_decode($resubject); //per mf#6100  -- jdg 16/07/2005
                    $resubject = base64_decode(strtr($resubject, "()", "+/"));
                    $resubject = str_replace('%20', ' ', $resubject);
                    $resubject = preg_replace('/%32/', '&', $resubject);
                    $resubject = preg_replace('/%33/', ';', $resubject);
                    $resubject = preg_replace('/\'/', '&#039;', $resubject);
                    $resubject = preg_replace('/\"/', '&quot;', $resubject);
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
                        ?>
					</form>
						<?php
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
                    if ($kunena_is_moderator) {
                        $allowEdit = 1;
                    }

                    if ($kunena_config->useredit == 1 && $kunena_my->id != "")
                    {
                        //Now, if the author==viewer and the viewer is allowed to edit his/her own post the let them edit
                        if ($kunena_my->id == $userID) {
                            if(((int)$kunena_config->useredittime)==0) {
                                $allowEdit = 1;
                            }
                            else {
                                //Check whether edit is in time
                                $modtime = $mes->modified_time;
                                if(!$modtime) {
                                    $modtime = $mes->time;
                                }
                                if(($modtime + ((int)$kunena_config->useredittime)) >= CKunenaTools::fbGetInternalTime()) {
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

                        //$htmlText = strtr($htmlText, $table);

                        //$htmlText = smile::fbHtmlSafe($htmlText);
                        $resubject = kunena_htmlspecialchars(stripslashes($mes->subject));
                        $authorName = kunena_htmlspecialchars($mes->name);
                        ?>

                        <form action = "<?php echo JRoute::_(KUNENA_LIVEURLREL."&amp;catid=$catid&amp;func=post"); ?>" method = "post" name = "postform" enctype = "multipart/form-data"/>
                        <input type = "hidden" name = "id" value = "<?php echo $mes->id;?>"/>
                        <input type = "hidden" name = "do" value = "editpostnow"/>

                        <?php
                        //get the writing stuff in:
                        $no_file_upload = 0;
                        $no_image_upload = 0;

                        if (file_exists(KUNENA_ABSTMPLTPATH . '/fb_write.html.php')) {
                            include (KUNENA_ABSTMPLTPATH . '/fb_write.html.php');
                        }
                        else {
                            include (KUNENA_PATH_TEMPLATE_DEFAULT .DS. 'fb_write.html.php');
                        }
                        ?>
					</form>
						<?php
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
                    if ($kunena_is_moderator) {
                        $allowEdit = 1;
                    }

                    if ($kunena_config->useredit == 1 && $kunena_my->id != "")
                    {
                        //Now, if the author==viewer and the viewer is allowed to edit his/her own post the let them edit
                        if ($kunena_my->id == $userid) {
                            if(((int)$kunena_config->useredittime)==0) {
                                $allowEdit = 1;
                            }
                            else {
                                $modtime = $mes->modified_time;
                                if(!$modtime) {
                                    $modtime = $mes->time;
                                }
                                if(($modtime + ((int)$kunena_config->useredittime) + ((int)$kunena_config->useredittimegrace)) >= CKunenaTools::fbGetInternalTime()) {
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

                        	if (!$kunena_is_moderator)
                        	{
                        		$kunena_db->setQuery("SELECT review FROM #__fb_categories WHERE id='{$catid}'");
                        		$kunena_db->query() or trigger_dberror('Unable to load review flag from categories.');
                        		$holdPost = $kunena_db->loadResult();
                        	}

                            $kunena_db->setQuery(
                            "UPDATE #__fb_messages SET name=".$kunena_db->quote($fb_authorname).", email=".$kunena_db->quote(addslashes($email))
                            . (($kunena_config->editmarkup) ? " ,modified_by='" . $modified_by
                            . "' ,modified_time='" . $modified_time . "' ,modified_reason=" . $kunena_db->quote($modified_reason) : "") . ", subject=" . $kunena_db->quote(addslashes($subject)) . ", topic_emoticon='" . ((int)$topic_emoticon) .  "', hold='" . ((int)$holdPost) . "' WHERE id={$id}");

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
                                echo CKunenaLink::GetLatestPostAutoRedirectHTML($kunena_config, $id, $kunena_config->messages_per_page, $catid);
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
                    if (!$kunena_is_moderator) {
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
                    if (!$kunena_is_moderator) {
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
                    if (!$kunena_is_moderator) {
			$app->redirect(htmlspecialchars_decode(JRoute::_(KUNENA_LIVEURLREL)), _POST_NOT_MODERATOR);
                    }

                    $catid = (int)$catid;
                    $id = (int)$id;
                    //get list of available forums
                    //$kunena_db->setQuery("SELECT id, name FROM #__fb_categories WHERE parent != '0'");
                    $kunena_db->setQuery("SELECT a.*, b.id AS catid, b.name AS category FROM #__fb_categories AS a LEFT JOIN #__fb_categories AS b ON b.id = a.parent WHERE a.parent!='0' AND a.id IN ($fbSession->allowed) ORDER BY parent, ordering");
                    $catlist = $kunena_db->loadObjectList();
                    	check_dberror("Unable to load categories.");
                    // get topic subject:
                    $kunena_db->setQuery("SELECT subject, id FROM #__fb_messages WHERE id='{$id}'");
                    $topicSubject = $kunena_db->loadResult();
                    	check_dberror("Unable to load messages.");
            ?>

                    <form action = "<?php echo JRoute::_(KUNENA_LIVEURLREL."&amp;func=post"); ?>" method = "post" name = "myform">
                        <input type = "hidden" name = "do" value = "domovepost"/>

                        <input type = "hidden" name = "id" value = "<?php echo $id;?>"/>

                        <p>
<?php echo _GEN_TOPIC; ?>: <strong><?php echo kunena_htmlspecialchars(stripslashes($topicSubject)); ?></strong>

    <br/>

    <br/> <?php echo _POST_MOVE_TOPIC; ?>:

    <br/>

    <select name = "catid" size = "15" class = "fb_move_selectbox">
        <?php
        foreach ($catlist as $cat) {
            echo "<OPTION value=\"$cat->id\" > $cat->category/$cat->name </OPTION>";
        }
        ?>
    </select>

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
		    if (!fb_has_moderator_permission($kunena_db, $newCatObj, $kunena_my->id, $kunena_is_admin)) {
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
                    echo CKunenaLink::GetLatestPostAutoRedirectHTML($kunena_config, $id, $kunena_config->messages_per_page, $catid);
                }
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
						if (!$kunena_is_moderator) {
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
                    $app->redirect(CKunenaLink::GetLatestPageAutoRedirectURL($kunena_config, $id, $kunena_config->messages_per_page), $success_msg);
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
                    $app->redirect(CKunenaLink::GetLatestPageAutoRedirectURL($kunena_config, $id, $kunena_config->messages_per_page), $success_msg);
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
                    $app->redirect(CKunenaLink::GetLatestPageAutoRedirectURL($kunena_config, $id, $kunena_config->messages_per_page), $success_msg);
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
                    $app->redirect(CKunenaLink::GetLatestPageAutoRedirectURL($kunena_config, $id, $kunena_config->messages_per_page), $success_msg);
                }
                else if ($do == "sticky")
                {
                    if (!$kunena_is_moderator) {
			$app->redirect(htmlspecialchars_decode(JRoute::_(KUNENA_LIVEURLREL)), _POST_NOT_MODERATOR);
                    }

                    $id = (int)$id;
                    $success_msg = _POST_STICKY_NOT_SET;
                    $kunena_db->setQuery("update #__fb_messages set ordering=1 where id=$id");
                    if ($id && $kunena_db->query() && $kunena_db->getAffectedRows()==1) {
                        $success_msg = _POST_STICKY_SET;
                    }
                    $app->redirect(CKunenaLink::GetLatestPageAutoRedirectURL($kunena_config, $id, $kunena_config->messages_per_page), $success_msg);
                }
                else if ($do == "unsticky")
                {
                    if (!$kunena_is_moderator) {
			$app->redirect(htmlspecialchars_decode(JRoute::_(KUNENA_LIVEURLREL)), _POST_NOT_MODERATOR);
                    }

                    $id = (int)$id;
                    $success_msg = _POST_STICKY_NOT_UNSET;
                    $kunena_db->setQuery("update #__fb_messages set ordering=0 where id=$id");
                    if ($id && $kunena_db->query() && $kunena_db->getAffectedRows()==1) {
                        $success_msg = _POST_STICKY_UNSET;
                    }
                    $app->redirect(CKunenaLink::GetLatestPageAutoRedirectURL($kunena_config, $id, $kunena_config->messages_per_page), $success_msg);
                }
                else if ($do == "lock")
                {
                    if (!$kunena_is_moderator) {
			$app->redirect(htmlspecialchars_decode(JRoute::_(KUNENA_LIVEURLREL)), _POST_NOT_MODERATOR);
                    }

                    $id = (int)$id;
                    $success_msg = _POST_LOCK_NOT_SET;
                    $kunena_db->setQuery("update #__fb_messages set locked=1 where id=$id");
                    if ($id && $kunena_db->query() && $kunena_db->getAffectedRows()==1) {
                        $success_msg = _POST_LOCK_SET;
                    }
                    $app->redirect(CKunenaLink::GetLatestPageAutoRedirectURL($kunena_config, $id, $kunena_config->messages_per_page), $success_msg);
                }
                else if ($do == "unlock")
                {
                    if (!$kunena_is_moderator) {
			$app->redirect(htmlspecialchars_decode(JRoute::_(KUNENA_LIVEURLREL)), _POST_NOT_MODERATOR);
                    }

                    $id = (int)$id;
                    $success_msg = _POST_LOCK_NOT_UNSET;
                    $kunena_db->setQuery("update #__fb_messages set locked=0 where id=$id");
                    if ($id && $kunena_db->query() && $kunena_db->getAffectedRows()==1) {
                        $success_msg = _POST_LOCK_UNSET;
                    }
                    $app->redirect(CKunenaLink::GetLatestPageAutoRedirectURL($kunena_config, $id, $kunena_config->messages_per_page), $success_msg);
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
    $kunena_config =& CKunenaConfig::getInstance();

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
            echo "<p align=\"center\">";
            echo _POST_NO_PUBACCESS1 . "<br />";
            echo _POST_NO_PUBACCESS2 . "<br /><br />";

            if ($kunena_config->fb_profile == 'cb') {
                echo '<a href="' . CKunenaCBProfile::getRegisterURL() . '">' . _POST_NO_PUBACCESS3 . '</a><br /></p>';
            }
            else {
                echo '<a href="' . JRoute::_('index.php?option=com_registration&amp;view=register') . '">' . _POST_NO_PUBACCESS3 . '</a><br /></p>';
            }
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

function listThreadHistory($id, $kunena_config, $kunena_db)
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
        $kunena_db->setQuery("SELECT * FROM #__fb_messages AS m LEFT JOIN #__fb_messages_text AS t ON m.id=t.mesid WHERE (thread='{$thread}' OR id='{$thread}') AND hold='0' ORDER BY time DESC LIMIT " . $kunena_config->historylimit);
        $messages = $kunena_db->loadObjectList();
        	check_dberror("Unable to load messages.");
        //and the subject of the first thread (for reference)
        $kunena_db->setQuery("SELECT subject FROM #__fb_messages WHERE id='{$thread}' and parent='0'");
        $this_message_subject = $kunena_db->loadResult();
        	check_dberror("Unable to load messages.");
        echo "<b>" . _POST_TOPIC_HISTORY . ":</b> " . kunena_htmlspecialchars(stripslashes($this_message_subject)) . " <br />" . _POST_TOPIC_HISTORY_MAX . " $kunena_config->historylimit " . _POST_TOPIC_HISTORY_LAST . "<br />";
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
            $kunena_emoticons = smile::getEmoticons(1);

            foreach ($messages as $mes)
            {
                $k = 1 - $k;
                $mes->name = kunena_htmlspecialchars($mes->name);
                $mes->email = kunena_htmlspecialchars($mes->email);
                $mes->subject = kunena_htmlspecialchars($mes->subject);


                $fb_message_txt = stripslashes(($mes->message));
                $fb_message_txt = smile::smileReplace($fb_message_txt, 1, $kunena_config->disemoticons, $kunena_emoticons);
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
                        $fb_message_txt = smile::htmlwrap($fb_message_txt, $kunena_config->wrap);

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
                if ($kunena_config->enableforumjump) {
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
