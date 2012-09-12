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
<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=user&layout=list') ?>" method="post" id="adminForm" name="adminForm">
	<input type="hidden" name="filter_order" value="<?php echo $this->state->get('list.ordering'); ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->state->get('list.direction'); ?>" />
	<?php echo JHtml::_( 'form.token' ); ?>
	<ul class="user-list list-unstyled list-header">
		<li class="header kbox-hover_header-row">
			<dl class="user-list list-unstyled">
				<dd class="user-id">
					<div class="innerspacer-header">
						<span class="bold"><a>#</a></span>
					</div>
				</dd>
				<?php if ($this->config->userlist_online) : ?>
					<dd class="user-online">
						<div class="innerspacer-header">
							<span class="bold"><a><?php echo JText::_('COM_KUNENA_USRL_ONLINE') ?></a></span>
						</div>
					</dd>
				<?php endif; ?>
				<?php if ($this->config->userlist_avatar) : ?>
					<dd class="user-avatar">
						<div class="innerspacer-header">
							<span class="bold"><a><?php echo JText::_('COM_KUNENA_USRL_AVATAR') ?></a></span>
						</div>
					</dd>
				<?php endif; ?>
				<?php if ($this->config->username) : ?>
					<dd class="user-username">
						<div class="innerspacer-header">
							<span class="bold"><?php echo JHtml::_( 'grid.sort', 'COM_KUNENA_USERNAME', 'username', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?></span>
						</div>
					</dd>
				<?php else : ?>
					<dd class="user-realname">
						<div class="innerspacer-header">
							<span class="bold"><?php echo JHtml::_( 'grid.sort', 'COM_KUNENA_REALNAME', 'name', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?></span>
						</div>
					</dd>
				<?php endif; ?>
				<?php if ($this->config->userlist_posts) : ?>
					<dd class="user-posts">
						<div class="innerspacer-header">
							<span class="bold"><?php echo JHtml::_( 'grid.sort', 'COM_KUNENA_USRL_POSTS', 'posts', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?></span>
						</div>
					</dd>
				<?php endif; ?>
				<?php if ($this->config->userlist_karma) : ?>
					<dd class="user-karma">
						<div class="innerspacer-header">
							<span class="bold"><?php echo JHtml::_( 'grid.sort', 'COM_KUNENA_USRL_KARMA', 'karma', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?></span>
						</div>
					</dd>
				<?php endif; ?>
				<?php if ($this->config->userlist_email) : ?>
					<dd class="user-email">
						<div class="innerspacer-header">
							<span class="bold"><?php echo JHtml::_( 'grid.sort', 'COM_KUNENA_USRL_EMAIL', 'email', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?></span>
						</div>
					</dd>
				<?php endif; ?>
				<?php if ($this->config->userlist_usertype) : ?>
					<dd class="user-usertype">
						<div class="innerspacer-header">
							<span class="bold"><?php echo JHtml::_( 'grid.sort', 'COM_KUNENA_USRL_USERTYPE', 'usertype', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?></span>
						</div>
					</dd>
				<?php endif; ?>
				<?php if ($this->config->userlist_joindate) : ?>
					<dd class="user-joindate">
						<div class="innerspacer-header">
							<span class="bold"><?php echo JHtml::_( 'grid.sort', 'COM_KUNENA_USRL_JOIN_DATE', 'registerDate', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?></span>
						</div>
					</dd>
				<?php endif; ?>
				<?php if ($this->config->userlist_lastvisitdate) : ?>
					<dd class="user-lastvisitdate">
						<div class="innerspacer-header">
							<span class="bold"><?php echo JHtml::_( 'grid.sort', 'COM_KUNENA_USRL_LAST_LOGIN', 'lastvisitDate', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?></span>
						</div>
					</dd>
				<?php endif; ?>
				<?php if ($this->config->userlist_userhits) : ?>
					<dd class="user-hits">
						<div class="innerspacer-header">
							<span class="bold"><?php echo JHtml::_( 'grid.sort', 'COM_KUNENA_USRL_HITS', 'uhits', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?></span>
						</div>
					</dd>
				<?php endif; ?>
			</dl>
		</li>
	</ul>
	<ul class="category-list">
		<?php foreach ($this->users as $user) { $this->displayUserRow($user); } ?>
	</ul>
</form>
