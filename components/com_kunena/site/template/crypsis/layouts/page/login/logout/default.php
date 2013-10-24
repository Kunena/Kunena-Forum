<?php
/**
 * Kunena Component
 * @package Kunena.Template.Crypsis
 * @subpackage Common
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
$markAllReadUrl = KunenaForumCategoryHelper::get()->getMarkReadUrl();
?>
<ul class="nav pull-right">
	<li class="dropdown">
		<a href="#" class="dropdown-toggle" data-toggle="dropdown">
			<i class="icon-large icon-user"></i> <b class="caret"></b>
		</a>
		<div class="dropdown-menu well well-small">
			<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena'); ?>" method="post" id="logout-form" class="form-inline">
				<div class="center">
					<a href="<?php  echo $this->me->getURL(); ?>">
						<?php echo $this->me->getAvatarImage('img-polaroid'); ?>
					</a>
					<p>
						<strong><?php echo $this->escape($this->me->get('name')); ?></strong>
					</p>
					<p>
						<i class="icon-clock"></i>
						<?php echo $this->lastvisitDate->toSpan('datetime_today', 'ago'); ?>
					</p>
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
					<a href="<?php echo $this->pm_link ?>" class="btn btn-small btn-link">
						<i class="icon-mail"></i>
						<?php echo $this->inboxCount ?>
					</a>
				</div>
				<?php endif ?>
				<div>
					<a href="<?php echo $this->me->getUrl(false, 'edit') ?>" class="btn btn-small btn-link">
						<i class="icon-cog"></i>
						<?php echo JText::_('COM_KUNENA_LOGOUTMENU_LABEL_PREFERENCES') ?>
					</a>
				</div>
				<div class="divider"></div>

				<?php if ($markAllReadUrl) : ?>
				<a href="<?php echo $markAllReadUrl ?>" class="btn btn-small btn-link">
					<i class="icon-out"></i>
					<?php echo JText::_('COM_KUNENA_MARK_ALL_READ') ?>
				</a>
				<?php endif ?>

				<button class="btn btn-small btn-link" name="submit" type="submit">
					<i class="icon-out"></i>
					<?php echo JText::_('COM_KUNENA_PROFILEBOX_LOGOUT'); ?>
				</button>

				<input type="hidden" name="view" value="user" />
				<input type="hidden" name="task" value="logout" />
				<?php echo JHtml::_('form.token'); ?>
			</form>
		</div>
	</li>
</ul>
