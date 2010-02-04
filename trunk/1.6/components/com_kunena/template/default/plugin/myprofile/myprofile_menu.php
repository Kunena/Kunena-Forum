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
?>
<div class="k_bt_cvr1">
<div class="k_bt_cvr2">
<div class="k_bt_cvr3">
<div class="k_bt_cvr4">
<div class="k_bt_cvr5">
<table class = "kblocktable" id = "kforumprofile" border = "0" cellspacing = "0" cellpadding = "0" width="100%">
    <thead>
        <tr>
            <th>
                <div class = "ktitle_cover">
                    <span class = "ktitle"><?php echo JText::_('COM_KUNENA_USER_PROFILE'); ?> <?php echo $this->kunena_username; ?></span>
                </div>
            </th>
        </tr>
    </thead>

    <tbody class = "kmyprofile_menu">

        <tr>
            <td class = "kmyprofile_menu_staff">
                <span class = "kmyprofile_menu_title"><?php echo JText::_('COM_KUNENA_MYPROFILE_PERSONAL_INFO'); ?></span>
                <?php echo CKunenaLink::GetSefHrefLink(KUNENA_LIVEURLREL . '&amp;func=myprofile', JText::_('COM_KUNENA_MYPROFILE_SUMMARY'), JText::_('COM_KUNENA_MYPROFILE_SUMMARY'),'nofollow'); ?>
<?php
 	// Only show userdetails link if we are in charge of the profile
    if ($kunena_config->fb_profile == 'fb')
    {
?>
            	<?php echo CKunenaLink::GetSefHrefLink(KUNENA_LIVEURLREL . '&amp;func=myprofile&amp;do=userdetails', JText::_('COM_KUNENA_EDIT_TITLE'), JText::_('COM_KUNENA_EDIT_TITLE'),'follow'); ?>
<?php
    }

    // Only show avatar link if we are in charge of it
    if ($kunena_config->allowavatar && $kunena_config->avatar_src == 'fb')
    {
?>
            	<?php echo CKunenaLink::GetSefHrefLink(KUNENA_LIVEURLREL . '&amp;func=myprofile&amp;do=avatar', JText::_('COM_KUNENA_MYPROFILE_MYAVATAR'), JText::_('COM_KUNENA_MYPROFILE_MYAVATAR'),'nofollow'); ?>
<?php
    }
?>
            </td>
        </tr>

        <tr>
            <td class = "kmyprofile_menu_staff">
                <span class = "kmyprofile_menu_title"><?php echo JText::_('COM_KUNENA_MYPROFILE_FORUM_SETTINGS'); ?></span>
                <?php echo CKunenaLink::GetSefHrefLink(KUNENA_LIVEURLREL . '&amp;func=myprofile&amp;do=showset', JText::_('COM_KUNENA_MYPROFILE_LOOK_AND_LAYOUT'), JText::_('COM_KUNENA_MYPROFILE_LOOK_AND_LAYOUT'), 'follow'); ?>
                <?php echo CKunenaLink::GetSefHrefLink(KUNENA_LIVEURLREL . '&amp;func=myprofile&amp;do=profileinfo', JText::_('COM_KUNENA_MYPROFILE_MY_PROFILE_INFO'), JText::_('COM_KUNENA_MYPROFILE_MY_PROFILE_INFO'), 'follow'); ?>
                <?php echo CKunenaLink::GetSefHrefLink(KUNENA_LIVEURLREL . '&amp;func=myprofile&amp;do=showmsg', JText::_('COM_KUNENA_MYPROFILE_MY_POSTS'), JText::_('COM_KUNENA_MYPROFILE_MY_POSTS'),'follow'); ?>
				<?php echo CKunenaLink::GetSefHrefLink(KUNENA_LIVEURLREL . '&amp;func=myprofile&amp;do=showsub', JText::_('COM_KUNENA_MYPROFILE_MY_SUBSCRIBES'), JText::_('COM_KUNENA_MYPROFILE_MY_SUBSCRIBES'), 'follow'); ?>
				<?php echo CKunenaLink::GetSefHrefLink(KUNENA_LIVEURLREL . '&amp;func=myprofile&amp;do=showfav', JText::_('COM_KUNENA_MYPROFILE_MY_FAVORITES'), JText::_('COM_KUNENA_MYPROFILE_MY_FAVORITES'), 'follow'); ?>
            </td>
        </tr>

<?php
    // UddeIM
    if ($kunena_config->pm_component == 'uddeim')
    {
?>
        <tr>
            <td class = "kmyprofile_menu_staff">
                <span class = "kmyprofile_menu_title"><?php echo JText::_('COM_KUNENA_MYPROFILE_PRIVATE_MESSAGING'); ?></span>
            <?php echo CKunenaLink::GetSefHrefLink('index.php?option=com_uddeim&amp;Itemid=' . KUNENA_UIM_ITEMID , JText::_('COM_KUNENA_MYPROFILE_INBOX'), JText::_('COM_KUNENA_MYPROFILE_INBOX'), 'follow'); ?>
            <?php echo CKunenaLink::GetSefHrefLink('index.php?option=com_uddeim&amp;task=new&amp;Itemid=' . KUNENA_UIM_ITEMID , JText::_('COM_KUNENA_MYPROFILE_NEW_MESSAGE'), JText::_('COM_KUNENA_MYPROFILE_NEW_MESSAGE'), 'follow'); ?>
            <?php echo CKunenaLink::GetSefHrefLink('index.php?option=com_uddeim&amp;task=outbox&amp;Itemid=' . KUNENA_UIM_ITEMID , JText::_('COM_KUNENA_MYPROFILE_OUTBOX'), JText::_('COM_KUNENA_MYPROFILE_OUTBOX'), 'follow'); ?>
            <?php echo CKunenaLink::GetSefHrefLink('index.php?option=com_uddeim&amp;task=trashcan&amp;Itemid=' . KUNENA_UIM_ITEMID , JText::_('COM_KUNENA_MYPROFILE_TRASH'), JText::_('COM_KUNENA_MYPROFILE_TRASH'), 'follow'); ?>
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