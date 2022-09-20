<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Template.Aurelia
 * @subpackage      Layout.User
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site;

\defined('_JEXEC') or die();

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Icons\KunenaIcons;
use Kunena\Forum\Libraries\Route\KunenaRoute;

$config = $this->config;

$cols = 1;

// Load caret.js always before atwho.js script and use it for autocomplete, emojiis...
$this->addScript('jquery.caret.js');
$this->addScript('jquery.atwho.js');
$this->addStyleSheet('jquery.atwho.css');
$this->addScript('assets/js/search.js');
?>
<h1>
	<?php echo Text::_('COM_KUNENA_MEMBERS'); ?>
</h1>

<h2 class="float-end">
	<?php echo $this->subLayout('Widget/Search')
	->set('state', $this->state->get('list.search'))
	->setLayout('user'); ?>
</h2>

<div class="float-start">
	<?php echo $this->subLayout('Widget/Pagination/List')
	->set('pagination', $this->pagination)
	->set('display', true); ?>
</div>

<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=user&layout=list'); ?>"
	  method="post" id="kuserlist-form" name="kuserlist-form">
	<input type="hidden" name="filter_order" value="<?php echo $this->state->get('list.ordering'); ?>"/>
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->state->get('list.direction'); ?>"/>
	<?php echo HTMLHelper::_('form.token'); ?>

	<table class="table table-bordered table-striped">
		<thead>
		<tr>
			<th class="col-md-1 center d-none d-md-table-cell">
				<a id="forumtop"> </a>
				<a href="#forumbottom" rel="nofollow">
					<?php echo KunenaIcons::arrowdown(); ?>
				</a>
			</th>

			<?php if ($config->userlistOnline && $config->userStatus)
:
				$cols++; ?>
				<th class="col-md-1 center d-none d-md-table-cell">
					<?php echo Text::_('COM_KUNENA_USRL_ONLINE'); ?>
				</th>
			<?php endif; ?>

			<?php if ($config->userlistAvatar)
:
				$cols++; ?>
				<th class="col-md-1 center d-none d-md-table-cell">
					<?php echo Text::_('COM_KUNENA_USRL_AVATAR'); ?>
				</th>
			<?php endif; ?>

			<?php if ($config->username)
:
				$cols++;
				?>
				<th class="col-md-2">
					<?php echo HTMLHelper::_(
					'kunenagrid.sort',
					'COM_KUNENA_USERNAME',
					'username',
					$this->state->get('list.direction'),
					$this->state->get('list.ordering'),
					'',
					'',
					'kuserlist-form'
				);
					?>
				</th>
			<?php else
:
				$cols++;
				?>
				<th class="col-md-3">-->
					<?php echo HTMLHelper::_(
					'kunenagrid.sort',
					'COM_KUNENA_REALNAME',
					'name',
					$this->state->get('list.direction'),
					$this->state->get('list.ordering'),
					'',
					'',
					'kuserlist-form'
				);
					?>
				</th>
			<?php endif;
			?>

			<?php if ($config->userlistPosts)
			:
				$cols++;
				?>
				<th class="col-md-1 center d-none d-md-table-cell">
					<?php echo HTMLHelper::_(
					'kunenagrid.sort',
					'COM_KUNENA_USRL_POSTS',
					'posts',
					$this->state->get('list.direction'),
					$this->state->get('list.ordering'),
					'',
					'',
					'kuserlist-form'
				);
					?>
				</th>
			<?php endif;
			?>

			<?php if ($config->userlistKarma)
			:
				$cols++;
				?>
				<th class="col-md-1 center d-none d-md-table-cell">
					<?php echo HTMLHelper::_(
					'kunenagrid.sort',
					'COM_KUNENA_USRL_KARMA',
					'karma',
					$this->state->get('list.direction'),
					$this->state->get('list.ordering'),
					'',
					'',
					'kuserlist-form'
				);
					?>
				</th>
			<?php endif;
			?>

			<?php if ($config->userlistEmail)
			:
				$cols++;
				?>
			<th class="col-md-1 d-none d-md-table-cell">
				<?php echo HTMLHelper::_(
					'kunenagrid.sort',
					'COM_KUNENA_USRL_EMAIL',
					'email',
					$this->state->get('list.direction'),
					$this->state->get('list.ordering'),
					'',
					'',
					'kuserlist-form'
				);
				?>
			<?php endif;
			?>

				<?php if ($config->userlistJoinDate)
				:
					$cols++;
					?>
			<th class="col-md-2 d-none d-md-table-cell">
					<?php echo HTMLHelper::_(
						'kunenagrid.sort',
						'COM_KUNENA_USRL_JOIN_DATE',
						'registerDate',
						$this->state->get('list.direction'),
						$this->state->get('list.ordering'),
						'',
						'',
						'kuserlist-form'
					);
					?>
			</th>
				<?php endif;
				?>

			<?php if ($config->userlistLastVisitDate)
			:
				$cols++;
				?>
				<th class="col-md-2 d-none d-md-table-cell">
					<?php echo HTMLHelper::_(
					'kunenagrid.sort',
					'COM_KUNENA_USRL_LAST_LOGIN',
					'lastvisitDate',
					$this->state->get('list.direction'),
					$this->state->get('list.ordering'),
					'',
					'',
					'kuserlist-form'
				);
					?>
				</th>
			<?php endif;
			?>

			<?php if ($config->userlistUserHits)
			:
				?>
				<th class="col-md-1 center d-none d-md-table-cell">
					<?php echo HTMLHelper::_(
					'kunenagrid.sort',
					'COM_KUNENA_USRL_HITS',
					'uhits',
					$this->state->get('list.direction'),
					$this->state->get('list.ordering'),
					'',
					'',
					'kuserlist-form'
				);
					?>
				</th>
			<?php endif;
			?>
		</tr>
		</thead>
		<tbody class="user-list">
		<?php
		$i               = $this->pagination->limitstart;
		$this->ktemplate = KunenaFactory::getTemplate();

		foreach ($this->users as $user)
		:
			$avatar = $config->userlistAvatar ? $user->getAvatarImage($this->ktemplate->params->get('avatarType'), 'thumb') : null;
			?>
			<tr>
				<td class="col-md-1 center">
					<?php echo ++$i; ?>
				</td>

				<?php if ($config->userlistOnline && $config->userStatus)
				:
					?>
					<td class="col-md-1 center d-none d-md-table-cell">
						<?php echo $this->subLayout('User/Item/Status')->set('user', $user); ?>
					</td>
				<?php endif; ?>

				<?php if ($avatar)
				:
					?>
					<td class="col-md-1 center d-none d-md-table-cell">
						<div class="post-image kwho-<?php echo $user->getType(0, true); ?>">
							<?php echo $avatar; ?>
						</div>
					</td>
				<?php endif; ?>

				<td class="col-md-2">
					<?php echo $user->getLink(null, null, ''); ?>
				</td>

				<?php if ($config->userlistPosts)
				:
					?>
					<td class="col-md-1 center d-none d-md-table-cell">
						<?php echo (int) $user->posts; ?>
					</td>
				<?php endif; ?>

				<?php if ($config->userlistKarma)
				:
					?>
					<td class="col-md-1 center d-none d-md-table-cell">
						<?php echo (int) $user->karma; ?>
					</td>
				<?php endif; ?>

				<?php if ($config->userlistEmail)
				:
					?>
					<td class="col-md-1 d-none d-md-table-cell">
						<?php echo $user->email ? HTMLHelper::_('email.cloak', $user->email) : '' ?>
					</td>
				<?php endif; ?>

				<?php if ($config->userlistJoinDate)
				:
					?>
					<td data-bs-toggle="tooltip" title="<?php echo $user->getRegisterDate()->toKunena('ago'); ?>"
						class="col-md-2 d-none d-sm-block">
						<?php echo $user->getRegisterDate()->toKunena('datetime_today'); ?>
					</td>
				<?php endif; ?>

				<?php if ($config->userlistLastVisitDate)
				:
					?>
					<td data-bs-toggle="tooltip" title="<?php echo $user->getLastVisitDate()->toKunena('ago'); ?>"
						class="col-md-2 d-none d-sm-block">
						<?php echo $user->getLastVisitDate()->toKunena('datetime_today'); ?>
					</td>
				<?php endif; ?>

				<?php if ($config->userlistUserHits)
				:
					?>
					<td class="col-md-1 center d-none d-md-table-cell">
						<?php echo (int) $user->uhits; ?>
					</td>
				<?php endif; ?>
			</tr>
		<?php endforeach; ?>
		</tbody>

		<tfoot>
		<tr>
			<td class="col-md-1 center d-none d-md-table-cell">
				<a id="forumbottom"> </a>
				<a href="#forumtop" rel="nofollow">
					<?php echo KunenaIcons::arrowup(); ?>
				</a>
			</td>
			<td colspan="8" class="d-none d-md-table-cell">
			</td>
		</tr>
		</tfoot>
	</table>

	<div class="float-start">
		<?php echo $this->subLayout('Widget/Pagination/List')
		->set('pagination', $this->pagination)
		->set('display', true); ?>
	</div>
</form>
<div class="clearfix"></div>
