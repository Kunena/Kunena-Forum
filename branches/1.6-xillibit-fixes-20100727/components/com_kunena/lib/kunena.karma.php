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


$kunena_config = KunenaFactory::getConfig ();
$kunena_db = &JFactory::getDBO();
$kunena_app =& JFactory::getApplication();

$do = JRequest::getCmd ( 'do', '' );
$userid = JRequest::getInt ( 'userid', 0 );
$pid = JRequest::getInt ( 'pid', 0 );

if ($pid) {
	$kunena_db->setQuery("SELECT catid, thread FROM #__kunena_messages WHERE id={$kunena_db->Quote($pid)}");
	$kmsg = $kunena_db->loadObject();
	if (KunenaError::checkDatabaseError()) return;
	if (is_object($kmsg)) {
		$catid = $kmsg->catid;
		$thread = $kmsg->thread;
	}
}

//Modify this to change the minimum time between karma modifications from the same user
$karma_min_seconds = '14400'; // 14400 seconds = 6 hours
?>

<table>
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
                    	if (JRequest::checkToken ( 'get' ) == false) {
							$this->_app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
                    		if ($pid) {
								$kunena_app->redirect ( CKunenaLink::GetLatestPageAutoRedirectURL ( $pid, $kunena_config->messages_per_page, $catid) );
							} else {
								$kunena_app->redirect ( CKunenaLink::GetMyProfileURL ( $userid) );
                            }
							return;
						}
                        // This checkes to see if it's not too soon for a new karma change
                        if (!CKunenaTools::isModerator($kunena_my->id, $catid))
                        {
                        	$userprofile = KunenaFactory::getUser($kunena_my->id);
                            $karma_time_old = $userprofile->karma_time;
                            $karma_time_diff = $time - $karma_time_old;
                        }

                        if (CKunenaTools::isModerator($kunena_my->id, $catid) || $karma_time_diff >= $karma_min_seconds)
                        {
                            if ($do == "increase")
                            {
                                $kunena_db->setQuery("UPDATE #__kunena_users SET karma_time={$kunena_db->Quote($time)} WHERE userid={$kunena_db->Quote( $kunena_my->id )} ");
							    $kunena_db->query();
							    if (KunenaError::checkDatabaseError()) return;
							    $kunena_db->setQuery("UPDATE #__kunena_users SET karma=karma+1 WHERE userid={$kunena_db->Quote( $userid )}");
							    $kunena_db->query();
							    if (KunenaError::checkDatabaseError()) return;
							    echo JText::_('COM_KUNENA_KARMA_INCREASED') . '<br />';
                           	 	if ($pid) {
								    $kunena_app->enqueueMessage(JText::_('COM_KUNENA_KARMA_INCREASED'));
									$kunena_app->redirect ( CKunenaLink::GetLatestPageAutoRedirectURL ( $pid, $kunena_config->messages_per_page, $catid) );
								} else {
                                	$kunena_app->enqueueMessage(JText::_('COM_KUNENA_KARMA_INCREASED'));
									$kunena_app->redirect ( CKunenaLink::GetMyProfileURL ( $userid) );
                                }
                            }
                            else if ($do == "decrease")
                            {
                                $kunena_db->setQuery("UPDATE #__kunena_users SET karma_time={$kunena_db->Quote($time)} WHERE userid={$kunena_db->Quote($kunena_my->id)}");
                                $kunena_db->query();
                                if (KunenaError::checkDatabaseError()) return;
                                $kunena_db->setQuery("UPDATE #__kunena_users SET karma=karma-1 WHERE userid={$kunena_db->Quote($userid)}");
                                $kunena_db->query();
                                if (KunenaError::checkDatabaseError()) return;
                                echo JText::_('COM_KUNENA_KARMA_DECREASED') . '<br />';
                            	if ($pid) {
									$kunena_app->enqueueMessage(JText::_('COM_KUNENA_KARMA_DECREASED'));
									$kunena_app->redirect ( CKunenaLink::GetLatestPageAutoRedirectURL ( $pid, $kunena_config->messages_per_page, $catid) );
								} else {
									$kunena_app->enqueueMessage(JText::_('COM_KUNENA_KARMA_DECREASED'));
									$kunena_app->redirect ( CKunenaLink::GetMyProfileURL ( $userid) );
                                }
                            }
                            else
                            { //you got me there... don't know what to $do
                                $kunena_app->enqueueMessage(JText::_('COM_KUNENA_USER_ERROR_KARMA'));
                    			$kunena_app->redirect ( CKunenaLink::GetLatestPageAutoRedirectURL ( $pid, $kunena_config->messages_per_page ) );
                            }
                        } else {
                        	if ($pid) {
                        		$kunena_app->enqueueMessage(JText::_('COM_KUNENA_KARMA_WAIT'));
                        		$kunena_app->redirect ( CKunenaLink::GetLatestPageAutoRedirectURL ( $pid, $kunena_config->messages_per_page, $catid) );
                        	}else{
                        		$kunena_app->enqueueMessage(JText::_('COM_KUNENA_KARMA_WAIT'));
                        		$kunena_app->redirect ( CKunenaLink::GetMyProfileURL ( $userid) );
                        	}
                        }
                    }
                    else if ($kunena_my->id == $userid) // In case the user tries modifing his own karma by changing the userid from the URL...
                    {
                        if ($do == "increase")   // Seriously decrease his karma if he tries to increase it
                        {
                            $kunena_db->setQuery("UPDATE #__kunena_users SET karma=karma-10, karma_time={$kunena_db->Quote($time)} WHERE userid='{$kunena_db->Quote($kunena_my->id)}");
                            $kunena_db->query();
                            if (KunenaError::checkDatabaseError()) return;
                        	if ($pid) {
                            	$kunena_app->enqueueMessage(JText::_('COM_KUNENA_KARMA_SELF_INCREASE'));
                        		$kunena_app->redirect ( CKunenaLink::GetLatestPageAutoRedirectURL ( $pid, $kunena_config->messages_per_page, $catid) );
                            } else {
                            	$kunena_app->enqueueMessage(JText::_('COM_KUNENA_KARMA_SELF_INCREASE'));
                        		$kunena_app->redirect ( CKunenaLink::GetMyProfileURL ( $userid) );
                            }
                        }

                        if ($do == "decrease") // Stop him from decreasing his karma but still update karma_time
                        {
                            $kunena_db->setQuery("UPDATE #__kunena_users SET karma_time={$kunena_db->Quote($time)} WHERE userid={$kunena_db->Quote($kunena_my->id)}");
                            $kunena_db->query();
                            if (KunenaError::checkDatabaseError()) return;
                        	if ($pid) {
                            	$kunena_app->enqueueMessage(JText::_('COM_KUNENA_KARMA_SELF_DECREASE'));
                        		$kunena_app->redirect ( CKunenaLink::GetLatestPageAutoRedirectURL ( $pid, $kunena_config->messages_per_page, $catid) );
                            } else {
                            	$kunena_app->enqueueMessage(JText::_('COM_KUNENA_KARMA_SELF_DECREASE'));
                        		$kunena_app->redirect ( CKunenaLink::GetMyProfileURL ( $userid) );
                            }
                        }
                    }
                }
                else
                { //get outa here, you fraud!
                    $kunena_app->enqueueMessage(JText::_('COM_KUNENA_USER_ERROR_KARMA'));
                    $kunena_app->redirect ( CKunenaLink::GetLatestPageAutoRedirectURL ( $pid, $kunena_config->messages_per_page ) );
                }
                ?>
            </center>
        </td>
    </tr>
</table>
