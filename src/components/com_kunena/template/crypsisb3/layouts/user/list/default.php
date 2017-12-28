<?php
/**
 * Kunena Component
 * @package     Kunena.Template.Crypsis
 * @subpackage  Layout.User
 *
 * @copyright   (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license     https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die;

$config = $this->config;

$cols = 1;
// Load caret.js always before atwho.js script and use it for autocomplete, emojiis...
$this->addScript('assets/js/jquery.caret.js');
$this->addScript('assets/js/jquery.atwho.js');
$this->addStyleSheet('assets/css/jquery.atwho.css');
$this->addScript('assets/js/search.js');
?>
<h1>
	<?php echo JText::_('COM_KUNENA_MEMBERS'); ?>
</h1>

<h2 class="pull-right">
	<?php echo $this->subLayout('Widget/Search')
	->set('state', $this->state->get('list.search'))
	->setLayout('user'); ?>
</h2>

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
				<th class="col-md-1 center hidden-xs">
					<a id="forumtop"> </a>
					<a href="#forumbottom" rel="nofollow">
						<?php echo KunenaIcons::arrowdown();?>
					</a>
				</th>

				<?php if ($config->userlist_online) : $cols++; ?>
				<th class="col-md-1 center hidden-xs">
					<?php echo JText::_('COM_KUNENA_USRL_ONLINE'); ?>
				</th>
				<?php endif; ?>

				<?php if ($config->userlist_avatar) : $cols++; ?>
				<th class="col-md-1 center hidden-xs">
					<?php echo JText::_('COM_KUNENA_USRL_AVATAR'); ?>
				</th>
				<?php endif; ?>

				<?php if ($config->username) : $cols++; ?>
				<th class="col-md-2">
					<?php echo JHtml::_(
	'kunenagrid.sort', 'COM_KUNENA_USERNAME', 'username',
	$this->state->get('list.direction'), $this->state->get('list.ordering'), '', '',
'kuserlist-form'); ?>
				</th>
				<?php else : $cols++; ?>
				<th class="col-md-3">
					<?php echo JHtml::_(
	'kunenagrid.sort', 'COM_KUNENA_REALNAME', 'name',
	$this->state->get('list.direction'), $this->state->get('list.ordering'), '', '',
'kuserlist-form'); ?>
				</th>
				<?php endif; ?>

				<?php if ($config->userlist_posts) : $cols++; ?>
				<th class="col-md-1 center hidden-xs">
					<?php echo JHtml::_(
	'kunenagrid.sort', 'COM_KUNENA_USRL_POSTS', 'posts',
	$this->state->get('list.direction'), $this->state->get('list.ordering'), '', '',
'kuserlist-form'); ?>
				</th>
				<?php endif; ?>

				<?php if ($config->userlist_karma) : $cols++; ?>
				<th class="col-md-1 center hidden-xs">
					<?php echo JHtml::_(
	'kunenagrid.sort', 'COM_KUNENA_USRL_KARMA', 'karma',
	$this->state->get('list.direction'), $this->state->get('list.ordering'), '', '',
'kuserlist-form'); ?>
				</th>
				<?php endif; ?>

				<?php if ($config->userlist_email) : $cols++; ?>
				<th class="col-md-1 hidden-xs">
					<?php echo JHtml::_(
	'kunenagrid.sort', 'COM_KUNENA_USRL_EMAIL', 'email',
	$this->state->get('list.direction'), $this->state->get('list.ordering'), '', '',
'kuserlist-form'); ?>
				</th>
				<?php endif; ?>

				<?php if ($config->userlist_joindate) : $cols++; ?>
				<th class="col-md-2 hidden-xs">
					<?php echo JHtml::_(
	'kunenagrid.sort', 'COM_KUNENA_USRL_JOIN_DATE', 'registerDate',
	$this->state->get('list.direction'), $this->state->get('list.ordering'), '', '',
'kuserlist-form'); ?>
				</th>
				<?php endif; ?>

				<?php if ($config->userlist_lastvisitdate) : $cols++; ?>
				<th class="col-md-2 hidden-xs">
					<?php echo JHtml::_(
	'kunenagrid.sort', 'COM_KUNENA_USRL_LAST_LOGIN', 'lastvisitDate',
	$this->state->get('list.direction'), $this->state->get('list.ordering'), '', '',
'kuserlist-form'); ?>
				</th>
				<?php endif; ?>

				<?php if ($config->userlist_userhits) : $cols++; ?>
				<th class="col-md-1 center hidden-xs">
					<?php echo JHtml::_(
	'kunenagrid.sort', 'COM_KUNENA_USRL_HITS', 'uhits',
	$this->state->get('list.direction'), $this->state->get('list.ordering'), '', '',
'kuserlist-form'); ?>
				</th>
				<?php endif; ?>
			</tr>
		</thead>
		<tbody>
		<?php
		$i = $this->pagination->limitstart;
		$this->ktemplate = KunenaFactory::getTemplate();
		// @var KunenaUser $user

		foreach ($this->users as $user) :
			$avatar = $config->userlist_avatar ? $user->getAvatarImage($this->ktemplate->params->get('avatarType'), 'thumb') : null;
		?>
			<tr>
				<td class="col-md-1 center">
					<?php echo ++$i; ?>
				</td>

				<?php if ($config->userlist_online) : ?>
				<td class="col-md-1 center hidden-xs">
					<?php echo $this->subLayout('User/Item/Status')->set('user', $user); ?>
				</td>
				<?php endif; ?>

				<?php if ($avatar) : ?>
				<td class="col-md-1 center hidden-xs">
					<div class="post-image">
							<?php echo $avatar; ?>
					</div>
				</td>
				<?php endif; ?>

				<td class="col-md-2">
					<?php echo $user->getLink(null, null, ''); ?>
				</td>

				<?php if ($config->userlist_posts) : ?>
				<td class="col-md-1 center hidden-xs">
					<?php echo (int) $user->posts; ?>
				</td>
				<?php endif; ?>

				<?php if ($config->userlist_karma) : ?>
				<td class="col-md-1 center hidden-xs">
					<?php echo (int) $user->karma; ?>
				</td>
				<?php endif; ?>

				<?php if ($config->userlist_email) : ?>
				<td class="col-md-1 hidden-xs">
					<?php echo $user->email ? JHtml::_('email.cloak', $user->email) : '' ?>
				</td>
				<?php endif; ?>

				<?php if ($config->userlist_joindate) : ?>
				<td title="<?php echo $user->getRegisterDate()->toKunena('ago'); ?>" class="col-md-2 hidden-xs">
					<?php echo $user->getRegisterDate()->toKunena('datetime_today'); ?>
				</td>
				<?php endif; ?>

				<?php if ($config->userlist_lastvisitdate) : ?>
				<td title="<?php echo $user->getLastVisitDate()->toKunena('ago'); ?>" class="col-md-2 hidden-xs">
					<?php echo $user->getLastVisitDate()->toKunena('datetime_today'); ?>
				</td>
				<?php endif; ?>

				<?php if ($config->userlist_userhits) : ?>
				<td class="col-md-1 center hidden-xs">
					<?php echo (int) $user->uhits; ?>
				</td>
				<?php endif; ?>
			</tr>
		<?php endforeach; ?>
		</tbody>
		<tfoot>
			<tr>
				<td class="col-md-1 center hidden-xs">
					<a id="forumbottom"> </a>
					<a href="#forumtop" rel="nofollow">
						<?php echo KunenaIcons::arrowup();?>
					</a>
				</td>
				<td colspan="8" class="hidden-xs">
				</td>
			</tr>
		</tfoot>

	</table>

	<div class="pull-left">
		<?php echo $this->subLayout('Widget/Pagination/List')
	->set('pagination', $this->pagination)
	->set('display', true); ?>
	</div>
</form>
<div class="clearfix"></div>
