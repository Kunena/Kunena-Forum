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
		<div class="kuserprofile">
			<h2 class="kheader"><a href="#" rel="kmod-detailsbox"><?php echo JText::_('COM_KUNENA_USER_PROFILE').' '.$this->escape($this->name) ?></a></h2>
			<div class="kdetailsbox kmod-userbox" id="kmod-detailsbox">
				<?php if ($this->avatarlink) : ?>
				<div id="kprofile-leftcol">
					<div class="kavatar-lg">
						<?php echo $this->avatarlink; ?>
					</div>
				</div>
				<?php endif; ?>
				<div id="kprofile-rightcol">

					<div id="kprofile-stats" class="clearfix">
						<ul>
							<?php if ( !empty($this->banReason) ) : ?><li><strong><?php echo JText::_('COM_KUNENA_MYPROFILE_BANINFO') ?>:</strong> <?php echo $this->escape($this->banReason) ?></li><?php endif ?>
							<li><span class="kicon-button kbuttononline-<?php echo $this->profile->isOnline('yes', 'no') ?>"><span class="online-<?php echo $this->profile->isOnline('yes', 'no') ?>"><span><?php echo $this->profile->isOnline(JText::_('COM_KUNENA_ONLINE'), JText::_('COM_KUNENA_OFFLINE')) ?></span></span></span></li>
							<?php if (!empty($this->usertype)): ?><li class="kprofile-usertype"><?php echo $this->escape($this->usertype); ?></li><?php endif; ?>
							<?php if (!empty($this->rank_title)): ?><li class="kprofile-ranktitle"><strong><?php echo JText::_('COM_KUNENA_MYPROFILE_RANK') ?>: </strong><?php echo $this->escape($this->rank_title); ?></li><?php endif; ?>
							<?php if (!empty($this->rank_image)): ?><li class="kprofile-rankimage"><?php echo $this->rank_image; ?></li><?php endif; ?>
						</ul>
						<ul>
							<?php if (!empty($this->registerdate)): ?><li><strong><?php echo JText::_('COM_KUNENA_MYPROFILE_REGISTERDATE') ?>:</strong> <?php echo KunenaDate::getInstance($this->registerdate)->toSpan('date_today', 'ago', 'utc') ?></li><?php endif; ?>
							<?php if (!empty($this->lastvisitdate)): ?><li><strong><?php echo JText::_('COM_KUNENA_MYPROFILE_LASTVISITDATE') ?>:</strong> <?php echo KunenaDate::getInstance($this->lastvisitdate)->toSpan('date_today', 'ago', 'utc') ?></li><?php endif; ?>
							<li><strong><?php echo JText::_('COM_KUNENA_MYPROFILE_TIMEZONE'); ?>:</strong> GMT <?php echo $this->localtime->toTimezone() ?></li>
							<li><strong><?php echo JText::_('COM_KUNENA_MYPROFILE_LOCAL_TIME'); ?>:</strong> <?php echo $this->localtime->toKunena('time') ?></li>
						</ul>
						<ul>
							<?php if (!empty($this->posts)): ?><li><strong><?php echo JText::_('COM_KUNENA_MYPROFILE_POSTS') ?>:</strong> <?php echo intval($this->posts) ?></li><?php endif; ?>
							<?php if (!empty($this->userpoints)): ?><li><strong><?php echo JText::_('COM_KUNENA_AUP_POINTS') ?></strong> <?php echo intval($this->userpoints) ?></li><?php endif; ?>
							<?php if (!empty($this->usermedals)) : ?><li><?php foreach ( $this->usermedals as $medal ) : echo $medal,' '; endforeach ?></li><?php endif ?>
							<li><strong><?php echo JText::_('COM_KUNENA_MYPROFILE_PROFILEVIEW') ?>:</strong> <?php echo intval($this->profile->uhits) ?></li>
							<li><?php echo $this->displayKarma(); ?></li>
							<?php if (!empty($this->pmLink)) : ?><li><?php echo $this->pmLink ?></li><?php endif ?>
						</ul>
						<?php if( !empty($this->personalText) ) : ?>
						<ul class="kuserprofile-about">
							<li><strong><?php echo JText::_('COM_KUNENA_MYPROFILE_ABOUTME') ?>:</strong> <?php echo KunenaHtmlParser::parseText($this->personalText) ?></li>
						</ul>
						<?php endif ?>
					</div>
					<div class="clrline"></div>
					<div id="kprofile-rightcoltop">
						<div class="kprofile-rightcol2">
							<div class="kiconrow">
								<?php echo $this->profile->socialButton('twitter', $this->showUnusedSocial) ?>
								<?php echo $this->profile->socialButton('facebook', $this->showUnusedSocial) ?>
								<?php echo $this->profile->socialButton('myspace', $this->showUnusedSocial) ?>
								<?php echo $this->profile->socialButton('linkedin', $this->showUnusedSocial) ?>
								<?php echo $this->profile->socialButton('skype', $this->showUnusedSocial) ?>
							</div>
							<div class="kiconrow">
								<?php echo $this->profile->socialButton('delicious', $this->showUnusedSocial) ?>
								<?php echo $this->profile->socialButton('friendfeed', $this->showUnusedSocial) ?>
								<?php echo $this->profile->socialButton('digg', $this->showUnusedSocial) ?>
							</div>
							<div class="clr"></div>
							<div class="kiconrow">
								<?php echo $this->profile->socialButton('yim', $this->showUnusedSocial) ?>
								<?php echo $this->profile->socialButton('aim', $this->showUnusedSocial) ?>
								<?php echo $this->profile->socialButton('gtalk', $this->showUnusedSocial) ?>
								<?php echo $this->profile->socialButton('icq', $this->showUnusedSocial) ?>
								<?php echo $this->profile->socialButton('msn', $this->showUnusedSocial) ?>
							</div>
							<div class="kiconrow">
								<?php echo $this->profile->socialButton('blogspot', $this->showUnusedSocial) ?>
								<?php echo $this->profile->socialButton('flickr', $this->showUnusedSocial) ?>
								<?php echo $this->profile->socialButton('bebo', $this->showUnusedSocial) ?>
							</div>
						</div>
						<div class="kprofile-rightcol1">
							<ul>
								<li><span class="kicon-profile kicon-profile-location"></span><strong><?php echo JText::_('COM_KUNENA_MYPROFILE_LOCATION') ?>:</strong> <?php echo $this->locationlink ?></li>
								<li><span class="kicon-profile kicon-profile-gender-unknown"></span><strong><?php echo JText::_('COM_KUNENA_MYPROFILE_GENDER') ?>:</strong> <?php echo $this->gender ?></li>
								<li><span class="kicon-profile kicon-profile-birthdate"></span><strong><?php echo JText::_('COM_KUNENA_MYPROFILE_BIRTHDATE') ?>:</strong> <?php echo KunenaDate::getInstance($this->profile->birthdate)->toKunena('date', 'ago', 'utc') ?></li>
							</ul>
						</div>
					</div>
					<div class="clrline"></div>
					<div id="kprofile-rightcolbot">
						<div class="kprofile-rightcol2">
							<ul>
								<?php if ($this->config->showemail && (!$this->profile->hideEmail || $this->me->isModerator())) : ?><li><span class="kicon-profile kicon-profile-email"></span><?php echo JHTML::_('email.cloak', $this->user->email) ?></li><?php endif; ?>
								<?php if (!empty($this->profile->websiteurl)):?><li><span class="kicon-profile kicon-profile-website"></span><a href="http://<?php echo $this->escape($this->profile->websiteurl) ?>" target="_blank"><?php echo KunenaHtmlParser::parseText($this->profile->websitename) ?></a></li><?php endif ?>
								<li>&nbsp;</li>
							</ul>
						</div>
						<?php if ($this->signature) : ?>
						<div class="kprofile-rightcol1">
							<h4><?php echo JText::_('COM_KUNENA_MYPROFILE_SIGNATURE') ?></h4>
							<div class="kmsgsignature">
								<div><?php echo KunenaHtmlParser::parseBBCode($this->signature) ?></div>
							</div>
						</div>
						<?php endif ?>
					</div>
				</div>
				<div class="clr"></div>


			<div class="clrline"></div>
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
						<?php if ($this->canBan) : ?>
						<dt class="closed" title="<?php echo $this->banInfo->id ? JText::_('COM_KUNENA_BAN_EDIT') : JText::_('COM_KUNENA_BAN_NEW' ) ?>"><?php echo $this->banInfo->id ? JText::_('COM_KUNENA_BAN_EDIT') : JText::_('COM_KUNENA_BAN_NEW' ) ?></dt>
						<dd style="display: none;">
							<?php $this->displayBanUser(); ?>
						</dd>
						<?php endif; ?>
					</dl>
				</div>

			</div>
			<div class="clr"></div>
		</div>