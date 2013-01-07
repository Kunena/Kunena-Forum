<?php
/**
 * Kunena Component
 * @package Kunena.Administrator.Template
 * @subpackage Attachments
 *
 * @copyright (C) 2008 - 2012 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

$document = JFactory::getDocument();
$document->addStyleSheet ( JURI::base(true).'/components/com_kunena/media/css/admin.css' );
if (JFactory::getLanguage()->isRTL()) $document->addStyleSheet ( JURI::base().'components/com_kunena/media/css/admin.rtl.css' );
?>
<div id="kadmin">
	<div class="kadmin-left"><?php include KPATH_ADMIN.'/views/common/tmpl/menu.php'; ?></div>
	<div class="kadmin-right">
	<div class="kadmin-functitle icon-files"><?php echo JText::_('COM_KUNENA_ATTACHMENTS_VIEW'); ?></div>
		<form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena') ?>" method="post" id="adminForm" name="adminForm">
			<input type="hidden" name="view" value="attachments" />
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="filter_order" value="<?php echo $this->state->get('list.ordering') ?>" />
			<input type="hidden" name="filter_order_Dir" value="<?php echo $this->escape ($this->state->get('list.direction')) ?>" />
			<input type="hidden" name="limitstart" value="<?php echo intval ( $this->navigation->limitstart ) ?>" />
			<input type="hidden" name="boxchecked" value="0" />
			<?php echo JHTML::_( 'form.token' ); ?>

			<table class="kadmin-sort">
			<tr>
					<td class="left" width="90%">
						<?php echo JText::_( 'COM_KUNENA_FILTER' ); ?>:
						<input type="text" name="search" id="search" value="<?php echo $this->escape ($this->state->get('list.search'));?>" class="text_area" onchange="document.adminForm.submit();" />
						<button onclick="this.form.submit();"><?php echo JText::_( 'COM_KUNENA_GO' ); ?></button>
						<button onclick="document.getElementById('search').value='';this.form.submit();"><?php echo JText::_( 'COM_KUNENA_RESET' ); ?></button>
					</td>
				</tr>
			</table>
			<table class="adminlist">
				<thead>
					<tr>
						<th align="center" width="5">#</th>
						<th width="5"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count ( $this->items ); ?>);" /></th>
						<th class="title"><?php echo JHTML::_('grid.sort', 'COM_KUNENA_FILENAME', 'a.filename', $this->state->get('list.direction'), $this->state->get('list.ordering') ); ?></th>
						<th class="center"><?php echo JHTML::_('grid.sort', 'COM_KUNENA_ATTACHMENTS_ID', 'a.id', $this->state->get('list.direction'), $this->state->get('list.ordering') ); ?></th>
						<th class="center"><?php echo JHTML::_('grid.sort', 'COM_KUNENA_ATTACHMENTS_FILETYPE', 'a.filetype', $this->state->get('list.direction'), $this->state->get('list.ordering') ); ?></th>
						<th class="center"><?php echo JHTML::_('grid.sort', 'COM_KUNENA_FILESIZE', 'a.size', $this->state->get('list.direction'), $this->state->get('list.ordering') ); ?>
						<th class="center"><?php echo JText::_('COM_KUNENA_A_IMGB_DIMS'); ?>	</th>
						<th class="center"><?php echo JText::_('COM_KUNENA_ATTACHMENTS_USERNAME'); ?></th>
						<th class="center"><?php echo JText::_('COM_KUNENA_MESSAGE'); ?></th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<td colspan="14">
							<div class="pagination">
								<div class="limit"><?php echo JText::_('COM_KUNENA_A_DISPLAY'); ?> <?php echo $this->navigation->getLimitBox (); ?></div>
								<?php echo $this->navigation->getPagesLinks (); ?>
								<div class="limit"><?php echo $this->navigation->getResultsCounter (); ?></div>
							</div>
						</td>
					</tr>
				</tfoot>
		<?php
			$k = 0;
			$i = 0;
			$n = count($this->items);
			foreach($this->items as $id=>$attachment) {
				$instance = KunenaForumMessageAttachmentHelper::get($attachment->id);
				$message = $instance->getMessage();
				$path = JPATH_ROOT.'/'.$attachment->folder.'/'.$attachment->filename;
				if ( $instance->isImage($attachment->filetype) && is_file($path)) list($width, $height) =	getimagesize( $path );
		?>
			<tr <?php echo 'class = "row' . $k . '"';?>>
				<td class="right"><?php echo $i + $this->navigation->limitstart + 1; ?></td>
				<td><?php echo JHTML::_('grid.id', $i, intval($attachment->id)) ?></td>
				<td class="left" width="70%"><?php echo $instance->getThumbnailLink() . ' ' . KunenaForumMessageAttachmentHelper::shortenFileName($attachment->filename, 10, 15) ?></td>
				<td class="center"><?php echo intval($attachment->id); ?></td>
				<td class="center"><?php echo $this->escape($attachment->filetype); ?></td>
				<td class="center"><?php echo number_format ( intval ( $attachment->size ) / 1024, 0, '', ',' ) . ' KB'; ?></td>
				<td class="center"><?php echo isset($width) && isset($height) ? $width . ' x ' . $height  : '' ?></td>
				<td class="center"><?php echo $this->escape($message->name); ?></td>
				<td class="center"><?php echo $this->escape($message->subject); ?></td>
			</tr>
				<?php
				$i++;
				$k = 1 - $k;
				}
				?>
		</table>

		</form>
	</div>
	<div class="kadmin-footer">
		<?php echo KunenaVersion::getLongVersionHTML (); ?>
	</div>
</div>

