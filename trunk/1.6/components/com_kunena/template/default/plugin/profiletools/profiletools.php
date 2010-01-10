<?php
/**
* @version $Id: forumtools.php 1505 2010-01-07 00:40:32Z mahagr $
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
					<a href = "<?php echo JRoute::_(KUNENA_LIVEURLREL . '&amp;func=myprofile'); ?>"> <?php echo _KUNENA_VIEW_PROFILE; ?> </a>
                </li>
                <li>
					<a href = "<?php echo JRoute::_(KUNENA_LIVEURLREL . '&amp;func=myprofile&amp;do=profileinfo'); ?>"><?php echo _KUNENA_EDIT_PROFILE; ?></a>
				</li>
				<?php
 					// Only show userdetails link if we are in charge of the profile
    				if ($kunena_config->fb_profile == 'fb')
   					 {
				?>
            	<li><a href = "<?php echo JRoute::_(KUNENA_LIVEURLREL . '&amp;func=myprofile&amp;do=userdetails'); ?>"> <?php echo _KUNENA_EDIT_DETAILS; ?></a></li>
				<?php
				    }
				    // Only show avatar link if we are in charge of it
 				   if ($kunena_config->allowavatar && $kunena_config->avatar_src == 'fb')
 				   {
				?>
            		<li><a href = "<?php echo JRoute::_(KUNENA_LIVEURLREL . '&amp;func=myprofile&amp;do=avatar'); ?>"> <?php echo _KUNENA_CHOOSE_AVATAR; ?></a></li>
				<?php
				    }
				?>
               <li>
              		<a href = "<?php echo JRoute::_(KUNENA_LIVEURLREL . '&amp;func=myprofile&amp;do=showset'); ?>"><?php echo _KUNENA_FORUM_PREFERENCES; ?></a>
				</li>

				<li>
					<a href = "<?php echo JRoute::_(KUNENA_LIVEURLREL . '&amp;func=myprofile&amp;do=showmsg'); ?>"><?php echo _KUNENA_VIEW_MY_POSTS; ?></a>
				</li>
				<li>
					<a href = "<?php echo JRoute::_(KUNENA_LIVEURLREL . '&amp;func=myprofile&amp;do=showsub'); ?>"><?php echo _KUNENA_VIEW_MY_SUBSCRIPTIONS; ?></a>
				</li>
				<li>
					<a href = "<?php echo JRoute::_(KUNENA_LIVEURLREL . '&amp;func=myprofile&amp;do=showfav'); ?>"><?php echo _KUNENA_VIEW_MY_FAVORITES; ?></a>
               </li>
            </ul>
        </div>
    </div>
</div>