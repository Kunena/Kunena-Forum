<?php
/**
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

if (version_compare(JVERSION, '1.7','>')) {
	// Joomla 1.7+
	JHtml::_('behavior.multiselect');
} elseif (version_compare(JVERSION, '1.6','>')) {
	// Joomla 1.6
	JHtml::_('script','system/multiselect.js',false,true);
} else {
	// Joomla 1.5
	$this->document->addScript(JURI::Root(true).'/includes/js/joomla.javascript.js');
}
?>
<div class="forumlist">
	<div class="inner">
		<span class="corners-top"><span></span></span>
			<ul class="topiclist">
				<li class="header">
					<dl class="icon">
						<dt><?php echo $this->title;?></dt>
						<dd>&nbsp;</dd>
					</dl>
				</li>
			</ul>

	<div class="kcontainer">
		<div class="kbody">
			<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena') ?>" method="post" name="adminForm">
				<input type="hidden" name="view" value="user">
				<input type="hidden" name="task" value="delfile" />
				<input type="hidden" name="boxchecked" value="0" />
				<?php echo JHTML::_( 'form.token' ); ?>

				<table>
					<tr class="ksth">
						<th class="frst"> # </th>
						<th width="5"><?php if (version_compare(JVERSION, '1.6','>')): ?>
							<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('COM_KUNENA_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
							<?php else: ?>
							<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count ( $this->items ); ?>);" />
							<?php endif; ?></th>
						<th><?php echo JText::_('COM_KUNENA_FILETYPE'); ?></th>

						<th><?php echo JText::_('COM_KUNENA_FILENAME'); ?></th>

						<th><?php echo JText::_('COM_KUNENA_FILESIZE'); ?></th>

						<th><?php echo JText::_('COM_KUNENA_FILE_PREVIEW'); ?></th>

						<th><?php echo JText::_('COM_KUNENA_FILE_DELETE'); ?></th>
					</tr>

					<?php
					if ( empty($this->items) ):
						echo '<li>'.JText::_('COM_KUNENA_USER_NO_ATTACHMENTS').'</li>';
						else:
					$i=0;
					$y=1;
					foreach ($this->items as $file) :
						$instance = KunenaForumMessageAttachmentHelper::get($file->id);

						$evenodd = $i % 2;

						if ($evenodd == 0)	$usrl_class="row1";
						else $usrl_class="row2";
					?>

					<tr class="k<?php echo $usrl_class ;?>">
						<td class="kcol-first"><?php echo $y; ?></td>
						<td class="kcol-mid"><?php echo JHTML::_('grid.id', $i, intval($file->id)) ?></td>
						<td align="center" class="kcol-mid"><img src="<?php echo $file->filetype != '' ? JURI::root().'media/kunena/icons/image.png' : JURI::root().'media/kunena/icons/file.png'; ?>" alt="" title="" /></td>

						<td class="kcol-mid"><?php echo $file->filename; ?></td>

						<td class="kcol-mid"><?php echo number_format ( intval ( $file->size ) / 1024, 0, '', ',' ) . ' KB'; ?></td>

						<td align="center" class="kcol-mid"><?php echo $instance->getThumbnailLink() ; ?></td>

						<td align="center" class="kcol-mid"><a href="javascript:void(0);" onclick="return listItemTask('cb<?php
						echo $i;
						?>','delete')"><img src="<?php echo $this->template->getImagePath('icons/publish_x.png') ?>" alt="" title="" /></a></td>

					</tr>
					<?php $i++; $y++; endforeach; endif; ?>
				</table>
				<input class="tk-submit-button" type="submit" value="<?php echo JText::_('COM_KUNENA_FILES_DELETE') ?>" style="float:right;" />
			</form>
		</div>
	</div>
		<span class="corners-bottom"><span></span></span>
	</div>
</div>
