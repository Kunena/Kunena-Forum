<?php
/**
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>
<ul id="profiletabs" class="shadetabs">
<li class="tk-profiletab-about tk-tips" title=" ::<?php echo JText::_('About'); ?>">
	<a href="#" rel="tcontent-about" class="selected tk-tips"><?php echo JText::_('About'); ?><br /><span></span></a>
</li>
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
<?php if ($this->me->isModerator() && $this->my->id == $this->profile->userid ): ?>
<li class="tk-profiletab-banm tk-tips" title=" ::<?php echo JText::_('COM_KUNENA_BAN_BANMANAGER'); ?>">
	<a href="#" rel="tcontent-banm"><?php echo JText::_('COM_KUNENA_BAN_BANMANAGER'); ?><br /><span></span></a></li>
<?php endif;?>
<?php if ($this->me->isModerator() && $this->my->id != $this->user->id):?>
<li class="tk-profiletab-banh tk-tips" title=" ::<?php echo JText::_('COM_KUNENA_BAN_BANHISTORY'); ?>">
	<a href="#" rel="tcontent-banh"><?php echo JText::_('COM_KUNENA_BAN_BANHISTORY'); ?><br /><span></span></a>
</li>
<?php endif;?>
<?php if ($this->canBan) : ?>
<li class="tk-profiletab-banu tk-tips" title=" ::<?php echo $this->banInfo->id ? JText::_('COM_KUNENA_BAN_EDIT') : JText::_('COM_KUNENA_BAN_NEW' ); ?>">
	<a href="#" rel="tcontent-banu"><?php echo $this->banInfo->id ? JText::_('COM_KUNENA_BAN_EDIT') : JText::_('COM_KUNENA_BAN_NEW' ); ?><br /><span></span></a>
</li>
<?php endif;?>
<?php // TODO : enable when Kunena updated ?>
<?php //if ( $this->canManageAttachs ): ?>
<?php if ($this->me->isModerator() || $this->my->id == $this->profile->userid ): ?>
<li class="tk-profiletab-edit-avatar tk-tips" title=" ::<?php echo JText::_('COM_KUNENA_MANAGE_ATTACHMENTS'); ?>">
	<a href="#" rel="tcontent-attachments"><?php echo JText::_('Attachhments'); ?><br /><span></span></a>
</li>
<?php endif;?>
<?php //endif;?>
<?php if ($this->me->isModerator()): ?>
<li class="tk-profiletab-review tk-tips" title=" ::<?php echo JText::_('COM_KUNENA_MESSAGE_ADMINISTRATION'); ?>">
	<a href="#" rel="tcontent-review"><?php echo JText::_('Review'); ?><br /><span></span></a>
</li>
<?php endif;?>

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
					<div class="kprofile-rightcol1 fltlft" style="margin-left:5px;">
						<h4><?php echo JText::_('COM_KUNENA_MYPROFILE_PERSONALTEXT'); ?></h4>
						<div class="tk-profile-personal-text"><?php echo KunenaHtmlParser::parseText($this->personalText); ?></div>
					</div>
					<div class="kprofile-rightcol1 fltrt">
						<h4><?php echo JText::_('COM_KUNENA_MYPROFILE_SIGNATURE'); ?></h4>
						<div class="msgsignature"><div><?php echo KunenaHtmlParser::parseBBCode($this->signature); ?></div></div>
					</div>
				</div>
				</li>
			</ul>
		<span class="corners-bottom"><span></span></span>
	</div>
</div>

<div class="forumlist tk-profile-sommary">
	<div class="catinner">
		<span class="corners-top"><span></span></span>
			<ul class="topiclist">
				<li class="header">
					<dl class="icon profilesommary">
						<dt>
							<span class="ktitle"><?php echo JText::_('Information and Contact'); ?></span>
						</dt>
					</dl>
				</li>
			</ul>
			<div class="kdetailsbox tk-profile-sommary" style="padding:10px;">
				<?php include 'default_summary.php'; ?>
			</div>
		<span class="corners-bottom"><span></span></span>
	</div>
</div>

</div>

<div id="tcontent-info" class="tabcontent">

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
<?php if ($this->me->isModerator() && $this->my->id == $this->profile->userid ): ?>
<div id="tcontent-banm" class="tabcontent">
<?php $this->displayBanManager(); ?>
</div>
<?php endif?>
<?php if ($this->me->isModerator() && $this->my->id != $this->user->id):?>
<div id="tcontent-banh" class="tabcontent">
<?php $this->displayBanHistory(); ?>
</div>
<?php endif?>
<?php if ($this->canBan) : ?>
<div id="tcontent-banu" class="tabcontent">
<?php $this->displayBanUser(); ?>
</div>
<?php endif?>
<?php //if ( $this->canManageAttachs ): ?>
<?php if ($this->me->isModerator() || $this->my->id == $this->profile->userid ): ?>
<div id="tcontent-attachments" class="tabcontent">
	<?php $this->displayAttachments(); ?>
</div>
<?php endif;?>
<?php //endif;?>
<?php if ($this->me->isModerator()): ?>
<div id="tcontent-review" class="tabcontent">
	<?php $this->displayUnapprovedPosts(); ?>
</div>
<?php endif;?>

</div>
<script type="text/javascript">
//<![CDATA[
var myprofile=new ddtabcontent("profiletabs")
myprofile.setpersist(true)
myprofile.setselectedClassTarget("link")
myprofile.init()
// ]]>
</script>