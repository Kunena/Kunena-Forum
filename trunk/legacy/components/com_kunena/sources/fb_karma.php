<?php
/**
* @version $Id: fb_karma.php 831 2008-07-15 04:14:59Z fxstein $
* Fireboard Component
* @package Fireboard
* @Copyright (C) 2006 - 2007 Best Of Joomla All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.bestofjoomla.com
*
* Based on Joomlaboard Component
* @copyright (C) 2000 - 2004 TSMF / Jan de Graaff / All Rights Reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author TSMF & Jan de Graaff
**/

defined('_VALID_MOS') or die('Direct Access to this location is not allowed.');

global $fbConfig;
global $is_Moderator;
//Modify this to change the minimum time between karma modifications from the same user
$karma_min_seconds = '14400'; // 14400 seconds = 6 hours
?>

<table border = 0 cellspacing = 0 cellpadding = 0 width = "100%" align = "center">
    <tr>
        <td>
            <br>
            <center>
                <?php
                //I hope these are needed :)
                $catid = (int)$catid;
                $pid = (int)$pid;

                //This checks:
                // - if the karma function is activated by the admin
                // - if a registered user submits the modify request
                // - if he specifies an action related to the karma change
                // - if he specifies the user that will have the karma modified
                if ($fbConfig->showkarma && $my->id != "" && $my->id != 0 && $do != '' && $userid != '')
                {
                    $time = FBTools::fbGetInternalTime();

                    if ($my->id != $userid)
                    {
                        // This checkes to see if it's not too soon for a new karma change
                        if (!$is_Moderator)
                        {
                            $database->setQuery('SELECT karma_time FROM #__fb_users WHERE userid=' . $my->id . '');
                            $karma_time_old = $database->loadResult();
                            $karma_time_diff = $time - $karma_time_old;
                        }

                        if ($karma_time_diff >= $karma_min_seconds || $is_Moderator)
                        {
                            if ($do == "increase")
                            {
                                $database->setQuery('UPDATE #__fb_users SET karma_time=' . $time . ' WHERE userid=' . $my->id . '');
							    $database->query() or trigger_dberror("Unable to update karma.");
							    $database->setQuery('UPDATE #__fb_users SET karma=karma+1 WHERE userid=' . $userid . '');
							    $database->query() or trigger_dberror("Unable to update karma.");
							    echo _KARMA_INCREASED . '<br /> <a href="' . sefRelToAbs(JB_LIVEURLREL . '&amp;func=view&amp;catid=' . $catid . '&amp;id=' . $pid) . '">' . _POST_CLICK . '</a>.';
                ?>

                            <script language = "javascript">
                                setTimeout("location='<?php echo sefRelToAbs(JB_LIVEURLREL.'&func=view&catid='.$catid.'&id='.$pid); ?>'", 3500);
                            </script>

                <?php
                            }
                            else if ($do == "decrease")
                            {
                                $database->setQuery('UPDATE #__fb_users SET karma_time=' . $time . ' WHERE userid=' . $my->id . '');
                                $database->query() or trigger_dberror("Unable to update karma.");
                                $database->setQuery('UPDATE #__fb_users SET karma=karma-1 WHERE userid=' . $userid . '');
                                $database->query() or trigger_dberror("Unable to update karma.");
                                echo _KARMA_DECREASED . '<br /> <a href="' . sefRelToAbs(JB_LIVEURLREL. '&amp;func=view&amp;catid=' . $catid . '&amp;id=' . $pid) . '">' . _POST_CLICK . '</a>.';
                ?>

                            <script language = "javascript">
                                setTimeout("location='<?php echo sefRelToAbs(JB_LIVEURLREL.'&func=view&catid='.$catid.'&id='.$pid); ?>'", 3500);
                            </script>

                <?php
                            }
                            else
                            { //you got me there... don't know what to $do
                                echo _USER_ERROR_A;
                                echo _USER_ERROR_B . "<br /><br />";
                                echo _USER_ERROR_C . "<br /><br />" . _USER_ERROR_D . ": <code>fb001-karma-02NoDO</code><br /><br />";
                            }
                        }
                        else
                            echo _KARMA_WAIT . '<br /> ' . _KARMA_BACK . ' <a href="' . sefRelToAbs(JB_LIVEURLREL . '&amp;func=view&amp;catid=' . $catid . '&amp;id=' . $pid) . '">' . _POST_CLICK . '</a>.';
                    }
                    else if ($my->id == $userid) // In case the user tries modifing his own karma by changing the userid from the URL...
                    {
                        if ($do == "increase")   // Seriously decrease his karma if he tries to increase it
                        {
                            $database->setQuery('UPDATE #__fb_users SET karma=karma-10, karma_time=' . $time . ' WHERE userid=' . $my->id . '');
                            $database->query() or trigger_dberror("Unable to update karma.");
							echo _KARMA_SELF_INCREASE . '<br />' . _KARMA_BACK . ' <a href="' . sefRelToAbs(JB_LIVEURLREL . '&amp;func=view&amp;catid=' . $catid . '&amp;id=' . $pid) . '">' . _POST_CLICK . '</a>.';
                        }

                        if ($do == "decrease") // Stop him from decreasing his karma but still update karma_time
                        {
                            $database->setQuery('UPDATE #__fb_users SET karma_time=' . $time . ' WHERE userid=' . $my->id . '');
                            $database->query() or trigger_dberror("Unable to update karma.");
                            echo _KARMA_SELF_DECREASE . '<br /> ' . _KARMA_BACK . ' <a href="' . sefRelToAbs(JB_LIVEURLREL . '&amp;func=view&amp;catid=' . $catid . '&amp;id=' . $pid) . '">' . _POST_CLICK . '</a>.';
                        }
                    }
                }
                else
                { //get outa here, you fraud!
                    echo _USER_ERROR_A;
                    echo _USER_ERROR_B . "<br /><br />";
                    echo _USER_ERROR_C . "<br /><br />" . _USER_ERROR_D . ": <code>fb001-karma-01NLO</code><br /><br />";
                //that should scare 'em off enough... ;-)
                }
                ?>
            </center>
        </td>
    </tr>
</table>