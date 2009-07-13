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
defined( '_JEXEC' ) or die('Restricted access');
$fbConfig =& CKunenaConfig::getInstance();
?>
<div class="<?php echo $boardclass; ?>_bt_cvr1">
<div class="<?php echo $boardclass; ?>_bt_cvr2">
<div class="<?php echo $boardclass; ?>_bt_cvr3">
<div class="<?php echo $boardclass; ?>_bt_cvr4">
<div class="<?php echo $boardclass; ?>_bt_cvr5">
<table class = "fb_blocktable" id = "fb_forumprofile" border = "0" cellspacing = "0" cellpadding = "0" width="100%">
    <thead>
        <tr>
            <th>
                <div class = "fb_title_cover">
                    <span class = "fb_title"><?php echo _USER_PROFILE; ?> <?php echo $username; ?></span>
                </div>
            </th>
        </tr>
    </thead>

    <tbody class = "fb_myprofile_menu">

        <tr>
            <td class = "fb_myprofile_menu_staff">
                <span class = "fb_myprofile_menu_title"><?php echo _KUNENA_MYPROFILE_PERSONAL_INFO; ?></span>
                <a href = "<?php echo JRoute::_(KUNENA_LIVEURLREL . '&amp;func=myprofile'); ?>"> <?php echo _KUNENA_MYPROFILE_SUMMARY; ?> </a>
<?php
 	// Only show userdetails link if we are in charge of the profile
    if ($fbConfig->fb_profile == 'fb')
    {
?>
            	<a href = "<?php echo JRoute::_(KUNENA_LIVEURLREL . '&amp;func=myprofile&amp;do=userdetails'); ?>"> <?php echo _KUNENA_EDIT_TITLE; ?> </a>
<?php
    }

    // Only show avatar link if we are in charge of it
    if ($fbConfig->allowavatar && $fbConfig->avatar_src == 'fb')
    {
?>
            	<a href = "<?php echo JRoute::_(KUNENA_LIVEURLREL . '&amp;func=myprofile&amp;do=avatar'); ?>"> <?php echo _KUNENA_MYPROFILE_MYAVATAR; ?></a>
<?php
    }
?>
            </td>
        </tr>

        <tr>
            <td class = "fb_myprofile_menu_staff">
                <span class = "fb_myprofile_menu_title"><?php echo _KUNENA_MYPROFILE_FORUM_SETTINGS; ?></span> <a href = "<?php echo JRoute::_(KUNENA_LIVEURLREL . '&amp;func=myprofile&amp;do=showset'); ?>"><?php echo _KUNENA_MYPROFILE_LOOK_AND_LAYOUT; ?></a>
            <a href = "<?php echo JRoute::_(KUNENA_LIVEURLREL . '&amp;func=myprofile&amp;do=profileinfo'); ?>"><?php echo _KUNENA_MYPROFILE_MY_PROFILE_INFO; ?></a> <a href = "<?php echo JRoute::_(KUNENA_LIVEURLREL . '&amp;func=myprofile&amp;do=showmsg'); ?>"><?php echo _KUNENA_MYPROFILE_MY_POSTS; ?></a> <a href = "<?php echo JRoute::_(KUNENA_LIVEURLREL . '&amp;func=myprofile&amp;do=showsub'); ?>"><?php echo _KUNENA_MYPROFILE_MY_SUBSCRIBES; ?></a> <a href = "<?php echo JRoute::_(KUNENA_LIVEURLREL . '&amp;func=myprofile&amp;do=showfav'); ?>"><?php echo _KUNENA_MYPROFILE_MY_FAVORITES; ?></a>
            </td>
        </tr>

<?php

    // UddeIM
    if ($fbConfig->pm_component == 'uddeim')
    {
?>
        <tr>
            <td class = "fb_myprofile_menu_staff">
                <span class = "fb_myprofile_menu_title"><?php echo _KUNENA_MYPROFILE_PRIVATE_MESSAGING; ?></span>
            <a href = "<?php echo JRoute::_('index.php?option=com_uddeim&amp;Itemid=' . KUNENA_UIM_ITEMID); ?>"><?php echo _KUNENA_MYPROFILE_INBOX; ?> </a>
            <a href = "<?php echo JRoute::_('index.php?option=com_uddeim&amp;task=new&amp;Itemid=' . KUNENA_UIM_ITEMID); ?>"><?php echo _KUNENA_MYPROFILE_NEW_MESSAGE; ?></a>
            <a href = "<?php echo JRoute::_('index.php?option=com_uddeim&amp;task=outbox&amp;Itemid=' . KUNENA_UIM_ITEMID); ?>"><?php echo _KUNENA_MYPROFILE_OUTBOX; ?></a>
            <a href = "<?php echo JRoute::_('index.php?option=com_uddeim&amp;task=trashcan&amp;Itemid=' . KUNENA_UIM_ITEMID); ?>"><?php echo _KUNENA_MYPROFILE_TRASH; ?></a>
            </td>
        </tr>
<?php
    }

    // MISSUS
    if ($fbConfig->pm_component == 'missus')
    {

?>
       <tr>
            <td class = "fb_myprofile_menu_staff">
                <span class = "fb_myprofile_menu_title"><?php echo _KUNENA_MYPROFILE_PRIVATE_MESSAGING; ?></span>
            <a href = "<?php echo JRoute::_('index.php?option=com_missus&amp;func=showinbox&amp;Itemid=' . KUNENA_MISSUS_ITEMID); ?>"><?php echo _KUNENA_MYPROFILE_INBOX; ?> </a>
            <a href = "<?php echo JRoute::_('index.php?option=com_missus&amp;func=newmsg&amp;Itemid=' . KUNENA_MISSUS_ITEMID); ?>"><?php echo _KUNENA_MYPROFILE_NEW_MESSAGE; ?></a>
            <a href = "<?php echo JRoute::_('index.php?option=com_missus&amp;func=showsent&amp;Itemid=' . KUNENA_MISSUS_ITEMID); ?>"><?php echo _KUNENA_MYPROFILE_OUTBOX; ?></a>
            <a href = "<?php echo JRoute::_('index.php?option=com_missus&amp;func=showtrash&amp;Itemid=' . KUNENA_MISSUS_ITEMID); ?>"><?php echo _KUNENA_MYPROFILE_TRASH; ?></a>
            <a href = "<?php echo JRoute::_('index.php?option=com_missus&amp;func=showcontacts&amp;Itemid=' . KUNENA_MISSUS_ITEMID); ?>"><?php echo _KUNENA_MYPROFILE_CONTACTS; ?></a>
            <a href = "<?php echo JRoute::_('index.php?option=com_missus&amp;func=showconfig&amp;Itemid=' . KUNENA_MISSUS_ITEMID); ?>"><?php echo _KUNENA_MYPROFILE_SETTINGS; ?></a>
            </td>
        </tr>
<?php
    }

    // JIM
    if ($fbConfig->pm_component == 'jim')
    {

?>
       <tr>
            <td class = "fb_myprofile_menu_staff">
                <span class = "fb_myprofile_menu_title"><?php echo _KUNENA_MYPROFILE_PRIVATE_MESSAGING; ?></span>
            <a href = "<?php echo JRoute::_('index.php?option=com_jim&amp;task=inbox'); ?>"><?php echo _KUNENA_MYPROFILE_INBOX; ?> </a>
            <a href = "<?php echo JRoute::_('index.php?option=com_jim&amp;task=new'); ?>"><?php echo _KUNENA_MYPROFILE_NEW_MESSAGE; ?></a>
            <a href = "<?php echo JRoute::_('index.php?option=com_jim&amp;task=outbox'); ?>"><?php echo _KUNENA_MYPROFILE_OUTBOX; ?></a>
            </td>
        </tr>
<?php

    }

?>


    </tbody>
</table>
</div>
</div>
</div>
</div>
</div>