<?php
/**
 * Kunena Component
 * @package         Kunena.Template.Crypsisb3
 * @subpackage      Layout.User
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

HTMLHelper::_('behavior.core');

$attachments = $this->attachments;
?>
<h3>
	<?php echo $this->headerText; ?>
</h3>

<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=user'); ?>" method="post" id="adminForm"
      name="adminForm">
	<input type="hidden" name="task" value="delfile"/>
	<input type="hidden" name="boxchecked" value="0"/>
	<?php echo HTMLHelper::_('form.token'); ?>

	<table class="table table-bordered table-striped table-hover">
		<thead>
		<tr>
			<th class="col-md-1 center">
				#
			</th>
			<?php if ($this->me->userid == $this->profile->userid || KunenaUserHelper::getMyself()->isModerator()) :?>
			<th class="col-md-1 center">
				<label>
					<input type="checkbox" name="checkall-toggle" value="cid"
					       title="<?php echo Text::_('COM_KUNENA_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)"/>
				</label>
			</th>
			<?php endif; ?>
			<th class="col-md-1 center">
				<?php echo Text::_('COM_KUNENA_FILETYPE'); ?>
			</th>
			<th class="col-md-2">
				<?php echo Text::_('COM_KUNENA_FILENAME'); ?>
			</th>
			<th class="col-md-2">
				<?php echo Text::_('COM_KUNENA_FILESIZE'); ?>
			</th>
			<th class="col-md-2">
				<?php echo Text::_('COM_KUNENA_ATTACHMENT_MANAGER_TOPIC'); ?>
			</th>
			<th class="col-md-1 center">
				<?php echo Text::_('COM_KUNENA_PREVIEW'); ?>
			</th>
			<?php if ($this->me->userid == $this->profile->userid || KunenaUserHelper::getMyself()->isModerator()) :?>
			<th class="col-md-1 center">
				<?php echo Text::_('COM_KUNENA_DELETE'); ?>
			</th>
			<?php endif; ?>
		</tr>
		</thead>
		<tbody>
		<?php if (!$attachments) : ?>
			<tr>
				<td colspan="8">
					<?php echo Text::_('COM_KUNENA_USER_NO_ATTACHMENTS'); ?>
				</td>
			</tr>
		<?php else :
			$i = $this->pagination->limitstart;
			foreach ($attachments as $attachment) :
				$message = $attachment->getMessage();
				$canDelete = $attachment->isAuthorised('delete');

				if ($attachment->isAuthorised('read', $this->me)) :
				?>
				<tr>
					<td class="center"><?php echo ++$i; ?></td>
					<?php if ($canDelete) : ?>
					<td class="center">
						<?php echo HTMLHelper::_('grid.id', $i, intval($attachment->id)); ?>
					</td>
					<?php endif; ?>
					<td class="center">
						<?php echo $attachment->isImage() ? KunenaIcons::picture() : KunenaIcons::file(); ?>
					</td>
					<td>
						<?php echo $attachment->getShortName(10, 5); ?>
					</td>
					<td>
						<?php echo number_format(intval($attachment->size) / 1024, 0, '', ',') . ' ' . Text::_('COM_KUNENA_USER_ATTACHMENT_FILE_WEIGHT'); ?>
					</td>
					<td>
						<?php echo $this->getTopicLink($message->getTopic(), $message, null, null, '', null, false, true); ?>
					</td>
					<td class="center">
						<?php echo $attachment->getLayout()->render('thumbnail'); ?>
					</td>
					<?php if ($canDelete) : ?>
					<td class="center">
							<a href="#modaldelete<?php echo $i ?>" role="button" class="btn center"
							   data-toggle="modal"><?php echo KunenaIcons::delete(); ?></a>

							<div class="modal fade" id="modaldelete" tabindex="-1" role="dialog"
							     aria-labelledby="modaldeleteLabel">
								<div class="modal-dialog" role="document">
									<div class="modal-content">
										<div class="modal-header">
											<button type="button" class="close" data-dismiss="modal" aria-label="Close">
												<span aria-hidden="true">&times;</span></button>
											<h4 class="modal-title"
											    id="myModalLabel"><?php echo Text::_('COM_KUNENA_FILES_CONFIRMATION_DELETE_MODAL_LABEL ') ?></h4>
										</div>
										<div class="modal-body">
											<p><?php echo Text::sprintf('COM_KUNENA_FILES_DELETE_MODAL_DESCRIPTION', $attachment->getFilename(), number_format(intval($attachment->size) / 1024, 0, '', ',') . ' ' . Text::_('COM_KUNENA_USER_ATTACHMENT_FILE_WEIGHT')); ?></p>
										</div>
										<div class="modal-footer">
											<button type="button" class="btn btn-default"
											        data-dismiss="modal"><?php echo Text::_('COM_KUNENA_FILES_CONFIRM_DELETE_MODAL_BUTTON') ?></button>
											<button type="button"
											        class="btn btn-primary"><?php echo Text::_('COM_KUNENA_FILES_CANCEL_DELETE_MODAL_BUTTON') ?></button>
										</div>
									</div>
								</div>
							</div>
					</td>
					<?php endif; ?>
				</tr>
				<?php endif; ?>
			<?php endforeach; ?>
		<?php endif; ?>
		</tbody>
	</table>
	<div class="pull-left">
		<?php echo $this->subLayout('Widget/Pagination/List')
			->set('pagination', $this->pagination->setDisplayedPages(4))
			->set('display', true); ?>
	</div>
	<?php if ($attachments && $this->me->userid == $this->profile->userid || $attachments && KunenaUserHelper::getMyself()->isModerator()) : ?>
		<a href="#modaldeleteall" class="btn btn-default pull-right"
		   data-toggle="modal"><?php echo Text::_('COM_KUNENA_FILES_DELETE'); ?></a>

		<div class="modal fade" id="modaldelete" tabindex="-1" role="dialog" aria-labelledby="modaldeleteLabel">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
									aria-hidden="true">&times;</span></button>
						<h4 class="modal-title"
						    id="myModalLabel"><?php echo Text::_('COM_KUNENA_FILES_CONFIRMATION_DELETE_MODAL_LABEL ') ?></h4>
					</div>
					<div class="modal-body">
						<p><?php echo Text::_('COM_KUNENA_FILES_DELETE_SELECTED_MODAL_DESCRIPTION'); ?></p>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default"
						        data-dismiss="modal"><?php echo Text::_('COM_KUNENA_FILES_CONFIRM_DELETE_MODAL_BUTTON') ?></button>
						<button type="button" class="btn btn-primary"
						        onclick="adminform.submit();"><?php echo Text::_('COM_KUNENA_FILES_CANCEL_DELETE_MODAL_BUTTON') ?></button>
					</div>
				</div>
			</div>
		</div>
	<?php endif; ?>
</form>
