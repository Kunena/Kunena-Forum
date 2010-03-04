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

defined( '_JEXEC' ) or die();


$kunena_config =& CKunenaConfig::getInstance();
$kunena_db = &JFactory::getDBO();

$do = JRequest::getCmd ( 'do', '' );
$userid = JRequest::getInt ( 'userid', 0 );
$pid = JRequest::getInt ( 'pid', 0 );

if ($pid) {
	$kunena_db->setQuery("SELECT catid, thread FROM #__fb_messages WHERE id='{$pid}'");
	$kmsg = $kunena_db->loadObject();
	check_dberror("Unable to load message.");
	if (is_object($kmsg)) {
		$catid = $kmsg->catid;
		$thread = $kmsg->thread;
	}
}

//Modify this to change the minimum time between karma modifications from the same user
$karma_min_seconds = '14400'; // 14400 seconds = 6 hours
?>

<table border = 0 cellspacing = 0 cellpadding = 0 width = "100%" align = "center">
    <tr>
        <td>
            <br>
            <center>
                <?php
                //This checks:
                // - if the karma function is activated by the admin
                // - if a registered user submits the modify request
                // - if he specifies an action related to the karma change
                // - if he specifies the user that will have the karma modified
                if ($kunena_config->showkarma && $kunena_my->id != "" && $kunena_my->id != 0 && $do != '' && $userid != '')
                {
                    $time = CKunenaTimeformat::internalTime();

                    if ($kunena_my->id != $userid)
                    {
                        // This checkes to see if it's not too soon for a new karma change
                        if (!CKunenaTools::isModerator($kunena_my->id, $catid))
                        {
                        	$userprofile = CKunenaUserprofile::getInstance($kunena_my->id);
                            $karma_time_old = $userprofile->karma_time;
                            $karma_time_diff = $time - $karma_time_old;
                        }

                        if (CKunenaTools::isModerator($kunena_my->id, $catid) || $karma_time_diff >= $karma_min_seconds)
                        {
                            if ($do == "increase")
                            {
                                $kunena_db->setQuery('UPDATE #__fb_users SET karma_time=' . $time . ' WHERE userid=' . $kunena_my->id . '');
							    $kunena_db->query();
							    check_dberror("Unable to update karma.");
							    $kunena_db->setQuery('UPDATE #__fb_users SET karma=karma+1 WHERE userid=' . $userid . '');
							    $kunena_db->query();
							    check_dberror("Unable to update karma.");
							    echo JText::_('COM_KUNENA_KARMA_INCREASED') . '<br />';
								if ($pid) {
									echo CKunenaLink::GetThreadLink('view', $catid, $pid, JText::_('COM_KUNENA_POST_CLICK'), JText::_('COM_KUNENA_POST_CLICK'));
                                	echo CKunenaLink::GetAutoRedirectHTML(JRoute::_(KUNENA_LIVEURLREL.'&amp;func=view&amp;catid='.$catid.'&id='.$pid), 3500);
								} else {
									echo CKunenaLink::GetProfileLink(null, $userid, JText::_('COM_KUNENA_POST_CLICK'));
                                	echo CKunenaLink::GetAutoRedirectHTML(CKunenaLink::GetProfileURL($userid), 3500);
                                }
                            }
                            else if ($do == "decrease")
                            {
                                $kunena_db->setQuery('UPDATE #__fb_users SET karma_time=' . $time . ' WHERE userid=' . $kunena_my->id . '');
                                $kunena_db->query();
                                check_dberror("Unable to update karma.");
                                $kunena_db->setQuery('UPDATE #__fb_users SET karma=karma-1 WHERE userid=' . $userid . '');
                                $kunena_db->query();
                                check_dberror("Unable to update karma.");
                                echo JText::_('COM_KUNENA_KARMA_DECREASED') . '<br />';
								if ($pid) {
									echo CKunenaLink::GetThreadLink('view', $catid, $pid, JText::_('COM_KUNENA_POST_CLICK'), JText::_('COM_KUNENA_POST_CLICK'));
                                	echo CKunenaLink::GetAutoRedirectHTML(JRoute::_(KUNENA_LIVEURLREL.'&amp;func=view&amp;catid='.$catid.'&id='.$pid), 3500);
								} else {
									echo CKunenaLink::GetProfileLink(null, $userid, JText::_('COM_KUNENA_POST_CLICK'));
									echo CKunenaLink::GetAutoRedirectHTML(CKunenaLink::GetProfileURL($userid), 3500);
                                }
                            }
                            else
                            { //you got me there... don't know what to $do
                                echo JText::_('COM_KUNENA_USER_ERROR_A');
                                echo JText::_('COM_KUNENA_USER_ERROR_B') . "<br /><br />";
                                echo JText::_('COM_KUNENA_USER_ERROR_C') . "<br /><br />" . JText::_('COM_KUNENA_USER_ERROR_D') . ": <code>fb001-karma-02NoDO</code><br /><br />";
                            }
                        } else {
                        	if ($pid) {
                        		echo JText::_('COM_KUNENA_KARMA_WAIT') . '<br /> ' . JText::_('COM_KUNENA_KARMA_BACK') .' '. CKunenaLink::GetSefHrefLink( KUNENA_LIVEURLREL . '&amp;func=view&amp;catid=' . $catid . '&amp;id=' . $pid , JText::_('COM_KUNENA_POST_CLICK') , JText::_('COM_KUNENA_POST_CLICK'), 'nofollow');
                        	}else{
                        		echo JText::_('COM_KUNENA_KARMA_WAIT') . '<br /> ' . JText::_('COM_KUNENA_KARMA_BACK') .' '. CKunenaLink::GetSefHrefLink( CKunenaLink::GetProfileURL($userid) , JText::_('COM_KUNENA_POST_CLICK') , JText::_('COM_KUNENA_POST_CLICK'), 'nofollow');
                        	}
                        }
                    }
                    else if ($kunena_my->id == $userid) // In case the user tries modifing his own karma by changing the userid from the URL...
                    {
                        if ($do == "increase")   // Seriously decrease his karma if he tries to increase it
                        {
                            $kunena_db->setQuery('UPDATE #__fb_users SET karma=karma-10, karma_time=' . $time . ' WHERE userid=' . $kunena_my->id . '');
                            $kunena_db->query();
                            check_dberror("Unable to update karma.");
                            if ($pid) {
                            	echo JText::_('COM_KUNENA_KARMA_SELF_INCREASE') . '<br />' . JText::_('COM_KUNENA_KARMA_BACK') . ' ' . CKunenaLink::GetSefHrefLink(KUNENA_LIVEURLREL . '&amp;func=view&amp;catid=' . $catid . '&amp;id=' . $pid , JText::_('COM_KUNENA_POST_CLICK') , JText::_('COM_KUNENA_POST_CLICK') , 'nofollow');
                            } else {
                            	echo JText::_('COM_KUNENA_KARMA_SELF_INCREASE') . '<br />' . JText::_('COM_KUNENA_KARMA_BACK') . ' ' . CKunenaLink::GetSefHrefLink(CKunenaLink::GetProfileURL($userid) , JText::_('COM_KUNENA_POST_CLICK') , JText::_('COM_KUNENA_POST_CLICK') , 'nofollow');
                            }
                        }

                        if ($do == "decrease") // Stop him from decreasing his karma but still update karma_time
                        {
                            $kunena_db->setQuery('UPDATE #__fb_users SET karma_time=' . $time . ' WHERE userid=' . $kunena_my->id . '');
                            $kunena_db->query();
                            check_dberror("Unable to update karma.");
                            if ($pid) {
                            	echo JText::_('COM_KUNENA_KARMA_SELF_DECREASE') . '<br />' . JText::_('COM_KUNENA_KARMA_BACK') . ' ' . CKunenaLink::GetSefHrefLink(KUNENA_LIVEURLREL . '&amp;func=view&amp;catid=' . $catid . '&amp;id=' . $pid , JText::_('COM_KUNENA_POST_CLICK') , JText::_('COM_KUNENA_POST_CLICK'), 'nofollow' );
                            } else {
                            	echo JText::_('COM_KUNENA_KARMA_SELF_DECREASE') . '<br />' . JText::_('COM_KUNENA_KARMA_BACK') . ' ' . CKunenaLink::GetSefHrefLink(CKunenaLink::GetProfileURL($userid) , JText::_('COM_KUNENA_POST_CLICK') , JText::_('COM_KUNENA_POST_CLICK'), 'nofollow' );
                            }
                        }
                    }
                }
                else
                { //get outa here, you fraud!
                    echo JText::_('COM_KUNENA_USER_ERROR_A');
                    echo JText::_('COM_KUNENA_USER_ERROR_B') . "<br /><br />";
                    echo JText::_('COM_KUNENA_USER_ERROR_C') . "<br /><br />" . JText::_('COM_KUNENA_USER_ERROR_D') . ": <code>fb001-karma-01NLO</code><br /><br />";
                //that should scare 'em off enough... ;-)
                }
                ?>
            </center>
        </td>
    </tr>
</table>
