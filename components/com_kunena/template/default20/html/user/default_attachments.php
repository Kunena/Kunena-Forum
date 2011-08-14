<?php
/**
 * Kunena Component
 * @package Kunena.Template.Default
 * @subpackage User
 *
 * @copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

$this->setTitle(JText::_('COM_KUNENA_VIEW_USERS_DEFAULT'));
if (version_compare(JVERSION, '1.5','=')) {
	// FIXME: Isn't there a better way to load this file?
	// I don't think so that this file can be loaded by an another way under Joomla! 1.5
	$this->document->addScript(JURI::Root()."includes/js/joomla.javascript.js");
} elseif (version_compare(JVERSION, '1.6','=')) {
	JHtml::_('script','system/multiselect.js',false,true);
} elseif (version_compare(JVERSION, '1.7','>'))  {
	JHtml::_('behavior.multiselect');
}
?>

<div class="kblock">
<div class="kheader">
	<h2><span><?php echo $this->title;?></span></h2>
</div>
	<div class="kcontainer">
		<div class="kbody">
			<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena') ?>" method="post" name="adminForm" id="adminForm">
				<input type="hidden" name="view" value="user">
				<input type="hidden" name="task" value="delfile" />

				<?php
				if ( empty($this->items) ):
					 echo JText::_('COM_KUNENA_USER_NO_ATTACHMENTS');
					else:
				$i=0;
				$y=1;
				foreach ($this->items as $file) :
					$instance = KunenaForumMessageAttachmentHelper::get($file->id);

					$evenodd = $i % 2;

					if ($evenodd == 0)	$usrl_class="row1";
					else $usrl_class="row2"; ?>

					<li class="kposts-row ">
						<table summary="List of all forum categories with posts and replies of each">
							<tbody>
								<tr>
									<td class="kposts-topic">
										<h3><?php echo $y; ?></h3>
									</td>
									<td class="ktopic-icon"><?php echo JHTML::_('grid.id', $i, intval($file->id)) ?></td>
									<td class="kpost-topic">
										<img src="<?php echo $file->filetype != '' ? JURI::root().'media/kunena/icons/image.png' : JURI::root().'media/kunena/icons/file.png'; ?>" alt="" title="" />
									</td>
									<td class="kpost-topic">
									 <?php echo $file->filename; ?>
									</td>
									<td class="kpost-topic">
									 <?php echo number_format ( intval ( $file->size ) / 1024, 0, '', ',' ) . ' KB'; ?>
									</td>
									<td class="kpost-topic">
									 <?php echo $instance->getThumbnailLink() ; ?>
									</td>
									<td class="kpost-topic">
										<a href="javascript:void(0);" onclick="return listItemTask('cb<?php
										echo $i;
										?>','delete')"><img src="<?php echo $this->template->getImagePath('icons/publish_x.png') ?>" alt="" title="" /></a>
									</td>
								</tr>
							</tbody>
						</table>
					</li>

					<?php $i++; $y++; endforeach; endif; ?>
				<div id="ksection-modbox">
					<input class="kbutton" type="submit" value="<?php echo JText::_('COM_KUNENA_FILES_DELETE') ?>" style="float:right;" />
					<?php if (version_compare(JVERSION, '1.6','<')): ?>
					<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count ( $this->items ); ?>);" />
					<?php else: ?>
					<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('COM_KUNENA_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
					<?php endif; ?>
				</div>
				<input type="hidden" name="boxchecked" value="0" />
				<?php echo JHTML::_( 'form.token' ); ?>
			</form>
		</div>
	</div>
</div>
