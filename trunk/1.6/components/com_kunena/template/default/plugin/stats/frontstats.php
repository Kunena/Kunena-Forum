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
$forumurl = JRoute::_(KUNENA_LIVEURLREL);
$statslink = JRoute::_(KUNENA_LIVEURLREL.'&amp;func=stats');

if ($kunena_config->fb_profile == "jomsocial")
{
	$userlist = JRoute::_('index.php?option=com_community&amp;view=search&amp;task=browse');
}
else if ($kunena_config->fb_profile == 'cb')
{
    $userlist = CKunenaCBProfile::getUserListURL();
}
else
{
    $userlist = JRoute::_(KUNENA_LIVEURLREL . '&amp;func=userlist');
}

if ($this->showgenstats > 0)
{
	$this->loadGenStats();

    	?>
        <!-- BEGIN: GENERAL STATS -->
<div class="fb__bt_cvr1">
<div class="fb__bt_cvr2">
<div class="fb__bt_cvr3">
<div class="fb__bt_cvr4">
<div class="fb__bt_cvr5">
        <table  class = "fb_blocktable" id ="fb_frontstats" border = "0" cellspacing = "0" cellpadding = "0" width="100%">
            <thead>
                <tr>
                    <th align="left">
                        <div class = "fb_title_cover fbm">
                            <a class="fb_title fbl" href = "<?php echo $statslink;?>"><?php echo stripslashes($kunena_config->board_title); ?> <?php echo _STAT_FORUMSTATS; ?></a>
                        </div>
                        <div class="fltrt">
							<span id="kstats_status"><span class="close"></span></span>
							<a id="kstats_toggle" href="#"></a>
						</div>
                        <!--  <img id = "BoxSwitch_frontstats__frontstats_tbody" class = "hideshow" src = "<?php echo KUNENA_URLIMAGESPATH . 'shrink.gif' ; ?>" alt = ""/> -->
                    </th>
                </tr>
            </thead>

            <tbody id="frontstats_tbody">
                <tr class="fb_sectiontableentry1">
                    <td class="td-1 fbm">
                    	<ul id="statslistright" class="fltrt right">
                    		<li><?php echo _STAT_TOTAL_USERS; ?>: <strong><a href="<?php echo $userlist;?>"><?php echo $this->totalmembers; ?></a></strong> | <?php echo _STAT_LATEST_MEMBERS; ?>:<strong> <?php echo CKunenaLink::GetProfileLink($kunena_config, $this->lastestmemberid, $this->lastestmember, $rel='nofollow'); ?></strong></li>
                    		<li>&nbsp;</li>
                    		<li><a href="<?php echo $userlist;?>"><?php echo _STAT_USERLIST; ?> &raquo;</a></li>
                    		<li><?php if ($kunena_config->showpopuserstats || $kunena_config->showpopsubjectstats) echo '<a href = "'.$statslink.'">'. _STAT_MORE_ABOUT_STATS.' &raquo;</a>'; ?></li>
                    	</ul>
                    	<ul id="statslistleft" class="fltlft">
                    		<li><?php echo _STAT_TOTAL_MESSAGES; ?>: <strong> <?php echo $this->totalmsgs; ?></strong> | <?php echo _STAT_TOTAL_SUBJECTS; ?>: <strong><?php echo $this->totaltitles; ?></strong></li>
                    		<li><?php echo _STAT_TOTAL_SECTIONS; ?>: <strong><?php echo $this->totalcats; ?></strong> | <?php echo _STAT_TOTAL_CATEGORIES; ?>: <strong><?php echo $this->totalsections; ?></strong></li>
                    		<li><?php echo _STAT_TODAY_OPEN_THREAD; ?>: <strong><?php echo $this->todayopen; ?></strong> | <?php echo _STAT_YESTERDAY_OPEN_THREAD; ?>: <strong><?php echo $this->yesterdayopen; ?></strong></li>
                    		<li><?php echo _STAT_TODAY_TOTAL_ANSWER; ?>: <strong><?php echo $this->todayanswer; ?></strong> | <?php echo _STAT_YESTERDAY_TOTAL_ANSWER; ?>: <strong><?php echo $this->yesterdayanswer; ?></strong></li>
                    	</ul>
                    </td>
                </tr>
            </tbody>
        </table>
        </div>
</div>
</div>
</div>
</div>
<!-- FINISH: GENERAL STATS -->

<?php
}

