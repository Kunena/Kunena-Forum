<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Administrator.Template
 * @subpackage      Attachments
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\WebAsset\WebAssetManager;
use Kunena\Forum\Libraries\Version\KunenaVersion;
use Kunena\Forum\Libraries\Route\KunenaRoute;

/** @var WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa->useScript('multiselect');
$wa->addInlineScript(
	'Joomla.orderTable = function () {
		var table = document.getElementById("sortTable");
		var direction = document.getElementById("directionTable");
		var order = table.options[table.selectedIndex].value;
		if (order != ' . $this->list->Ordering . ') {
dirn = "asc";
} else {
dirn = direction.options[direction.selectedIndex].value;
}
Joomla.tableOrdering(order, dirn, "");
}'
);
?>

<div id="kunena" class="container-fluid">
	<div class="row">
		<div id="j-main-container" class="col-md-12" role="main">
			<div class="card card-block bg-faded p-2">
				<form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=attachments') ?>"
					  method="post" id="adminForm"
					  name="adminForm">
					<input type="hidden" name="task" value=""/>
					<input type="hidden" name="boxchecked" value="0"/>
					<input type="hidden" name="filter_order" value="<?php echo $this->list->Ordering; ?>"/>
					<input type="hidden" name="filter_order_Dir" value="<?php echo $this->list->Direction; ?>"/>
					<?php echo HTMLHelper::_('form.token'); ?>

					<div id="filter-bar" class="btn-toolbar">
						<div class="filter-search btn-group pull-left">
							<label for="filter_search"
								   class="element-invisible"><?php echo Text::_('COM_KUNENA_FIELD_LABEL_SEARCHIN'); ?></label>
							<input type="text" name="filter_search" id="filter_search" class="filter form-control"
								   placeholder="<?php echo Text::_('COM_KUNENA_CATEGORIES_FIELD_INPUT_SEARCHCATEGORIES'); ?>"
								   value="<?php echo $this->filter->Search; ?>"
								   title="<?php echo Text::_('COM_KUNENA_CATEGORIES_FIELD_INPUT_SEARCHCATEGORIES'); ?>"/>
						</div>
						<div class="btn-group pull-left">
							<button class="btn btn-outline-primary tip" type="submit"
									title="<?php echo Text::_('COM_KUNENA_SYS_BUTTON_FILTERSUBMIT'); ?>"><i
										class="icon-search"></i> <?php echo Text::_('COM_KUNENA_SYS_BUTTON_FILTERSUBMIT') ?>
							</button>
							<button class="btn btn-outline-primary tip" type="button"
									title="<?php echo Text::_('COM_KUNENA_SYS_BUTTON_FILTERRESET'); ?>"
									onclick="jQuery('.filter').val('');jQuery('#adminForm').submit();"><i
										class="icon-remove"></i> <?php echo Text::_('COM_KUNENA_SYS_BUTTON_FILTERRESET'); ?>
							</button>
						</div>
						<div class="btn-group pull-right hidden-phone">
							<label for="limit"
								   class="element-invisible"><?php echo Text::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC'); ?></label>
							<?php echo $this->pagination->getLimitBox(); ?>
						</div>
						<div class="btn-group pull-right hidden-phone">
							<label for="directionTable"
								   class="element-invisible"><?php echo Text::_('JFIELD_ORDERING_DESC'); ?></label>
							<select name="directionTable" id="directionTable" class="input-medium"
									onchange="Joomla.orderTable()">
								<option value=""><?php echo Text::_('JFIELD_ORDERING_DESC'); ?></option>
								<?php echo HTMLHelper::_('select.options', $this->sortDirectionFields, 'value', 'text', $this->list->Direction); ?>
							</select>
						</div>
						<div class="btn-group pull-right">
							<label for="sortTable"
								   class="element-invisible"><?php echo Text::_('JGLOBAL_SORT_BY'); ?></label>
							<select name="sortTable" id="sortTable" class="input-medium" onchange="Joomla.orderTable()">
								<option value=""><?php echo Text::_('JGLOBAL_SORT_BY'); ?></option>
								<?php echo HTMLHelper::_('select.options', $this->sortFields, 'value', 'text', $this->list->Ordering); ?>
							</select>
						</div>
						<div class="clearfix"></div>
					</div>

					<table class="table table-striped" id="attachmentsList">
						<thead>
						<tr>
							<th width="1%"><input type="checkbox" name="toggle" value=""
												  onclick="Joomla.checkAll(this)"/>
							</th>
							<th><?php echo HTMLHelper::_('grid.sort', 'COM_KUNENA_ATTACHMENTS_FIELD_LABEL_TITLE', 'filename', $this->list->Direction, $this->list->Ordering); ?></th>
							<th><?php echo HTMLHelper::_('grid.sort', 'COM_KUNENA_ATTACHMENTS_FIELD_LABEL_TYPE', 'filetype', $this->list->Direction, $this->list->Ordering); ?></th>
							<th><?php echo HTMLHelper::_('grid.sort', 'COM_KUNENA_ATTACHMENTS_FIELD_LABEL_SIZE', 'size', $this->list->Direction, $this->list->Ordering); ?>
							<th><?php echo Text::_('COM_KUNENA_ATTACHMENTS_FIELD_LABEL_IMAGEDIMENSIONS'); ?></th>
							<th><?php echo HTMLHelper::_('grid.sort', 'COM_KUNENA_ATTACHMENTS_USERNAME', 'username', $this->list->Direction, $this->list->Ordering); ?></th>
							<th><?php echo HTMLHelper::_('grid.sort', 'COM_KUNENA_ATTACHMENTS_FIELD_LABEL_MESSAGE', 'post', $this->list->Direction, $this->list->Ordering); ?></th>
							<th><?php echo HTMLHelper::_('grid.sort', 'JGRID_HEADING_ID', 'id', $this->list->Direction, $this->list->Ordering); ?></th>
						</tr>
						<tr>
							<td class="hidden-phone">
							</td>
							<td class="nowrap">
								<label for="filterTitle"
									   class="element-invisible"><?php echo Text::_('COM_KUNENA_FIELD_LABEL_SEARCHIN'); ?></label>
								<input class="input-block-level input-filter filter form-control" type="text"
									   name="filterTitle"
									   id="filterTitle"
									   placeholder="<?php echo Text::_('COM_KUNENA_SYS_BUTTON_FILTERSUBMIT') ?>"
									   value="<?php echo $this->filter->Title; ?>"
									   title="<?php echo Text::_('COM_KUNENA_SYS_BUTTON_FILTERSUBMIT') ?>"/>
							</td>
							<td class="nowrap">
								<label for="filterType"
									   class="element-invisible"><?php echo Text::_('COM_KUNENA_FIELD_LABEL_SEARCHIN'); ?></label>
								<input class="input-block-level input-filter filter form-control" type="text"
									   name="filterType"
									   id="filterType"
									   placeholder="<?php echo Text::_('COM_KUNENA_SYS_BUTTON_FILTERSUBMIT') ?>"
									   value="<?php echo $this->filter->Type; ?>"
									   title="<?php echo Text::_('COM_KUNENA_SYS_BUTTON_FILTERSUBMIT') ?>"/>
							</td>
							<td class="nowrap">
								<label for="filter_size"
									   class="element-invisible"><?php echo Text::_('COM_KUNENA_FIELD_LABEL_SEARCHIN'); ?></label>
								<input class="input-block-level input-filter filter form-control" type="text"
									   name="filter_size"
									   id="filter_size"
									   placeholder="<?php echo Text::_('COM_KUNENA_SYS_BUTTON_FILTERSUBMIT') ?>"
									   value="<?php echo $this->filter->Size; ?>"
									   title="<?php echo Text::_('COM_KUNENA_SYS_BUTTON_FILTERSUBMIT') ?>"/>
							</td>
							<td class="nowrap">
								<?php /*
										<label for="filter_dims" class="element-invisible"><?php echo 'Text::_('COM_KUNENA_FIELD_LABEL_SEARCHIN');;?></label>
										<input class="input-block-level input-filter filter form-control" type="text" name="filter_dims" id="filter_dims" placeholder="<?php echo Text::_('COM_KUNENA_SYS_BUTTON_FILTERSUBMIT') ?>" value="<?php echo $this->filterDimensions; ?>" title="<?php echo Text::_('COM_KUNENA_SYS_BUTTON_FILTERSUBMIT') ?>" />
									*/ ?>
							</td>
							<td class="nowrap">
								<label for="filter_username"
									   class="element-invisible"><?php echo Text::_('COM_KUNENA_FIELD_LABEL_SEARCHIN'); ?></label>
								<input class="input-block-level input-filter filter form-control" type="text"
									   name="filter_username"
									   id="filter_username"
									   placeholder="<?php echo Text::_('COM_KUNENA_SYS_BUTTON_FILTERSUBMIT') ?>"
									   value="<?php echo $this->filter->Username; ?>"
									   title="<?php echo Text::_('COM_KUNENA_SYS_BUTTON_FILTERSUBMIT') ?>"/>
							</td>
							<td class="nowrap">
								<label for="filter_post"
									   class="element-invisible"><?php echo Text::_('COM_KUNENA_FIELD_LABEL_SEARCHIN'); ?></label>
								<input class="input-block-level input-filter filter form-control" type="text"
									   name="filter_post"
									   id="filter_post"
									   placeholder="<?php echo Text::_('COM_KUNENA_SYS_BUTTON_FILTERSUBMIT') ?>"
									   value="<?php echo $this->filter->Post; ?>"
									   title="<?php echo Text::_('COM_KUNENA_SYS_BUTTON_FILTERSUBMIT') ?>"/>
							</td>
							<td class="nowrap center hidden-phone">
							</td>
						</tr>
						</thead>
						<tfoot>
						<tr>
							<td colspan="8">
								<?php echo $this->pagination->getListFooter(); ?>
							</td>
						</tr>
						</tfoot>
						<tbody>
						<?php
						$i = 0;

						if ($this->pagination->total > 0)
							:
							foreach ($this->items as $id => $attachment)
								:
								$message = $attachment->getMessage();
								?>
								<tr>
									<td><?php echo HTMLHelper::_('grid.id', $i, intval($attachment->id)) ?></td>
									<td><?php echo $attachment->getLayout()->render('thumbnail') . '<br />' . $attachment->getFilename() ?></td>
									<td><?php echo $this->escape($attachment->filetype); ?></td>
									<td><?php echo number_format(intval($attachment->size) / 1024, 0, '', ',') . ' ' . Text::_('COM_KUNENA_A_FILESIZE_KB'); ?></td>
									<td><?php echo $attachment->width > 0 ? $attachment->width . ' x ' . $attachment->height : '' ?></td>
									<td><?php echo $this->escape($message->getAuthor()->getName()); ?></td>
									<td><?php echo $this->escape($message->subject); ?></td>
									<td><?php echo intval($attachment->id); ?></td>
								</tr>
								<?php
								$i++;
							endforeach;
						else

							:
							?>
							<tr>
								<td colspan="10">
									<div class="card card-block bg-faded p-2 center filter-state">
											<span><?php echo Text::_('COM_KUNENA_FILTERACTIVE');
												?>
												<?php // <a href="#" onclick="document.getElements('.filter').set('value', '');this.form.submit();return false;"><?php echo Text::_('COM_KUNENA_FIELD_LABEL_FILTERCLEAR');</a>
												?>
												<?php if ($this->filter->Active || $this->pagination->total > 0)
													:
													?>
													<button class="btn btn-outline-primary" type="button"
															onclick="document.getElements('.filter').set('value', '');this.form.submit();"><?php echo Text::_('COM_KUNENA_FIELD_LABEL_FILTERCLEAR'); ?></button>
												<?php endif; ?>
											</span>
									</div>
								</td>
							</tr>
						<?php endif; ?>
						</tbody>
					</table>
				</form>
				<div class="clearfix"></div>
			</div>
		</div>
	</div>

	<div class="pull-right small">
		<?php echo KunenaVersion::getLongVersionHTML(); ?>
	</div>
</div>
