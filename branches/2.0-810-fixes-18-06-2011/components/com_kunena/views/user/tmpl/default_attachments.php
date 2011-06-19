<?php
/**
 * @version $Id: list.php 4809 2011-04-26 11:24:56Z mahagr $
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

$this->setTitle(JText::_('COM_KUNENA_VIEW_USERS_DEFAULT'));
$doc = JFactory::getDocument();
// FIXME: Isn't there a better way to load this file?
$doc->addScript(JURI::Root()."includes/js/joomla.javascript.js");
?>

<div class="kblock">
<div class="kheader">
  <h2><span><?php echo $this->title;?></span></h2>
</div>
	<div class="kcontainer">
		<div class="kbody">
			<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena') ?>" method="post" name="adminForm">
				<input type="hidden" name="view" value="user">
				<input type="hidden" name="task" value="delfile" />
				<table width="100%">
					<tr class="ksth">
						<th class="frst"> # </th>
						<th width="5"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count ( $this->items ); ?>);" /></th>
						<th><?php echo JText::_('COM_KUNENA_FILETYPE'); ?></th>

						<th><?php echo JText::_('COM_KUNENA_FILENAME'); ?></th>

						<th><?php echo JText::_('COM_KUNENA_FILESIZE'); ?></th>

						<th><?php echo JText::_('COM_KUNENA_FILE_PREVIEW'); ?></th>

						<th><?php echo JText::_('COM_KUNENA_FILE_DELETE'); ?></th>
					</tr>

					<?php
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
						?>','delete')"><img src="<?php echo JURI::root().'components/com_kunena/template/default/images/icons/publish_x.png'; ?>" alt="" title="" /></a></td>

					</tr>
					<?php $i++; $y++; endforeach; ?>
				</table>
				<input class="kbutton" type="submit" value="<?php echo JText::_('COM_KUNENA_FILES_DELETE') ?>" style="float:right;" />
				<input type="hidden" name="boxchecked" value="0" />
				<?php echo JHTML::_( 'form.token' ); ?>
			</form>
		</div>
	</div>
</div>
