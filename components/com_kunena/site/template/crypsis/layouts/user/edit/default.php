<?php
/**
 * Kunena Component
 * @package     Kunena.Template.Crypsis
 * @subpackage  Layout.User
 *
 * @copyright   (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        http://www.kunena.org
 **/
defined('_JEXEC') or die;
?>
<h2>
	<?php echo JText::_('COM_KUNENA_USER_PROFILE'); ?> <?php echo $this->escape($this->profile->getName()); ?>

	<?php echo $this->profile->getLink(
		'<i class="icon-arrow-left"></i> ' . JText::_('COM_KUNENA_BACK'),
		JText::_('COM_KUNENA_BACK'), 'nofollow', '', 'btn pull-right'
	); ?>
</h2>

<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=user'); ?>" method="post" enctype="multipart/form-data" name="kuserform" class="form-validate" id="kuserform">
	<input type="hidden" name="task" value="save" />
	<input type="hidden" name="userid" value="<?php echo (int) $this->user->id; ?>" />
	<?php echo JHtml::_('form.token'); ?>

	<div class="tabs">
		<ul id="KunenaUserEdit" class="nav nav-tabs">
			<li class="active">
				<a href="#home" data-toggle="tab">
					<?php echo JText::_('COM_KUNENA_PROFILE_EDIT_USER'); ?>
				</a>
			</li>
			<li>
				<a href="#editprofile" data-toggle="tab">
					<?php echo JText::_('COM_KUNENA_PROFILE_EDIT_PROFILE'); ?>
				</a>
			</li>
			<li>
				<a href="#editavatar" data-toggle="tab">
					<?php echo JText::_('COM_KUNENA_PROFILE_EDIT_AVATAR'); ?>
				</a>
			</li>
			<li>
				<a href="#editsettings" data-toggle="tab">
					<?php echo JText::_('COM_KUNENA_PROFILE_EDIT_SETTINGS'); ?>
				</a>
			</li>
		</ul>

		<div id="KunenaUserEdit" class="tab-content">
			<div class="tab-pane fade in active" id="home">
				<?php echo $this->subRequest('User/Edit/User'); ?>
			</div>
			<div class="tab-pane fade" id="editprofile">
				<?php echo $this->subRequest('User/Edit/Profile'); ?>
			</div>
			<div class="tab-pane fade" id="editavatar">
				<?php echo $this->subRequest('User/Edit/Avatar'); ?>
			</div>
			<div class="tab-pane fade" id="editsettings">
				<?php echo $this->subRequest('User/Edit/Settings'); ?>
			</div>

			<br />

			<div class="center">
				<button class="btn btn-primary validate" type="submit">
					<?php echo JText::_('COM_KUNENA_SAVE'); ?>
				</button>
				<input type="button" name="cancel" class="btn"
				       value="<?php echo (' ' . JText::_('COM_KUNENA_CANCEL') . ' '); ?>"
				       onclick="window.history.back();"
				       title="<?php echo (JText::_('COM_KUNENA_EDITOR_HELPLINE_CANCEL')); ?>" />
			</div>
		</div>
	</div>
</form>
