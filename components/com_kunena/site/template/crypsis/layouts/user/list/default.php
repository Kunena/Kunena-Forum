<?php
/**
 * Kunena Component
 * @package     Kunena.Template.Crypsis
 * @subpackage  Layout.User
 *
 * @copyright   (C) 2008 - 2014 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        http://www.kunena.org
 **/
defined('_JEXEC') or die;

$cols = 1;
?>
<h2>
	<?php echo JText::_('COM_KUNENA_USRL_USERLIST'); ?>

	<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=user&layout=list'); ?>" method="post"
	      name="usrlform" id="usrlform" class="form-search pull-right">
		<input type="hidden" name="view" value="user" />
		<?php if ($this->me->exists()): ?>
			<input type="hidden" id="kurl_users" name="kurl_users" value="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=user&layout=listmention&format=raw') ?>" />
		<?php endif; ?>
		<?php echo JHtml::_('form.token'); ?>

		<div class="input-append">
			<label>
				<input id="kusersearch" class="input-medium search-query" type="text" name="search"
				       value="<?php echo $this->escape($this->state->get('list.search')); ?>" placeholder="" />
			</label>

			<button type="submit" class="btn"><span class="icon icon-search"></span></button>
		</div>
	</form>
</h2>

<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=user&layout=list'); ?>"
      method="post" id="kuserlist-form" name="kuserlist-form">
	<input type="hidden" name="filter_order" value="<?php echo $this->state->get('list.ordering'); ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->state->get('list.direction'); ?>" />
	<?php echo JHtml::_( 'form.token' ); ?>

	<table class="table table-bordered table-striped table-hover table-condensed">
		<thead>
			<tr>
				<th class="center">
					#
				</th>

				<?php if ($this->config->userlist_online) : $cols++; ?>
				<th class="center">
					<?php echo JText::_('COM_KUNENA_USRL_ONLINE'); ?>
				</th>
				<?php endif; ?>

				<?php if ($this->config->userlist_avatar) : $cols++; ?>
				<th class="center">
					<?php echo JText::_('COM_KUNENA_USRL_AVATAR'); ?>
				</th>
				<?php endif; ?>

				<?php if ($this->config->username) : $cols++; ?>
				<th>
					<?php echo JHtml::_( 'kunenagrid.sort', 'COM_KUNENA_USERNAME', 'username',
						$this->state->get('list.direction'), $this->state->get('list.ordering'), '', '',
						'kuserlist-form'); ?>
				</th>
				<?php else : $cols++; ?>
				<th>
					<?php echo JHtml::_( 'kunenagrid.sort', 'COM_KUNENA_REALNAME', 'name',
						$this->state->get('list.direction'), $this->state->get('list.ordering'), '', '',
						'kuserlist-form'); ?>
				</th>
				<?php endif; ?>

				<?php if ($this->config->userlist_posts) : $cols++; ?>
				<th class="center">
					<?php echo JHtml::_( 'kunenagrid.sort', 'COM_KUNENA_USRL_POSTS', 'posts',
						$this->state->get('list.direction'), $this->state->get('list.ordering'), '', '',
						'kuserlist-form'); ?>
				</th>
				<?php endif; ?>

				<?php if ($this->config->userlist_karma) : $cols++; ?>
				<th class="center">
					<?php echo JHtml::_( 'kunenagrid.sort', 'COM_KUNENA_USRL_KARMA', 'karma',
						$this->state->get('list.direction'), $this->state->get('list.ordering'), '', '',
						'kuserlist-form'); ?>
				</th>
				<?php endif; ?>

				<?php if ($this->config->userlist_email) : $cols++; ?>
				<th>
					<?php echo JHtml::_( 'kunenagrid.sort', 'COM_KUNENA_USRL_EMAIL', 'email',
						$this->state->get('list.direction'), $this->state->get('list.ordering'), '', '',
						'kuserlist-form'); ?>
				</th>
				<?php endif; ?>

				<?php if ($this->config->userlist_joindate) : $cols++; ?>
				<th>
					<?php echo JHtml::_( 'kunenagrid.sort', 'COM_KUNENA_USRL_JOIN_DATE', 'registerDate',
						$this->state->get('list.direction'), $this->state->get('list.ordering'), '', '',
						'kuserlist-form'); ?>
				</th>
				<?php endif; ?>

				<?php if ($this->config->userlist_lastvisitdate) : $cols++; ?>
				<th>
					<?php echo JHtml::_( 'kunenagrid.sort', 'COM_KUNENA_USRL_LAST_LOGIN', 'lastvisitDate',
						$this->state->get('list.direction'), $this->state->get('list.ordering'), '', '',
						'kuserlist-form'); ?>
				</th>
				<?php endif; ?>

				<?php if ($this->config->userlist_userhits) : $cols++; ?>
				<th class="center">
					<?php echo JHtml::_( 'kunenagrid.sort', 'COM_KUNENA_USRL_HITS', 'uhits',
						$this->state->get('list.direction'), $this->state->get('list.ordering'), '', '',
						'kuserlist-form'); ?>
				</th>
				<?php endif; ?>

			</tr>
		</thead>
		<tbody>
		<?php
		$i = $this->pagination->limitstart;

		/** @var KunenaUser $user */
		foreach ($this->users as $user) :
			$avatar = $this->config->userlist_avatar ? $user->getAvatarImage('img-rounded', 'list') : null;
		?>
			<tr>
				<td class="center">
					<?php echo ++$i; ?>
				</td>

				<?php if ($this->config->userlist_online) : ?>
				<td class="center">
					<span class="label label-<?php echo $user->isOnline('success', 'important'); ?>">
						<?php echo $user->isOnline(JText::_('COM_KUNENA_ONLINE'), JText::_('COM_KUNENA_OFFLINE')); ?>
					</span>
				</td>
				<?php endif; ?>

				<?php if ($avatar) : ?>
				<td class="center">
					<?php echo $avatar; ?>
				</td>
				<?php endif; ?>

				<td>
					<?php echo $user->getLink(); ?>
				</td>

				<?php if ($this->config->userlist_posts) : ?>
				<td class="center">
					<?php echo (int) $user->posts; ?>
				</td>
				<?php endif; ?>

				<?php if ($this->config->userlist_karma) : ?>
				<td class="center">
					<?php echo (int) $user->karma; ?>
				</td>
				<?php endif; ?>

				<?php if ($this->config->userlist_email) : ?>
				<td>
					<?php echo $user->email ? JHtml::_('email.cloak', $user->email) : '' ?>
				</td>
				<?php endif; ?>

				<?php if ($this->config->userlist_joindate) : ?>
				<td title="<?php echo $user->getRegisterDate()->toKunena('ago'); ?>">
					<?php echo $user->getRegisterDate()->toKunena('datetime_today'); ?>
				</td>
				<?php endif; ?>

				<?php if ($this->config->userlist_lastvisitdate) : ?>
				<td title="<?php echo $user->getLastVisitDate()->toKunena('ago'); ?>">
					<?php echo $user->getLastVisitDate()->toKunena('datetime_today'); ?>
				</td>
				<?php endif; ?>

				<?php if ($this->config->userlist_userhits) : ?>
				<td class="center">
					<?php echo (int) $user->uhits; ?>
				</td>
				<?php endif; ?>

			</tr>
			<?php endforeach; ?>

		</tbody>
		<?php if ($i > 1) : ?>
		<tfoot>
			<tr>
				<td colspan="<?php echo $cols; ?>">
					<div class="pull-right">
						<?php echo $this->subLayout('Widget/Pagination/List')->set('pagination', $this->pagination); ?>
					</div>
				</td>
			</tr>
		</tfoot>
		<?php endif; ?>
	</table>
</form>
<div class="clearfix"></div>
