<?php
/**
 * Kunena Component
 * @package Kunena.Template.Crypsis
 * @subpackage User
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

if ($this->me->exists()) {
	$this->addScriptDeclaration( "// <![CDATA[
document.addEvent('domready', function() {
	// Attach auto completer to the following ids:
	new Autocompleter.Request.JSON('kusersearch', '".KunenaRoute::_('index.php?option=com_kunena&view=user&layout=list&format=raw')."', { 'postVar': 'search' });
});
// ]]>");
}
$cols = 1;
?>


<h2>
	<?php echo JText::_('COM_KUNENA_USRL_USERLIST'); ?>
	<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=user&layout=list') ?>" method="post" name="usrlform" id="usrlform" class="pull-right">
		<input type="hidden" name="view" value="user" />
		<?php echo JHtml::_( 'form.token' ); ?>

		<input id="kusersearch" type="text" name="search" class="input-medium search-query"
		       value="<?php echo $this->escape($this->state->get('list.search')); ?>"
			/>
		<input type="button" class="btn" value="<?php echo JText::_('COM_KUNENA_USRL_SEARCH'); ?>" />
	</form>
</h2>

<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=user&layout=list') ?>" method="post" id="kuserlist-form" name="kuserlist-form">
	<input type="hidden" name="filter_order" value="<?php echo $this->state->get('list.ordering'); ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->state->get('list.direction'); ?>" />
	<?php echo JHtml::_( 'form.token' ); ?>

	<table class="table table-bordered table-striped table-hover table-condensed">
		<thead>
			<tr>
				<th> # </th>
				<?php if ($this->config->userlist_online) : $cols++; ?>
				<th><?php echo JText::_('COM_KUNENA_USRL_ONLINE'); ?></th>
				<?php endif; ?>
				<?php if ($this->config->userlist_avatar) : $cols++; ?>
				<th><?php echo JText::_('COM_KUNENA_USRL_AVATAR'); ?></th>
				<?php endif; ?>
				<?php if ($this->config->username) : $cols++; ?>
				<th><?php echo JHtml::_( 'kunenagrid.sort', 'COM_KUNENA_USERNAME', 'username', $this->state->get('list.direction'), $this->state->get('list.ordering'), '', '', 'kuserlist-form'); ?></th>
				<?php else : $cols++; ?>
				<th><?php echo JHtml::_( 'kunenagrid.sort', 'COM_KUNENA_REALNAME', 'name', $this->state->get('list.direction'), $this->state->get('list.ordering'), '', '', 'kuserlist-form'); ?></th>
				<?php endif; ?>
				<?php if ($this->config->userlist_posts) : $cols++; ?>
				<th><?php echo JHtml::_( 'kunenagrid.sort', 'COM_KUNENA_USRL_POSTS', 'posts', $this->state->get('list.direction'), $this->state->get('list.ordering'), '', '', 'kuserlist-form'); ?></th>
				<?php endif; ?>
				<?php if ($this->config->userlist_karma) : $cols++; ?>
				<th><?php echo JHtml::_( 'kunenagrid.sort', 'COM_KUNENA_USRL_KARMA', 'karma', $this->state->get('list.direction'), $this->state->get('list.ordering'), '', '', 'kuserlist-form'); ?></th>
				<?php endif; ?>
				<?php if ($this->config->userlist_email) : $cols++; ?>
				<th><?php echo JHtml::_( 'kunenagrid.sort', 'COM_KUNENA_USRL_EMAIL', 'email', $this->state->get('list.direction'), $this->state->get('list.ordering'), '', '', 'kuserlist-form'); ?></th>
				<?php endif; ?>
				<?php if ($this->config->userlist_joindate) : $cols++; ?>
				<th><?php echo JHtml::_( 'kunenagrid.sort', 'COM_KUNENA_USRL_JOIN_DATE', 'registerDate', $this->state->get('list.direction'), $this->state->get('list.ordering'), '', '', 'kuserlist-form'); ?></th>
				<?php endif; ?>
				<?php if ($this->config->userlist_lastvisitdate) : $cols++; ?>
				<th><?php echo JHtml::_( 'kunenagrid.sort', 'COM_KUNENA_USRL_LAST_LOGIN', 'lastvisitDate', $this->state->get('list.direction'), $this->state->get('list.ordering'), '', '', 'kuserlist-form'); ?></th>
				<?php endif; ?>
				<?php if ($this->config->userlist_userhits) : $cols++; ?>
				<th><?php echo JHtml::_( 'kunenagrid.sort', 'COM_KUNENA_USRL_HITS', 'uhits', $this->state->get('list.direction'), $this->state->get('list.ordering'), '', '', 'kuserlist-form'); ?></th>
				<?php endif; ?>
			</tr>
		</thead>
		<?php
		$i = $this->pagination->limitstart;
		foreach ($this->users as $user) :
			$i++;

			$avatar = $user->getAvatarImage('', 'list');
			if ($user->lastvisitDate == "0000-00-00 00:00:00") {
				$lastvisitDate = KunenaDate::getInstance($user->registerDate);
			} else {
				$lastvisitDate = KunenaDate::getInstance($user->lastvisitDate);
			}
		?>
		<tbody>
			<tr>
				<td>
					<?php echo $i; ?>
				</td>
				<?php if ($this->config->userlist_online) : ?>
				<td>
					<span class="kicon-button kbuttononline-<?php echo $user->isOnline('yes', 'no') ?>">
						<span class="online-<?php echo $user->isOnline('yes', 'no') ?>">
							<span>
								<?php echo $user->isOnline(JText::_('COM_KUNENA_ONLINE'), JText::_('COM_KUNENA_OFFLINE')); ?>
							</span>
						</span>
					</span>
				</td>
				<?php endif; ?>
				<?php if ($this->config->userlist_avatar) : ?>
				<td>
					<?php echo $avatar; ?>
				</td>
				<?php endif; ?>
				<td>
					<?php echo $user->getLink(); ?>
				</td>
				<?php if ($this->config->userlist_posts) : ?>
				<td>
					<?php echo intval($user->posts); ?>
				</td>
				<?php endif; ?>
				<?php if ($this->config->userlist_karma) : ?>
				<td>
					<?php echo intval($user->karma); ?>
				</td>
				<?php endif; ?>
				<?php if ($this->config->userlist_email) : ?>
				<td>
					<?php echo $user->email ? JHtml::_('email.cloak', $user->email) : '' ?>
				</td>
				<?php endif; ?>
				<?php if ($this->config->userlist_joindate) : ?>
				<td title="<?php echo KunenaDate::getInstance($user->registerDate)->toKunena('ago') ?>">
					<?php echo KunenaDate::getInstance($user->registerDate)->toKunena('datetime_today') ?>
				</td>
				<?php endif; ?>
				<?php if ($this->config->userlist_lastvisitdate) : ?>
				<td title="<?php echo $lastvisitDate->toKunena('ago') ?>">
					<?php echo $lastvisitDate->toKunena('datetime_today') ?>
				</td>
				<?php endif; ?>
				<?php if ($this->config->userlist_userhits) : ?>
				<td>
					<?php echo $this->escape($user->uhits) ?>
				</td>
				<?php endif; ?>
			</tr>
			<?php endforeach; ?>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="<?php echo $cols; ?>">
					<div class="pull-right">
						<?php echo $this->subLayout('Pagination/List')->set('pagination', $this->pagination); ?>
					</div>
				</td>
			</tr>
		</tfoot>
	</table>
</form>

