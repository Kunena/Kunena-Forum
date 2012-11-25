<?php
/**
 * Kunena Component
 * @package Kunena.Template.Mirage
 * @subpackage User
 *
 * @copyright (C) 2008 - 2012 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>
<div class="kuserlist-user">
	<?php if (!empty($this->action)) echo $this->action ?>
	<h3 class="kuserlist-fullname"><?php echo $this->user->getLink() ?></h3>
	<?php if ($this->config->userlist_avatar) : ?>
	<div class="kuserlist-avatar">
		<?php echo $this->user->getLink($this->user->getAvatarImage('', 'profile')) ?>
		<?php if ($this->config->userlist_online) : ?>
		<span class="kdetails-status k<?php echo $this->user->isOnline('online', 'offline') ?>"><?php echo $this->user->isOnline(JText::_('COM_KUNENA_ONLINE'), JText::_('COM_KUNENA_OFFLINE')) ?></span>
		<?php endif ?>
	<?php endif ?>
	</div>
	<ul class="kuserlist-details">
		<?php if (!$this->config->username) : ?>
		<li class="kdetails-username"><span><?php echo JText::_('COM_KUNENA_USERNAME') ?>:</span> <?php echo $this->escape($this->user->username) ?></li>
		<?php else : ?>
		<li class="kdetails-username"><span><?php echo JText::_('COM_KUNENA_REALNAME') ?>:</span> <?php echo $this->escape($this->user->name) ?></li>
		<?php endif; ?>
		<?php if (!empty($this->email)) : ?>
		<li class="kdetails-joindate"><span><?php echo JText::_('COM_KUNENA_USRL_EMAIL') ?>:</span> <?php echo $this->email ?></li>
		<?php endif; ?>
		<?php if ($this->config->userlist_usertype) : ?>
		<li class="kdetails-usertype"><span><?php echo JText::_('COM_KUNENA_USRL_USERTYPE') ?>:</span> <?php echo JText::_($this->user->getType()) ?></li>
		<?php endif; ?>
		<?php if ($this->config->userlist_joindate) : ?>
		<li class="kdetails-usertype"><span><?php echo JText::_('COM_KUNENA_USRL_JOIN_DATE') ?>:</span> <?php echo KunenaDate::getInstance($this->user->registerDate)->toSpan('date_today', 'ago') ?></li>
		<?php endif; ?>
		<?php if ($this->config->userlist_lastvisitdate) : ?>
		<li class="kdetails-usertype"><span><?php echo JText::_('COM_KUNENA_USRL_LAST_LOGIN') ?>:</span> <?php echo KunenaDate::getInstance($this->user->lastvisitDate)->toSpan('date_today', 'ago') ?></li>
		<?php endif; ?>
		<?php if ($this->config->userlist_posts) : ?>
		<li class="kdetails-posts"><span><?php echo JText::_('COM_KUNENA_USRL_POSTS') ?>:</span> <?php echo intval($this->user->posts); ?></li>
		<?php endif; ?>
		<?php if ($this->config->userlist_userhits) : ?>
		<li class="kdetails-posts"><span><?php echo JText::_('COM_KUNENA_USRL_HITS') ?>:</span> <?php echo intval($this->user->uhits) ?></li>
		<?php endif; ?>
		<?php if ($this->config->userlist_karma) : ?>
		<li class="kdetails-karma"><span><?php echo JText::_('COM_KUNENA_USRL_KARMA') ?>:</span> <?php echo intval($this->user->karma); ?></li>
		<?php endif; ?>
		<?php if (!empty($this->user->websiteurl)):?>
		<li class="kdetails-website"><span><?php echo JText::_('COM_KUNENA_MYPROFILE_WEBSITE') ?>:</span> <a href="http://<?php echo $this->escape($this->user->websiteurl); ?>" target="_blank"><?php echo KunenaHtmlParser::parseText($this->user->websitename ? $this->user->websitename : $this->user->websiteurl); ?></a></li>
		<?php endif;?>
		<?php if (!empty($this->rank_title)) : ?>
		<li class="kdetails-rank"><span><?php echo JText::_('COM_KUNENA_MYPROFILE_RANK') ?>:</span> <?php echo $this->rank_title ?></li>
		<?php endif ?>
		<?php if (!empty($this->rank_image)) : ?>
		<li class="kdetails-rankimg"><?php echo $this->rank_image ?></li>
		<?php endif ?>
		<li>
			<ul class="kdetails-links">
				<?php echo $this->user->socialButton('twitter'); ?>
				<?php echo $this->user->socialButton('facebook'); ?>
				<?php echo $this->user->socialButton('myspace'); ?>
				<?php echo $this->user->socialButton('linkedin'); ?>
				<?php echo $this->user->socialButton('skype'); ?>
				<?php echo $this->user->socialButton('delicious'); ?>
				<?php echo $this->user->socialButton('friendfeed'); ?>
				<?php echo $this->user->socialButton('digg'); ?>
				<?php echo $this->user->socialButton('yim'); ?>
				<?php echo $this->user->socialButton('aim'); ?>
				<?php echo $this->user->socialButton('gtalk'); ?>
				<?php echo $this->user->socialButton('icq'); ?>
				<?php echo $this->user->socialButton('msn'); ?>
				<?php echo $this->user->socialButton('blogspot'); ?>
				<?php echo $this->user->socialButton('flickr'); ?>
				<?php echo $this->user->socialButton('bebo'); ?>
			</ul>
		</li>
	</ul>
</div>