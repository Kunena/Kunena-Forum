<?php
/**
 * Kunena Component
 *
 * @package     Kunena.Template.Crypsis
 * @subpackage  Layout.Widget
 *
 * @copyright   (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        http://www.kunena.org
 **/
defined('_JEXEC') or die;

$markAllReadUrl = KunenaForumCategoryHelper::get()->getMarkReadUrl();
// FIXME: move announcements logic and pm logic into the template file...
?>
<ul class="nav pull-right">
	<li class="dropdown mobile-user">
		<a href="#" class="dropdown-toggle" data-toggle="dropdown">
			<?php if ($this->me->status == 0) : ?>
				<i class="icon-large icon-user green"></i>
			<?php elseif ($this->me->status == 1) : ?>
				<i class="icon-large icon-user yellow"></i>
			<?php elseif ($this->me->status == 2) : ?>
				<i class="icon-large icon-user red "></i>
			<?php elseif ($this->me->status == 3) : ?>
				<i class="icon-large icon-user grey"></i>
			<?php endif; ?>
			<b class="caret"></b>
		</a>

		<div class="dropdown-menu" id="nav-menu">

			<div class="center">
				<p><strong><?php echo $this->me->getLink(null, null, 'nofollow', '', null); ?></strong></p>
				<a href="<?php echo $this->me->getURL(); ?>">
					<?php echo $this->me->getAvatarImage('img-polaroid', 128, 128); ?>
				</a>
				<p>
					<i class="icon-clock"></i>
					<?php echo $this->me->getLastVisitDate()->toKunena('config_post_dateformat'); ?>
				</p>
			</div>
			<div class="divider"></div>
			<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena'); ?>" method="post" id="status-form" class="form-inline">
				<div>
					<input id="status-online" class="hide" type="radio" value="0" name="status" />
					<label for="status-online" class="btn btn-link">
						<a href="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=user&task=status&status=0&' . JSession::getFormToken() . '=1'); ?>" class="btn btn-link">
							<i class="icon-plus green"></i>
							<?php echo JText::_('COM_KUNENA_ONLINE') ?>
						</a>
					</label>
				</div>

				<div>
					<input id="status-away" class="hide" type="radio" value="1" name="status" />
					<label for="status-away" class="btn btn-link">
						<a href="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=user&task=status&status=1&' . JSession::getFormToken() . '=1'); ?>" class="btn btn-link">
							<i class="icon-plus yellow"></i>
							<?php echo JText::_('COM_KUNENA_AWAY') ?>
						</a>
					</label>
				</div>
				<div>
					<input id="status-busy" class="hide" type="radio" value="2" name="status" />
					<label for="status-busy" class="btn btn-link">
						<a href="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=user&task=status&status=2&' . JSession::getFormToken() . '=1');; ?>" class="btn btn-link">
							<i class="icon-minus red"></i>
							<?php echo JText::_('COM_KUNENA_BUSY') ?>
						</a>
					</label>
				</div>
				<div>
					<input id="status-invisible" class="hide" type="radio" value="3" name="status" />
					<label for="status-invisible" class="btn btn-link">
						<a href="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=user&task=status&status=3&' . JSession::getFormToken() . '=1'); ?>" class="btn btn-link">
							<i class="icon-minus grey"></i>
							<?php echo JText::_('COM_KUNENA_INVISIBLE') ?>
						</a>
					</label>
				</div>
				<input type="hidden" name="view" value="user" />
				<input type="hidden" name="task" value="status" />
				<?php echo JHtml::_('form.token'); ?>
			</form>
			<div class="divider"></div>
			<div>
				<?php JHtml::_('bootstrap.modal', 'statusText'); ?>
				<a data-toggle="modal" data-target="#statusTextModal" class="btn btn-link">
					<i class="icon-pencil green"></i>
					<?php echo JText::_('COM_KUNENA_STATUS') ?>
				</a>
			</div>
			<div class="divider"></div>

			<?php if (!empty($this->announcementsUrl)) : ?>
				<div>
					<a href="<?php echo $this->announcementsUrl; ?>" class="btn btn-link">
						<i class="icon-pencil-2"></i>
						<?php echo JText::_('COM_KUNENA_ANN_ANNOUNCEMENTS') ?>
					</a>
				</div>
			<?php endif; ?>

			<?php if (!empty($this->pm_link)) : ?>
				<div>
					<a href="<?php echo $this->pm_link; ?>" class="btn btn-link">
						<i class="icon-mail"></i>
						<?php echo $this->inboxCount; ?>
					</a>
				</div>
			<?php endif; ?>

			<div>
				<a href="<?php echo $this->me->getUrl(false, 'edit'); ?>" class="btn btn-link">
					<i class="icon-cog"></i>
					<?php echo JText::_('COM_KUNENA_LOGOUTMENU_LABEL_PREFERENCES'); ?>
				</a>
			</div>
			<div class="divider"></div>

			<?php if ($markAllReadUrl) : ?>
				<div>
					<a href="<?php echo $markAllReadUrl; ?>" class="btn btn-link">
						<i class="icon-drawer"></i>
						<?php echo JText::_('COM_KUNENA_MARK_ALL_READ'); ?>
					</a>
				</div>
			<?php endif ?>
			<div class="divider"></div>

			<?php echo $this->subLayout('Widget/Module')->set('position', 'kunena_logout'); ?>

			<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena'); ?>" method="post" id="logout-form" class="form-inline">
				<div>
					<button class="btn btn-link" name="submit" type="submit">
						<i class="icon-out"></i>
						<?php echo JText::_('COM_KUNENA_PROFILEBOX_LOGOUT'); ?>
					</button>
				</div>
				<input type="hidden" name="view" value="user" />
				<input type="hidden" name="task" value="logout" />
				<?php echo JHtml::_('form.token'); ?>
			</form>
		</div>
	</li>
</ul>
<?php
/* Note these have to be outsize the dropdown as z-index stack context is different
from the parent forcing the dropsown to take over z-index calculation */
?>
<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena'); ?>" method="post" id="statusText-form" class="form-inline">
	<?php echo $this->subLayout('Widget/Modal')
		->set('id', 'statusTextModal')
		->set('name', 'status_text')
		->set('label', JText::_('COM_KUNENA_STATUS_MESSAGE'))
		->set('description', JText::_('COM_KUNENA_STATUS_TYP'))
		->set('data', $this->me->status_text)
		->set('form', 'statusText-form'); ?>
	<input type="hidden" name="view" value="user" />
	<input type="hidden" name="task" value="statustext" />
	<?php echo JHtml::_('form.token'); ?>
</form>
<script type='text/javascript'>
	jQuery(document).ready(function ($) {
		$("input[name=status]").change(function () {
			$("#status-form").submit();
		});
	});
</script>
