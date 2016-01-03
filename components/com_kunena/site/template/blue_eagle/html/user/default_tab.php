<?php
/**
 * Kunena Component
 * @package Kunena.Template.Blue_Eagle
 * @subpackage User
 *
 * @copyright (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

JHtml::_('behavior.calendar');
JHtml::_('behavior.tooltip');
?>
<div id="kprofile-rightcoltop">
	<div class="kprofile-rightcol2">
<?php
	$this->displayTemplateFile('user', 'default', 'social');
?>
	</div>
	<div class="kprofile-rightcol1">
		<ul>
			<li><span class="kicon-profile kicon-profile-location"></span><strong><?php echo JText::_('COM_KUNENA_MYPROFILE_LOCATION'); ?>:</strong> <?php echo $this->locationlink; ?></li>
			<!--  The gender determines the suffix on the span class- gender-male & gender-female  -->
			<li><span class="kicon-profile kicon-profile-gender-<?php echo $this->genderclass; ?>"></span><strong><?php echo JText::_('COM_KUNENA_MYPROFILE_GENDER'); ?>:</strong> <?php echo $this->gender; ?></li>
			<li class="bd"><span class="kicon-profile kicon-profile-birthdate"></span><strong><?php echo JText::_('COM_KUNENA_MYPROFILE_BIRTHDATE'); ?>:</strong> <span title="<?php echo KunenaDate::getInstance($this->profile->birthdate)->toKunena('ago', 'GMT'); ?>"><?php echo KunenaDate::getInstance($this->profile->birthdate)->toKunena('date', 'GMT'); ?></span>
			<!--  <a href="#" title=""><span class="bday-remind"></span></a> -->
			</li>
		</ul>
	</div>
</div>

<div class="clrline"></div>
<div id="kprofile-rightcolbot">
	<div class="kprofile-rightcol2">
		<?php if ($this->email || !empty($this->profile->websiteurl)): ?>
			<ul>
				<?php if ($this->email): ?>
					<li><span class="kicon-profile kicon-profile-email"></span><?php echo $this->email; ?></li>
				<?php endif; ?>
				<?php if (!empty($this->profile->websiteurl)): ?>
					<?php // FIXME: we need a better way to add http/https ?>
					<li><span class="kicon-profile kicon-profile-website"></span><a href="<?php echo $this->escape($this->websiteurl); ?>" target="_blank"><?php echo KunenaHtmlParser::parseText(trim($this->profile->websitename) ? $this->profile->websitename : $this->websiteurl); ?></a></li>
				<?php endif; ?>
			</ul>
		<?php endif;?>
	</div>
	<div class="kprofile-rightcol1">
		<h4><?php echo JText::_('COM_KUNENA_MYPROFILE_SIGNATURE'); ?></h4>
		<div class="kmsgsignature"><div><?php echo $this->signatureHtml ?></div></div>
	</div>

</div>

<div class="clrline"></div>

<div id="kprofile-tabs">
	<dl class="tabs">
		<?php if($this->showUserPosts) : ?>
		<dt class="open" title="<?php echo JText::_('COM_KUNENA_USERPOSTS'); ?>"><?php echo JText::_('COM_KUNENA_USERPOSTS'); ?></dt>
		<dd style="display: none;">
			<?php $this->displayUserPosts(); ?>
		</dd>
		<?php endif; ?>
		<?php if ($this->showSubscriptions) :?>
		<dt class="closed" title="<?php echo JText::_('COM_KUNENA_SUBSCRIPTIONS'); ?>"><?php echo JText::_('COM_KUNENA_SUBSCRIPTIONS'); ?></dt>
		<dd style="display: none;">
			<?php $this->displayCategoriesSubscriptions(); ?>
			<?php $this->displaySubscriptions(); ?>
		</dd>
		<?php endif; ?>
		<?php if ($this->showFavorites) : ?>
		<dt class="closed" title="<?php echo JText::_('COM_KUNENA_FAVORITES'); ?>"><?php echo JText::_('COM_KUNENA_FAVORITES'); ?></dt>
		<dd style="display: none;">
			<?php $this->displayFavorites(); ?>
		</dd>
		<?php endif; ?>
		<?php if($this->showThankyou) : ?>
		<dt class="closed" title="<?php echo JText::_('COM_KUNENA_THANK_YOU'); ?>"><?php echo JText::_('COM_KUNENA_THANK_YOU'); ?></dt>
		<dd style="display: none;">
			<?php $this->displayGotThankyou(); ?>
			<?php $this->displaySaidThankyou(); ?>
		</dd>
		<?php endif; ?>
		<?php if ($this->showUnapprovedPosts): ?>
		<dt class="open" title="<?php echo JText::_('COM_KUNENA_MESSAGE_ADMINISTRATION'); ?>"><?php echo JText::_('COM_KUNENA_MESSAGE_ADMINISTRATION'); ?></dt>
		<dd style="display: none;">
			<?php $this->displayUnapprovedPosts(); ?>
		</dd>
		<?php endif; ?>
		<?php if ($this->showAttachments): ?>
		<dt class="closed" title="<?php echo JText::_('COM_KUNENA_MANAGE_ATTACHMENTS'); ?>"><?php echo JText::_('COM_KUNENA_MANAGE_ATTACHMENTS'); ?></dt>
		<dd style="display: none;">
			<?php $this->displayAttachments(); ?>
		</dd>
		<?php endif;?>
		<?php if ($this->showBanManager): ?>
		<dt class="closed" title="<?php echo JText::_('COM_KUNENA_BAN_BANMANAGER'); ?>"><?php echo JText::_('COM_KUNENA_BAN_BANMANAGER'); ?></dt>
		<dd style="display: none;">
			<?php $this->displayBanManager(); ?>
		</dd>
		<?php endif;?>
		<?php if ($this->showBanHistory):?>
		<dt class="closed" title="<?php echo JText::_('COM_KUNENA_BAN_BANHISTORY'); ?>"><?php echo JText::_('COM_KUNENA_BAN_BANHISTORY'); ?></dt>
		<dd style="display: none;">
			<?php $this->displayBanHistory(); ?>
		</dd>
		<?php endif;?>
		<?php if ($this->showBanUser) : ?>
		<dt class="closed" title="<?php echo $this->banInfo->id ? JText::_('COM_KUNENA_BAN_EDIT') : JText::_('COM_KUNENA_BAN_NEW' ); ?>"><?php echo $this->banInfo->id ? JText::_('COM_KUNENA_BAN_EDIT') : JText::_('COM_KUNENA_BAN_NEW' ); ?></dt>
		<dd style="display: none;">
			<?php $this->displayBanUser(); ?>
	</dd>
	<?php endif; ?>
	</dl>
</div>
