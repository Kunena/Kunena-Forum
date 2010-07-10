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
 **/
defined( '_JEXEC' ) or die();
JHTML::_('behavior.calendar');
JHTML::_('behavior.tooltip');
?>
<div id="kprofile-rightcoltop">
	<div class="kprofile-rightcol2">
<?php
	CKunenaTools::loadTemplate('/profile/socialbuttons.php');
?>
	</div>
	<div class="kprofile-rightcol1 kicon-profile">
		<ul>
			<li><span class="location"></span><strong><?php echo JText::_('COM_KUNENA_MYPROFILE_LOCATION'); ?>:</strong> <?php echo $this->locationlink; ?></li>
			<!--  The gender determines the suffix on the span class- gender-male & gender-female  -->
			<li><span class="gender-<?php echo $this->genderclass; ?>"></span><strong><?php echo JText::_('COM_KUNENA_MYPROFILE_GENDER'); ?>:</strong> <?php echo $this->gender; ?></li>
			<li class="bd"><span class="birthdate"></span><strong><?php echo JText::_('COM_KUNENA_MYPROFILE_BIRTHDATE'); ?>:</strong> <span title="<?php echo CKunenaTimeformat::showDate($this->profile->birthdate, 'ago', 'utc', 0); ?>"><?php echo CKunenaTimeformat::showDate($this->profile->birthdate, 'date', 'utc', 0); ?></span>
			<!--  <a href="#" title=""><span class="bday-remind"></span></a> -->
			</li>
		</ul>
	</div>
</div>

<div class="clrline"></div>
<div id="kprofile-rightcolbot">
	<div class="kprofile-rightcol2">
		<ul>
			<?php if ($this->config->showemail && (!$this->profile->hideEmail || CKunenaTools::isModerator($this->my->id))): ?><li><span class="email"></span><a href="mailto:<?php echo $this->escape($this->user->email); ?>"><?php echo $this->escape($this->user->email); ?></a></li><?php endif; ?>
			<?php // FIXME: we need a better way to add http/https ?>
			<li><span class="website"></span><a href="http://<?php echo $this->escape($this->profile->websiteurl); ?>" target="_blank"><?php echo KunenaParser::parseText($this->profile->websitename); ?></a></li>
		</ul>
	</div>
	<div class="kprofile-rightcol1">
		<h4><?php echo JText::_('COM_KUNENA_MYPROFILE_SIGNATURE'); ?></h4>
		<div class="kmsgsignature"><div><?php echo KunenaParser::parseBBCode($this->signature); ?></div></div>
	</div>

</div>

<div class="clrline"></div>

<div id="kprofile-tabs">
	<dl class="tabs">
		<dt class="open"><?php echo JText::_('COM_KUNENA_USERPOSTS'); ?></dt>
		<dd style="display: none;">
			<?php $this->displayUserPosts(); ?>
		</dd>
		<?php if($this->config->showthankyou) : ?>
		<dt class="closed"><?php echo JText::_('COM_KUNENA_THANK_YOU'); ?></dt>
		<dd style="display: none;">
			<?php $this->displayGotThankYou(); ?>
			<?php $this->displaySaidThankYou(); ?>
		</dd>
		<?php endif; ?>
		<?php if ($this->my->id == $this->user->id): ?>
		<!--
		<dt class="closed"><?php echo JText::_('COM_KUNENA_OWNTOPICS'); ?></dt>
		<dd style="display: none;">
			<?php //$this->displayOwnTopics(); ?>
		</dd>
		<dt class="closed"><?php echo JText::_('COM_KUNENA_USERTOPICS'); ?></dt>
		<dd style="display: none;">
			<?php //$this->displayUserTopics(); ?>
		</dd>
		-->
		<?php if ($this->config->allowsubscriptions) :?>
		<dt class="closed"><?php echo JText::_('COM_KUNENA_SUBSCRIPTIONS'); ?></dt>
		<dd style="display: none;">
			<?php $this->displayCategoriesSubscriptions(); ?>
			<?php $this->displaySubscriptions(); ?>
		</dd>
		<?php endif; ?>
		<?php if ($this->config->allowfavorites) : ?>
		<dt class="closed"><?php echo JText::_('COM_KUNENA_FAVORITES'); ?></dt>
		<dd style="display: none;">
			<?php $this->displayFavorites(); ?>
		</dd>
		<?php endif; ?>
		<?php endif;?>
		<?php if (CKunenaTools::isModerator($this->my->id) && $this->my->id == $this->profile->userid ): ?>
		<dt class="closed"><?php echo JText::_('COM_KUNENA_BAN_BANMANAGER'); ?></dt>
		<dd style="display: none;">
			<?php $this->displayBanManager(); ?>
		</dd>
		<?php endif;?>
		<?php if (CKunenaTools::isModerator($this->my->id) && $this->my->id != $this->user->id):?>
		<dt class="closed"><?php echo JText::_('COM_KUNENA_BAN_BANHISTORY'); ?></dt>
		<dd style="display: none;">
			<?php $this->displayBanHistory(); ?>
		</dd>
		<?php endif;?>
		<?php if ($this->canBan) : ?>
		<dt class="closed"><?php echo $this->banInfo->id ? JText::_('COM_KUNENA_BAN_EDIT') : JText::_('COM_KUNENA_BAN_NEW' ); ?></dt>
		<dd style="display: none;">
			<?php $this->displayBanUser(); ?>
	</dd>
	<?php endif; ?>
	</dl>
</div>
