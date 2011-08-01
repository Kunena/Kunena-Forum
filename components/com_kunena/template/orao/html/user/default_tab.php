<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>
<?php /*?>
				<div id="kprofile-tabs">
					<dl class="tabs">
						<?php if ($this->me->isModerator()): ?>
						<dt class="closed" title="<?php echo JText::_('COM_KUNENA_MESSAGE_ADMINISTRATION') ?>"><?php echo JText::_('COM_KUNENA_MESSAGE_ADMINISTRATION') ?></dt>
						<dd style="display: none;">
							<?php $this->displayUnapprovedPosts(); ?>
						</dd>
						<?php endif; ?>
						<dt class="open" title="<?php echo JText::_('COM_KUNENA_USERPOSTS') ?>"><?php echo JText::_('COM_KUNENA_USERPOSTS') ?></dt>
						<dd style="display: none;">
							<?php $this->displayUserPosts(); ?>
						</dd>
						<?php if($this->config->showthankyou && $this->my->id != 0) : ?>
						<dt class="closed" title="<?php echo JText::_('COM_KUNENA_THANK_YOU') ?>"><?php echo JText::_('COM_KUNENA_THANK_YOU') ?></dt>
						<dd style="display: none;">
							<?php $this->displayGotThankYou(); ?>
							<?php $this->displaySaidThankYou(); ?>
						</dd>
						<?php endif; ?>
						<?php if ($this->my->id == $this->user->id): ?>
						<?php if ($this->config->allowsubscriptions) :?>
						<dt class="closed" title="<?php echo JText::_('COM_KUNENA_SUBSCRIPTIONS') ?>"><?php echo JText::_('COM_KUNENA_SUBSCRIPTIONS') ?></dt>
						<dd style="display: none;">
							<?php //$this->displayCategoriesSubscriptions(); ?>
							<?php $this->displaySubscriptions(); ?>
						</dd>
						<?php endif; ?>
						<?php if ($this->config->allowfavorites) : ?>
						<dt class="closed" title="<?php echo JText::_('COM_KUNENA_FAVORITES') ?>"><?php echo JText::_('COM_KUNENA_FAVORITES') ?></dt>
						<dd style="display: none;">
							<?php $this->displayFavorites(); ?>
						</dd>
						<?php endif; ?>
						<?php endif;?>
						<?php if ($this->me->isModerator() && $this->my->id == $this->profile->userid ): ?>
						<dt class="closed" title="<?php echo JText::_('COM_KUNENA_BAN_BANMANAGER') ?>"><?php echo JText::_('COM_KUNENA_BAN_BANMANAGER') ?></dt>
						<dd style="display: none;">
							<?php $this->displayBanManager(); ?>
						</dd>
						<?php endif;?>
						<?php if ($this->me->isModerator() && $this->my->id != $this->user->id):?>
						<dt class="closed" title="<?php echo JText::_('COM_KUNENA_BAN_BANHISTORY') ?>"><?php echo JText::_('COM_KUNENA_BAN_BANHISTORY') ?></dt>
						<dd style="display: none;">
							<?php $this->displayBanHistory(); ?>
						</dd>
						<?php endif;?>
						<?php if ($this->me->isModerator() || $this->my->id == $this->profile->userid ): ?>
						<dt class="closed" title="<?php echo JText::_('COM_KUNENA_MANAGE_ATTACHMENTS'); ?>"><?php echo JText::_('COM_KUNENA_MANAGE_ATTACHMENTS'); ?></dt>
						<dd style="display: none;">
							<?php $this->displayAttachments(); ?>
						</dd>
						<?php endif;?>
						<?php if ($this->canBan) : ?>
						<dt class="closed" title="<?php echo $this->banInfo->id ? JText::_('COM_KUNENA_BAN_EDIT') : JText::_('COM_KUNENA_BAN_NEW' ) ?>"><?php echo $this->banInfo->id ? JText::_('COM_KUNENA_BAN_EDIT') : JText::_('COM_KUNENA_BAN_NEW' ) ?></dt>
						<dd style="display: none;">
							<?php $this->displayBanUser(); ?>
						</dd>
						<?php endif; ?>
					</dl>
				</div>
<?php */?>


<ul id="profiletabs" class="shadetabs">
<li class="tk-profiletab-about tk-tips" title=" ::<?php echo JText::_('About'); ?>">
	<a href="#" rel="tcontent-about" class="selected tk-tips"><?php echo JText::_('About'); ?><br /><span></span></a>
</li>
<?php /*?>
<li class="tk-profiletab-info tk-tips" title=" ::<?php echo JText::_('Info'); ?>">
	<a href="#" rel="tcontent-info" class="selected tk-tips"><?php echo JText::_('Info'); ?><br /><span></span></a>
</li>
<?php */?>
<li class="tk-profiletab-posts tk-tips" title=" ::<?php echo JText::_('COM_KUNENA_USERPOSTS'); ?>">
	<a href="#" rel="tcontent-posts"><?php echo JText::_('COM_KUNENA_USERPOSTS'); ?><br /><span></span></a>
</li>
<?php if($this->config->showthankyou && $this->my->id != 0) : ?>
<li class="tk-profiletab-thankyou tk-tips" title=" ::<?php echo JText::_('COM_KUNENA_THANK_YOU'); ?>">
	<a href="#" rel="tcontent-thankyou"><?php echo JText::_('COM_KUNENA_THANK_YOU'); ?><br /><span></span></a>
</li>
<?php endif;?>
<?php if ($this->my->id == $this->user->id): ?>
<?php if ($this->config->allowsubscriptions) :?>
<li class="tk-profiletab-sub tk-tips" title=" ::<?php echo JText::_('COM_KUNENA_SUBSCRIPTIONS'); ?>">
	<a href="#" rel="tcontent-subs"><?php echo JText::_('COM_KUNENA_SUBSCRIPTIONS'); ?><br /><span></span></a>
</li>
<?php endif; ?>
<?php if ($this->config->allowfavorites) : ?>
<li class="tk-profiletab-fav tk-tips" title=" ::<?php echo JText::_('COM_KUNENA_FAVORITES'); ?>">
	<a href="#" rel="tcontent-fav"><?php echo JText::_('COM_KUNENA_FAVORITES'); ?><br /><span></span></a>
</li>
<?php endif;?>
<?php endif;?>
<?php //if (CKunenaTools::isModerator($this->my->id) && $this->my->id == $this->profile->userid ): ?>
<li class="tk-profiletab-banm tk-tips" title=" ::<?php echo JText::_('COM_KUNENA_BAN_BANMANAGER'); ?>">
	<a href="#" rel="tcontent-banm"><?php echo JText::_('COM_KUNENA_BAN_BANMANAGER'); ?><br /><span></span></a></li>
<?php //endif;?>
<?php //if (CKunenaTools::isModerator($this->my->id) && $this->my->id != $this->user->id):?>
<li class="tk-profiletab-banh tk-tips" title=" ::<?php echo JText::_('COM_KUNENA_BAN_BANHISTORY'); ?>">
	<a href="#" rel="tcontent-banh"><?php echo JText::_('COM_KUNENA_BAN_BANHISTORY'); ?><br /><span></span></a>
</li>
<?php //endif;?>
<?php if ($this->canBan) : ?>
<li class="tk-profiletab-banu tk-tips" title=" ::<?php echo $this->banInfo->id ? JText::_('COM_KUNENA_BAN_EDIT') : JText::_('COM_KUNENA_BAN_NEW' ); ?>">
	<a href="#" rel="tcontent-banu"><?php echo $this->banInfo->id ? JText::_('COM_KUNENA_BAN_EDIT') : JText::_('COM_KUNENA_BAN_NEW' ); ?><br /><span></span></a>
</li>
<?php endif;?>
<?php if ($this->me->isModerator() || $this->my->id == $this->profile->userid ): ?>
<li class="tk-profiletab-edit-avatar tk-tip" title=" ::<?php echo JText::_('COM_KUNENA_MANAGE_ATTACHMENTS'); ?>">
	<a href="#" rel="tcontent-edit-avatar"><?php echo JText::_('Attachhments'); ?><br /><span></span></a>
</li>
<?php endif;?>
<?php if ($this->me->isModerator()): ?>
<li class="tk-profiletab-review tk-tips" title=" ::<?php echo JText::_('COM_KUNENA_MESSAGE_ADMINISTRATION'); ?>">
	<a href="#" rel="tcontent-review"><?php echo JText::_('Review'); ?><br /><span></span></a>
</li>
<?php endif;?>
<?php /*if (!empty($this->editlink)):?>
<li class="tk-profiletab-edit tk-tips" style="float:right;" title=" ::<?php echo JText::_('Edit your Profile'); ?>" class="tk-tips"><?php //echo $this->editlink?></li>
<?php endif;*/?>

</ul>
<div style="border:0px solid gray; margin-bottom: 1em; padding: 10px 0 0px;clear:both;">

<div id="tcontent-about" class="tabcontent">
<div class="forumlist tk-profile-sommary">
	<div class="catinner">
		<span class="corners-top"><span></span></span>
			<ul class="topiclist">
				<li class="header">
					<dl class="icon profilesommary">
						<dt>
							<span class="ktitle">
							<?php echo $this->name; ?></span>
						</dt>
					</dl>
				</li>
			</ul>
			<ul class="topiclist forums">
				<li class="rowfull">
	<div id="kprofile-rightcolbot">
		<div class="kprofile-rightcol1 fltlft">
			<h4><?php echo JText::_('COM_KUNENA_MYPROFILE_PERSONALTEXT'); ?></h4>
			<div class="tk-profile-personal-text"><?php //echo KunenaParser::parseText($this->personalText); ?></div>
		</div>
		<div class="kprofile-rightcol1 fltrt">
			<h4><?php echo JText::_('COM_KUNENA_MYPROFILE_SIGNATURE'); ?></h4>
			<div class="msgsignature"><div><?php //echo KunenaParser::parseBBCode($this->signature); ?></div></div>
		</div>
	</div>
					</li>
				</ul>
			<div class="body-bottom1"><div class="body-bottom2"><div class="body-bottom3"><div class="body-bottom4"></div></div></div></div>
		<span class="corners-bottom"><span></span></span>
	</div>
</div>
</div>
<div id="tcontent-info" class="tabcontent">
<div class="forumlist tk-profile-sommary">
	<div class="catinner">
		<span class="corners-top"><span></span></span>
			<ul class="topiclist">
				<li class="header">
					<dl class="icon profilesommary">
						<dt>
							<span class="ktitle">
							<?php echo JText::_('Information and Contact'); ?></span>
						</dt>
					</dl>
				</li>
			</ul>
			<ul class="topiclist forums">
				<li class="rowfull">
							<ul>
								<?php if (!empty($this->registerdate)): ?>
								<li>
									<strong><?php echo JText::_('COM_KUNENA_MYPROFILE_REGISTERDATE'); ?>:</strong>
									<span title="<?php //echo CKunenaTimeformat::showDate($this->registerdate, 'ago', 'utc'); ?>"><?php// echo CKunenaTimeformat::showDate($this->registerdate, 'date_today', 'utc'); ?></span>
								</li>
								<?php endif; ?>
								<?php if (!empty($this->lastvisitdate)): ?>
								<li>
									<strong><?php echo JText::_('COM_KUNENA_MYPROFILE_LASTVISITDATE'); ?>:</strong>
									<span title="<?php //echo CKunenaTimeformat::showDate($this->lastvisitdate, 'ago', 'utc'); ?>"><?php //echo CKunenaTimeformat::showDate($this->lastvisitdate, 'date_today', 'utc'); ?></span>
								</li>
								<?php endif; ?>
							</ul>
						<ul>
							<li>
								<span class="location"></span><strong><?php echo JText::_('COM_KUNENA_MYPROFILE_LOCATION'); ?>:</strong> <?php echo $this->locationlink; ?>
							</li>
							<!--  The gender determines the suffix on the span class- gender-male & gender-female  -->
							<li>
								<span class="gender-<?php echo $this->genderclass; ?>"></span><strong><?php echo JText::_('COM_KUNENA_MYPROFILE_GENDER'); ?>:</strong> <?php echo $this->gender; ?>
							</li>
							<li>
								<span class="birthdate"></span><strong><?php echo JText::_('COM_KUNENA_MYPROFILE_BIRTHDATE'); ?>:</strong>
								<span title="<?php //echo CKunenaTimeformat::showDate($this->profile->birthdate, 'ago', 'utc', 0); ?>"><?php //echo CKunenaTimeformat::showDate($this->profile->birthdate, 'date', 'utc', 0); ?></span>
							<!--  <a href="#" title=""><span class="bday-remind"></span></a> -->
							</li>
							<?php if ($this->config->showemail && (!$this->profile->hideEmail /*|| CKunenaTools::isModerator($this->my->id)*/)): ?>
							<li>
								<span class="email"></span>
								<strong><?php echo JText::_('COM_KUNENA_GEN_EMAIL'); ?>:</strong> <a href="mailto:<?php echo $this->escape($this->user->email); ?>"><?php echo $this->escape($this->user->email); ?></a>
							</li>
							<?php endif; ?>
							<li>
								<span class="website"></span>
								<strong><?php echo JText::_('COM_KUNENA_TEMPLATE_WEB_SITE'); ?>:</strong> <a href="http://<?php echo $this->escape($this->profile->websiteurl); ?>" target="_blank"><?php //echo KunenaParser::parseText($this->profile->websitename); ?></a>
							</li>
							<li><?php echo $this->displayKarma(); ?></li>
					<?php //if ($PMlink) {
							?>
						<li><?php// echo $PMlink; ?></li>
					<?php // } ?>
						</ul><br />

						<div class="social-buttons">
							<?php //CKunenaTools::loadTemplate('/profile/socialbuttons.php'); ?>
						</div>
					</li>
				</ul>
			<div class="body-bottom1"><div class="body-bottom2"><div class="body-bottom3"><div class="body-bottom4"></div></div></div></div>
		<span class="corners-bottom"><span></span></span>
	</div>
</div>

</div>
<div id="tcontent-posts" class="tabcontent">
<?php $this->displayUserPosts(); ?>
</div>
<?php if($this->config->showthankyou && $this->my->id != 0) : ?>
<div id="tcontent-thankyou" class="tabcontent">
<?php $this->displayGotThankYou(); ?>
<?php $this->displaySaidThankYou(); ?>
</div>
<?php endif;?>
<?php if ($this->my->id == $this->user->id): ?>
<?php if ($this->config->allowsubscriptions) :?>
<div id="tcontent-subs" class="tabcontent">
<?php $this->displaySubscriptions(); ?>
<?php $this->displayCategoriesSubscriptions(); ?>
</div>
<?php endif; ?>
<?php if ($this->config->allowfavorites) : ?>
<div id="tcontent-fav" class="tabcontent">
<?php $this->displayFavorites(); ?>
</div>
<?php endif;?>
<?php endif;?>
<?php //if (CKunenaTools::isModerator($this->my->id) && $this->my->id == $this->profile->userid ): ?>
<div id="tcontent-banm" class="tabcontent">
<?php $this->displayBanManager(); ?>
</div>
<?php //endif?>
<?php //if (CKunenaTools::isModerator($this->my->id) && $this->my->id != $this->user->id):?>
<div id="tcontent-banh" class="tabcontent">
<?php $this->displayBanHistory(); ?>
</div>
<?php //endif?>
<?php if ($this->canBan) : ?>
<div id="tcontent-banu" class="tabcontent">
<?php $this->displayBanUser(); ?>
</div>
<?php endif?>
<?php //if (CKunenaTools::isModerator($this->my->id)): ?>
<div id="tcontent-review" class="tabcontent">
<?php //$this->displayReviewPosts(); ?>
</div>
<?php //endif;?>

</div>
<script type="text/javascript">
//<![CDATA[
var myprofile=new ddtabcontent("profiletabs")
myprofile.setpersist(true)
myprofile.setselectedClassTarget("link")
myprofile.init()
// ]]>
</script>