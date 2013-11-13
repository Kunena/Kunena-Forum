<?php
/**
 * Kunena Component
 * @package     Kunena.Template.Crypsis
 * @subpackage  Layout.User
 *
 * @copyright   (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        http://www.kunena.org
 **/
defined('_JEXEC') or die;

/** @var array|KunenaForumMessageAttachment[] $attachments */
$attachments = $this->attachments;
?>
<h3>
	<?php echo $this->headerText; ?>
</h3>

<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=user'); ?>" method="post" id="adminForm"
      name="adminForm">
	<input type="hidden" name="task" value="delfile" />
	<input type="hidden" name="boxchecked" value="0" />
	<?php echo JHtml::_('form.token'); ?>

	<table class="table table-bordered table-striped table-hover">
		<thead>
			<tr>
				<th>
					#
				</th>
				<th width="5">
					<label>
						<input type="checkbox" name="checkall-toggle" value="cid"
						       title="<?php echo JText::_('COM_KUNENA_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
					</label>
				</th>
				<th>
					<?php echo JText::_('COM_KUNENA_FILETYPE'); ?>
				</th>
				<th>
					<?php echo JText::_('COM_KUNENA_FILENAME'); ?>
				</th>
				<th>
					<?php echo JText::_('COM_KUNENA_FILESIZE'); ?>
				</th>
				<th>
					<?php echo JText::_('COM_KUNENA_ATTACHMENT_MANAGER_TOPIC'); ?>
				</th>
				<th>
					<?php echo JText::_('COM_KUNENA_PREVIEW'); ?>
				</th>
				<th>
					<?php echo JText::_('COM_KUNENA_DELETE'); ?>
				</th>
			</tr>
		</thead>
		<tbody>
			<?php
				if (!$attachments) :
					echo JText::_('COM_KUNENA_USER_NO_ATTACHMENTS');
				else :
					$i=0;
					foreach ($attachments as $attachment) :
						$message = $attachment->getMessage();
						$canDelete = $attachment->isAuthorised('delete');
			?>
			<tr>
				<td><?php echo ++$i; ?></td>
				<td>
					<?php if ($canDelete) echo JHtml::_('grid.id', $i-1, intval($attachment->id)); ?>
				</td>
				<td class="center">
					<img src="<?php echo $attachment->isImage()
						? JUri::root(true).'/media/kunena/icons/image.png'
						: JUri::root(true).'/media/kunena/icons/file.png'; ?>" alt="" title="" />
				</td>
				<td>
					<?php echo $attachment->getFilename(); ?>
				</td>
				<td>
					<?php echo number_format(intval($attachment->size) / 1024, 0, '', ',') . ' ' . JText::_('COM_KUNENA_USER_ATTACHMENT_FILE_WEIGHT'); ?>
				</td>
				<td>
					<?php echo $this->getTopicLink($message->getTopic(), $message); ?>
				</td>
				<td class="center">
					<?php echo $attachment->getThumbnailLink() ; ?>
				</td>
				<td class="center">

					<?php if ($canDelete) : ?>
					<a href="javascript:void(0);" onclick="return listItemTask('cb<?php echo $i; ?>','delfile');">
						<img src="<?php echo $this->template->getImagePath('icons/publish_x.png'); ?>" alt="" title="" />
					</a>
					<?php endif ?>

				</td>
			</tr>
			<?php
					endforeach;
				endif;
			?>
		</tbody>
	</table>

	<input class="btn pull-right" type="submit" value="<?php echo JText::_('COM_KUNENA_FILES_DELETE'); ?>" />
</form>
