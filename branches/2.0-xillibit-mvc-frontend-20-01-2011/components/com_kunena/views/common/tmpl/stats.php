<?php
/**
* @version $Id: frontstats.php 4237 2011-01-19 16:04:04Z mahagr $
* Kunena Component
* @package Kunena
*
* @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.org
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

?>
<div class="kblock kfrontstats">
	<div class="kheader">
		<span class="ktoggler"><a class="ktoggler close" title="<?php echo JText::_('COM_KUNENA_TOGGLER_COLLAPSE') ?>" rel="kfrontstats-tbody"></a></span>
		<h2><span><?php echo CKunenaLink::GetStatsLink($this->config->board_title.' '.JText::_('COM_KUNENA_STAT_FORUMSTATS'), ''); ?></span></h2>
	</div>
	<div class="kcontainer" id="kfrontstats-tbody">
		<div class="kbody">
			<table class = "kblocktable" id ="kfrontstats">
				<tr class="krow1">
					<td class = "kcol-first">
						<div class="kstatsicon"></div>
					</td>
					<td class="kcol-mid km">
						<ul id="kstatslistright" class="fltrt kright">
							<li><?php echo JText::_('COM_KUNENA_STAT_TOTAL_USERS'); ?>: <strong><?php echo $this->userlist1; ?></strong> <span class="divider">|</span> <?php echo JText::_('COM_KUNENA_STAT_LATEST_MEMBERS'); ?>:<strong> <?php echo CKunenaLink::GetProfileLink($this->lastestmemberid); ?></strong></li>
							<li>&nbsp;</li>
							<li><?php echo $this->userlist2; ?></li>
							<li><?php if ($this->config->showpopuserstats || $this->config->showpopsubjectstats) echo CKunenaLink::GetStatsLink(JText::_('COM_KUNENA_STAT_MORE_ABOUT_STATS').' &raquo;');?></li>
						</ul>
						<ul id="kstatslistleft" class="fltlft">
							<li><?php echo JText::_('COM_KUNENA_STAT_TOTAL_MESSAGES'); ?>: <strong> <?php echo intval($this->totalmsgs); ?></strong> <span class="divider">|</span> <?php echo JText::_('COM_KUNENA_STAT_TOTAL_SUBJECTS'); ?>: <strong><?php echo intval($this->totaltitles); ?></strong></li>
							<li><?php echo JText::_('COM_KUNENA_STAT_TOTAL_SECTIONS'); ?>: <strong><?php echo intval($this->totalsections); ?></strong> <span class="divider">|</span> <?php echo JText::_('COM_KUNENA_STAT_TOTAL_CATEGORIES'); ?>: <strong><?php echo intval($this->totalcats); ?></strong></li>
							<li><?php echo JText::_('COM_KUNENA_STAT_TODAY_OPEN_THREAD'); ?>: <strong><?php echo intval($this->todayopen); ?></strong> <span class="divider">|</span> <?php echo JText::_('COM_KUNENA_STAT_YESTERDAY_OPEN_THREAD'); ?>: <strong><?php echo intval($this->yesterdayopen); ?></strong></li>
							<li><?php echo JText::_('COM_KUNENA_STAT_TODAY_TOTAL_ANSWER'); ?>: <strong><?php echo intval($this->todayanswer); ?></strong> <span class="divider">|</span> <?php echo JText::_('COM_KUNENA_STAT_YESTERDAY_TOTAL_ANSWER'); ?>: <strong><?php echo intval($this->yesterdayanswer); ?></strong></li>
						</ul>
					</td>
				</tr>
			</table>
		</div>
	</div>
</div>


