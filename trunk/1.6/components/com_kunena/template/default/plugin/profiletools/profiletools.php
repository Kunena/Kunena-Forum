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
defined( '_JEXEC' ) or die();

$kunena_config =& CKunenaConfig::getInstance();

$func = JString::strtolower ( JRequest::getCmd ( 'func', 'listcat' ) );
$catid = JRequest::getInt('catid', 0);
$id = JRequest::getInt('id', 0);
?>

<script type = "text/javascript">
    jQuery(document).ready(function()
    {
        jQuery("#jrftsw").click(function()
        {
            jQuery(".forumtools_contentBox").slideToggle("fast");
            return false;
        });
    });
</script>

<div id = "fb_ft-cover">
    <div id = "forumtools_control">
        <a href = "#" id = "jrftsw" class = "forumtools"><?php echo _KUNENA_PROFILE_OPTIONS;?></a>
    </div>

    <div class="forumtools_contentBox" id="box1">
        <div class="forumtools_content" id="subBox1">
            <ul>
                <li>
					<?php echo CKunenaLink::GetSefHrefLink(KUNENA_LIVEURLREL . '&amp;func=myprofile', _KUNENA_VIEW_PROFILE, _KUNENA_VIEW_PROFILE, 'follow'); ?>
                </li>
                <li>
					<?php echo CKunenaLink::GetSefHrefLink(KUNENA_LIVEURLREL . '&amp;func=myprofile&amp;do=profileinfo', _KUNENA_EDIT_PROFILE, _KUNENA_EDIT_PROFILE, 'follow'); ?>
				</li>
				<?php
 					// Only show userdetails link if we are in charge of the profile
    				if ($kunena_config->fb_profile == 'fb')
   					 {
				?>
            	<li><?php echo CKunenaLink::GetSefHrefLink(KUNENA_LIVEURLREL . '&amp;func=myprofile&amp;do=userdetails', _KUNENA_EDIT_DETAILS, _KUNENA_EDIT_DETAILS, 'follow'); ?></li>
				<?php
				    }
				    // Only show avatar link if we are in charge of it
 				   if ($kunena_config->allowavatar && $kunena_config->avatar_src == 'fb')
 				   {
				?>
            		<li><?php echo CKunenaLink::GetSefHrefLink(KUNENA_LIVEURLREL . '&amp;func=myprofile&amp;do=avatar', _KUNENA_CHOOSE_AVATAR, _KUNENA_CHOOSE_AVATAR,'follow'); ?></li>
				<?php
				    }
				?>
               <li>
              		<?php echo CKunenaLink::GetSefHrefLink(KUNENA_LIVEURLREL . '&amp;func=myprofile&amp;do=showset', _KUNENA_FORUM_PREFERENCES, _KUNENA_FORUM_PREFERENCES, 'follow'); ?>
				</li>

				<li>
					<?php echo CKunenaLink::GetSefHrefLink(KUNENA_LIVEURLREL . '&amp;func=myprofile&amp;do=showmsg', _KUNENA_VIEW_MY_POSTS, _KUNENA_VIEW_MY_POSTS, 'follow'); ?>
				</li>
				<li>
					<?php echo CKunenaLink::GetSefHrefLink(KUNENA_LIVEURLREL . '&amp;func=myprofile&amp;do=showsub', _KUNENA_VIEW_MY_SUBSCRIPTIONS, _KUNENA_VIEW_MY_SUBSCRIPTIONS, 'follow'); ?>
				</li>
				<li>
					<?php echo CKunenaLink::GetSefHrefLink(KUNENA_LIVEURLREL . '&amp;func=myprofile&amp;do=showfav', _KUNENA_VIEW_MY_FAVORITES, _KUNENA_VIEW_MY_FAVORITES, 'follow'); ?>
               </li>
            </ul>
        </div>
    </div>
</div>