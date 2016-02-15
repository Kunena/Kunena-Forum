<?php
/**
 * Kunena Component
 * @package     Kunena.Template.Crypsis
 * @subpackage  Layout.User
 *
 * @copyright   (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die;

$config = $this->config;

$cols = 1;

$this->addScript('js/search.js');
?>
<h2>
	<?php echo JText::_('COM_KUNENA_MEMBERS'); ?>
</h2>

<div class="pull-right">
	<?php echo $this->subLayout('Widget/Search')
		->set('state', $this->state->get('list.search'))
		->setLayout('user'); ?>
</div>

<div class="pull-left">
	<?php echo $this->subLayout('Widget/Pagination/List')
		->set('pagination', $this->pagination)
		->set('display', true); ?>
</div>

<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=user&layout=list'); ?>"
      method="post" id="kuserlist-form" name="kuserlist-form">
	<input type="hidden" name="filter_order" value="<?php echo $this->state->get('list.ordering'); ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->state->get('list.direction'); ?>" />
	<?php echo JHtml::_('form.token'); ?>

	<table class="table table-bordered table-striped">
		<thead>
			<tr>
				<th class="span1 center hidden-phone">
					<a id="forumtop"> </a>
					<a href="#forumbottom">
						<i class="icon-arrow-down"></i>
					</a>
				</th>

				<?php if ($config->userlist_online) : $cols++; ?>
				<th class="span1 center hidden-phone">
					<?php echo JText::_('COM_KUNENA_USRL_ONLINE'); ?>
				</th>
				<?php endif; ?>

				<?php if ($config->userlist_avatar) : $cols++; ?>
				<th class="span1 center hidden-phone">
					<?php echo JText::_('COM_KUNENA_USRL_AVATAR'); ?>
				</th>
				<?php endif; ?>

				<?php if ($config->username) : $cols++; ?>
				<th class="span2">
					<?php echo JHtml::_( 'kunenagrid.sort', 'COM_KUNENA_USERNAME', 'username',
						$this->state->get('list.direction'), $this->state->get('list.ordering'), '', '',
						'kuserlist-form'); ?>
				</th>
				<?php else : $cols++; ?>
				<th class="span3">
					<?php echo JHtml::_( 'kunenagrid.sort', 'COM_KUNENA_REALNAME', 'name',
						$this->state->get('list.direction'), $this->state->get('list.ordering'), '', '',
						'kuserlist-form'); ?>
				</th>
				<?php endif; ?>

				<?php if ($config->userlist_posts) : $cols++; ?>
				<th class="span1 center hidden-phone">
					<?php echo JHtml::_( 'kunenagrid.sort', 'COM_KUNENA_USRL_POSTS', 'posts',
						$this->state->get('list.direction'), $this->state->get('list.ordering'), '', '',
						'kuserlist-form'); ?>
				</th>
				<?php endif; ?>

				<?php if ($config->userlist_email) : $cols++; ?>
				<th class="span1 hidden-phone">
					<?php echo JHtml::_( 'kunenagrid.sort', 'COM_KUNENA_USRL_EMAIL', 'email',
						$this->state->get('list.direction'), $this->state->get('list.ordering'), '', '',
						'kuserlist-form'); ?>
				</th>
				<?php endif; ?>

				<?php if ($config->userlist_joindate) : $cols++; ?>
				<th class="span2 hidden-phone">
					<?php echo JHtml::_( 'kunenagrid.sort', 'COM_KUNENA_USRL_JOIN_DATE', 'registerDate',
						$this->state->get('list.direction'), $this->state->get('list.ordering'), '', '',
						'kuserlist-form'); ?>
				</th>
				<?php endif; ?>

				<?php if ($config->userlist_lastvisitdate) : $cols++; ?>
				<th class="span2 hidden-phone">
					<?php echo JHtml::_( 'kunenagrid.sort', 'COM_KUNENA_USRL_LAST_LOGIN', 'lastvisitDate',
						$this->state->get('list.direction'), $this->state->get('list.ordering'), '', '',
						'kuserlist-form'); ?>
				</th>
				<?php endif; ?>

				<?php if ($config->userlist_userhits) : $cols++; ?>
				<th class="span1 center hidden-phone">
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
			$avatar = $config->userlist_avatar ? $user->getAvatarImage('img-polaroid', 'thumb') : null;
		?>
			<tr>
				<td class="span1 center">
					<?php echo ++$i; ?>
				</td>

				<?php if ($config->userlist_online) : ?>
				<td class="span1 center hidden-phone">
					<?php echo $this->subLayout('User/Item/Status')->set('user', $user); ?>
				</td>
				<?php endif; ?>

				<?php if ($avatar) : ?>
				<td class="span1 center hidden-phone">
					<div class="post-image">
							<?php echo $avatar; ?>
					</div>
				</td>
				<?php endif; ?>

				<td class="span2">
					<?php echo $user->getLink(); ?>
				</td>

				<?php if ($config->userlist_posts) : ?>
				<td class="span1 center hidden-phone">
					<?php echo (int) $user->posts; ?>
				</td>
				<?php endif; ?>

				<?php if ($config->userlist_email) : ?>
				<td class="span1 hidden-phone">
					<?php echo $user->email ? JHtml::_('email.cloak', $user->email) : '' ?>
				</td>
				<?php endif; ?>

				<?php if ($config->userlist_joindate) : ?>
				<td title="<?php echo $user->getRegisterDate()->toKunena('ago'); ?>" class="span2 hidden-phone">
					<?php echo $user->getRegisterDate()->toKunena('datetime_today'); ?>
				</td>
				<?php endif; ?>

				<?php if ($config->userlist_lastvisitdate) : ?>
				<td title="<?php echo $user->getLastVisitDate()->toKunena('ago'); ?>" class="span2 hidden-phone">
					<?php echo $user->getLastVisitDate()->toKunena('datetime_today'); ?>
				</td>
				<?php endif; ?>

				<?php if ($config->userlist_userhits) : ?>
				<td class="span1 center hidden-phone">
					<?php echo (int) $user->uhits; ?>
				</td>
				<?php endif; ?>
			</tr>
		<?php endforeach; ?>
		</tbody>
		<tfoot>
		<td class="span1 center hidden-phone">
			<a id="forumbottom"> </a>
			<a href="#forumtop">
				<i class="icon-arrow-up hasTooltip"></i>
			</a>
		</td>
		<td colspan="7" class="hidden-phone">
		</td>
		</tfoot>

	</table>

	<div class="pull-left">
		<?php echo $this->subLayout('Widget/Pagination/List')
			->set('pagination', $this->pagination)
			->set('display', true); ?>
	</div>
</form>
<div class="clearfix"></div>
