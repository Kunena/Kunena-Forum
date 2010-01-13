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
defined( '_JEXEC' ) or die();

$kunena_config =& CKunenaConfig::getInstance();
?>
<div class="<?php echo KUNENA_BOARD_CLASS; ?>_bt_cvr1">
<div class="<?php echo KUNENA_BOARD_CLASS; ?>_bt_cvr2">
<div class="<?php echo KUNENA_BOARD_CLASS; ?>_bt_cvr3">
<div class="<?php echo KUNENA_BOARD_CLASS; ?>_bt_cvr4">
<div class="<?php echo KUNENA_BOARD_CLASS; ?>_bt_cvr5">
<table class = "fb_blocktable" id = "fb_forumprofile" border = "0" cellspacing = "0" cellpadding = "0" width="100%">
    <thead>
        <tr>
            <th>
                <div class = "fb_title_cover">
                    <span class = "fb_title"><?php echo _USER_PROFILE; ?> <?php echo $this->kunena_username; ?></span>
                </div>
            </th>
        </tr>
    </thead>

    <tbody class = "fb_myprofile_menu">

        <tr>
            <td class = "fb_myprofile_menu_staff">
                <span class = "fb_myprofile_menu_title"><?php echo _KUNENA_MYPROFILE_PERSONAL_INFO; ?></span>
                <?php echo CKunenaLink::GetSefHrefLink(KUNENA_LIVEURLREL . '&amp;func=myprofile', _KUNENA_MYPROFILE_SUMMARY, _KUNENA_MYPROFILE_SUMMARY,'nofollow'); ?>
<?php
 	// Only show userdetails link if we are in charge of the profile
    if ($kunena_config->fb_profile == 'fb')
    {
?>
            	<?php echo CKunenaLink::GetSefHrefLink(KUNENA_LIVEURLREL . '&amp;func=myprofile&amp;do=userdetails', _KUNENA_EDIT_TITLE, _KUNENA_EDIT_TITLE,'follow'); ?>
<?php
    }

    // Only show avatar link if we are in charge of it
    if ($kunena_config->allowavatar && $kunena_config->avatar_src == 'fb')
    {
?>
            	<?php echo CKunenaLink::GetSefHrefLink(KUNENA_LIVEURLREL . '&amp;func=myprofile&amp;do=avatar', _KUNENA_MYPROFILE_MYAVATAR, _KUNENA_MYPROFILE_MYAVATAR,'nofollow'); ?>
<?php
    }
?>
            </td>
        </tr>

        <tr>
            <td class = "fb_myprofile_menu_staff">
                <span class = "fb_myprofile_menu_title"><?php echo _KUNENA_MYPROFILE_FORUM_SETTINGS; ?></span>
                <?php echo CKunenaLink::GetSefHrefLink(KUNENA_LIVEURLREL . '&amp;func=myprofile&amp;do=showset', _KUNENA_MYPROFILE_LOOK_AND_LAYOUT, _KUNENA_MYPROFILE_LOOK_AND_LAYOUT, 'follow'); ?>
                <?php echo CKunenaLink::GetSefHrefLink(KUNENA_LIVEURLREL . '&amp;func=myprofile&amp;do=profileinfo', _KUNENA_MYPROFILE_MY_PROFILE_INFO, _KUNENA_MYPROFILE_MY_PROFILE_INFO, 'follow'); ?>
                <?php echo CKunenaLink::GetSefHrefLink(KUNENA_LIVEURLREL . '&amp;func=myprofile&amp;do=showmsg', _KUNENA_MYPROFILE_MY_POSTS, _KUNENA_MYPROFILE_MY_POSTS,'follow'); ?>
				<?php echo CKunenaLink::GetSefHrefLink(KUNENA_LIVEURLREL . '&amp;func=myprofile&amp;do=showsub', _KUNENA_MYPROFILE_MY_SUBSCRIBES, _KUNENA_MYPROFILE_MY_SUBSCRIBES, 'follow'); ?>
				<?php echo CKunenaLink::GetSefHrefLink(KUNENA_LIVEURLREL . '&amp;func=myprofile&amp;do=showfav', _KUNENA_MYPROFILE_MY_FAVORITES, _KUNENA_MYPROFILE_MY_FAVORITES, 'follow'); ?>
            </td>
        </tr>

<?php
    // UddeIM
    if ($kunena_config->pm_component == 'uddeim')
    {
?>
        <tr>
            <td class = "fb_myprofile_menu_staff">
                <span class = "fb_myprofile_menu_title"><?php echo _KUNENA_MYPROFILE_PRIVATE_MESSAGING; ?></span>
            <?php echo CKunenaLink::GetSefHrefLink('index.php?option=com_uddeim&amp;Itemid=' . KUNENA_UIM_ITEMID , _KUNENA_MYPROFILE_INBOX, _KUNENA_MYPROFILE_INBOX, 'follow'); ?>
            <?php echo CKunenaLink::GetSefHrefLink('index.php?option=com_uddeim&amp;task=new&amp;Itemid=' . KUNENA_UIM_ITEMID , _KUNENA_MYPROFILE_NEW_MESSAGE, _KUNENA_MYPROFILE_NEW_MESSAGE, 'follow'); ?>
            <?php echo CKunenaLink::GetSefHrefLink('index.php?option=com_uddeim&amp;task=outbox&amp;Itemid=' . KUNENA_UIM_ITEMID , _KUNENA_MYPROFILE_OUTBOX, _KUNENA_MYPROFILE_OUTBOX, 'follow'); ?>
            <?php echo CKunenaLink::GetSefHrefLink('index.php?option=com_uddeim&amp;task=trashcan&amp;Itemid=' . KUNENA_UIM_ITEMID , _KUNENA_MYPROFILE_TRASH, _KUNENA_MYPROFILE_TRASH, 'follow'); ?>
            </td>
        </tr>
<?php
    }

    // MISSUS
    if ($kunena_config->pm_component == 'missus')
    {

?>
       <tr>
            <td class = "fb_myprofile_menu_staff">
                <span class = "fb_myprofile_menu_title"><?php echo _KUNENA_MYPROFILE_PRIVATE_MESSAGING; ?></span>
            <?php echo CKunenaLink::GetSefHrefLink('index.php?option=com_missus&amp;func=showinbox&amp;Itemid=' . KUNENA_MISSUS_ITEMID , _KUNENA_MYPROFILE_INBOX, _KUNENA_MYPROFILE_INBOX, 'follow'); ?>
            <?php echo CKunenaLink::GetSefHrefLink('index.php?option=com_missus&amp;func=newmsg&amp;Itemid=' . KUNENA_MISSUS_ITEMID , _KUNENA_MYPROFILE_NEW_MESSAGE, _KUNENA_MYPROFILE_NEW_MESSAGE, 'follow'); ?>
            <?php echo CKunenaLink::GetSefHrefLink('index.php?option=com_missus&amp;func=showsent&amp;Itemid=' . KUNENA_MISSUS_ITEMID ,  _KUNENA_MYPROFILE_OUTBOX,  _KUNENA_MYPROFILE_OUTBOX, 'follow'); ?>
            <?php echo CKunenaLink::GetSefHrefLink('index.php?option=com_missus&amp;func=showtrash&amp;Itemid=' . KUNENA_MISSUS_ITEMID , _KUNENA_MYPROFILE_TRASH, _KUNENA_MYPROFILE_TRASH, 'follow'); ?>
            <?php echo CKunenaLink::GetSefHrefLink('index.php?option=com_missus&amp;func=showcontacts&amp;Itemid=' . KUNENA_MISSUS_ITEMID , _KUNENA_MYPROFILE_CONTACTS, _KUNENA_MYPROFILE_CONTACTS, 'follow'); ?>
            <?php echo CKunenaLink::GetSefHrefLink('index.php?option=com_missus&amp;func=showconfig&amp;Itemid=' . KUNENA_MISSUS_ITEMID , _KUNENA_MYPROFILE_SETTINGS, _KUNENA_MYPROFILE_SETTINGS, 'follow'); ?>
            </td>
        </tr>
<?php
    }

    // JIM
    if ($kunena_config->pm_component == 'jim')
    {

?>
       <tr>
            <td class = "fb_myprofile_menu_staff">
                <span class = "fb_myprofile_menu_title"><?php echo _KUNENA_MYPROFILE_PRIVATE_MESSAGING; ?></span>
            <?php echo CKunenaLink::GetSefHrefLink('index.php?option=com_jim&amp;task=inbox', _KUNENA_MYPROFILE_INBOX, _KUNENA_MYPROFILE_INBOX, 'follow'); ?>
            <?php echo CKunenaLink::GetSefHrefLink('index.php?option=com_jim&amp;task=new' , _KUNENA_MYPROFILE_NEW_MESSAGE, _KUNENA_MYPROFILE_NEW_MESSAGE, 'follow'); ?>
            <?php echo CKunenaLink::GetSefHrefLink('index.php?option=com_jim&amp;task=outbox' , _KUNENA_MYPROFILE_OUTBOX, _KUNENA_MYPROFILE_OUTBOX, 'follow'); ?>
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