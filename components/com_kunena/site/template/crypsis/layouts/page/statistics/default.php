<?php
/**
 * Kunena Component
 * @package Kunena.Template.Crypsis
 * @subpackage Common
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>

<div class="well well-small"> <span><a class="ktoggler close" title="<?php echo JText::_('COM_KUNENA_TOGGLER_COLLAPSE') ?>" rel="kfrontstats-tbody"></a></span>
  <h2 class="page-header">
	<span>
		<?php echo $this->statisticsLink; ?>
	</span>
  </h2>
  <div class="row-fluid column-row">
    <div class="span12 column-item" >
      <ul id="kstatslistleft" class="fltlft span6" style="list-style-type:none;">
        <li><?php echo JText::_('COM_KUNENA_STAT_TOTAL_MESSAGES'); ?>: <strong> <?php echo intval($this->messageCount); ?></strong> <span class="divider">|</span> <?php echo JText::_('COM_KUNENA_STAT_TOTAL_SUBJECTS'); ?>: <strong><?php echo intval($this->topicCount); ?></strong></li>
        <li><?php echo JText::_('COM_KUNENA_STAT_TOTAL_SECTIONS'); ?>: <strong><?php echo intval($this->sectionCount); ?></strong> <span class="divider">|</span> <?php echo JText::_('COM_KUNENA_STAT_TOTAL_CATEGORIES'); ?>: <strong><?php echo intval($this->categoryCount); ?></strong></li>
        <li><?php echo JText::_('COM_KUNENA_STAT_TODAY_OPEN_THREAD'); ?>: <strong><?php echo $this->todayTopicCount; ?></strong> <span class="divider">|</span> <?php echo JText::_('COM_KUNENA_STAT_YESTERDAY_OPEN_THREAD'); ?>: <strong><?php echo intval($this->yesterdayTopicCount); ?></strong></li>
        <li><?php echo JText::_('COM_KUNENA_STAT_TODAY_TOTAL_ANSWER'); ?>: <strong><?php echo intval($this->todayReplyCount); ?></strong> <span class="divider">|</span> <?php echo JText::_('COM_KUNENA_STAT_YESTERDAY_TOTAL_ANSWER'); ?>: <strong><?php echo intval($this->yesterdayReplyCount); ?></strong></li>
      </ul>
      <ul id="kstatslistright" class="fltrt kright span6" style="list-style-type:none;">
        <li ><?php echo JText::_('COM_KUNENA_STAT_TOTAL_USERS'); ?>: <strong><?php echo $this->usercountLink; ?></strong> <span class="divider">|</span> <?php echo JText::_('COM_KUNENA_STAT_LATEST_MEMBERS'); ?>: <strong><?php echo $this->latestMemberLink ?></strong></li>
        <li>&nbsp;</li>
        <li><?php echo $this->userlistLink; ?></li>
        <li>
          <?php if ($this->config->showpopuserstats || $this->config->showpopsubjectstats) echo $this->moreLink; ?>
        </li>
      </ul>
    </div>
  </div>
</div>
