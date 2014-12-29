<?php
/**
 * Kunena Component
 * @package     Kunena.Template.Crypsis
 * @subpackage  Layout.Widget
 *
 * @copyright   (C) 2008 - 2014 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        http://www.kunena.org
 **/
defined('_JEXEC') or die;

$markAllReadUrl = KunenaForumCategoryHelper::get()->getMarkReadUrl();
// FIXME: move announcements logic and pm logic into the template file...
?>
<ul class="nav pull-right">
	<li class="dropdown">
		<a href="#" class="dropdown-toggle" data-toggle="dropdown">
			<i class="icon-large icon-user"></i> <b class="caret"></b>
		</a>

		<div class="dropdown-menu">
			<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena'); ?>" method="post" id="logout-form" class="form-inline">
				<div class="center">
					<a href="<?php  echo $this->me->getURL(); ?>">
						<?php echo $this->me->getAvatarImage('img-thumbnail', 128); ?>
					</a>
					<p>
						<strong><?php echo $this->escape($this->me->getName()); ?></strong>
					</p>
					<p>
						<i class="icon-clock"></i>
						<?php echo $this->me->getLastVisitDate()->toKunena('config_post_dateformat', 'ago'); ?>
					</p>
				</div>
				<div class="divider"></div>

				<?php if (!empty($this->announcementsUrl)) : ?>
				<div>
					<a href="<?php echo $this->announcementsUrl; ?>" class="btn btn-small btn-link">
						<i class="icon-pencil-2"></i>
						<?php echo JText::_('COM_KUNENA_ANN_ANNOUNCEMENTS') ?>
					</a>
				</div>
				<?php endif; ?>

				<?php if (!empty($this->pm_link)) : ?>
				<div>
					<a href="<?php echo $this->pm_link; ?>" class="btn btn-small btn-link">
						<i class="icon-mail"></i>
						<?php echo $this->inboxCount; ?>
					</a>
				</div>
				<?php endif; ?>

				<div>
					<a href="<?php echo $this->me->getUrl(false, 'edit'); ?>" class="btn btn-small btn-link">
						<i class="icon-cog"></i>
						<?php echo JText::_('COM_KUNENA_LOGOUTMENU_LABEL_PREFERENCES'); ?>
					</a>
				</div>
				<div class="divider"></div>

				<?php if ($markAllReadUrl) : ?>
				<a href="<?php echo $markAllReadUrl; ?>" class="btn btn-small btn-link">
					<i class="icon-drawer"></i>
					<?php echo JText::_('COM_KUNENA_MARK_ALL_READ'); ?>
				</a>
				<?php endif ?>
				<div class="divider"></div>

				<button class="btn-link" name="submit" type="submit">
					<a><i class="icon-out"></i>
						<?php echo JText::_('COM_KUNENA_PROFILEBOX_LOGOUT'); ?></a>
				</button>

				<input type="hidden" name="view" value="user" />
				<input type="hidden" name="task" value="logout" />
				<?php echo JHtml::_('form.token'); ?>
			</form>
		</div>
	</li>
</ul>
